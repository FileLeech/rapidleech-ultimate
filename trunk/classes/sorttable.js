/*
  Based on SortTable version 2, 7th April 2007
  http://www.kryogenix.org/code/browser/sorttable/
*/
 
var stIsIE = /*@cc_on!@*/false;

sorttable = {
  init: function() {
    // quit if this function has already been called
    if (arguments.callee.done) { return; }
    // flag this function so we don't do the same thing twice
    arguments.callee.done = true;
    // kill the timer
    if (_timer) { clearInterval(_timer); }
    
    if (!document.createElement || !document.getElementsByTagName) { return; }
    
    sorttable.DATE_RE = /^(\d\d?)[\/\.-](\d\d?)[\/\.-]((\d\d)?\d\d)$/;
    
    forEach(document.getElementsByTagName('table'), function(table) {
      if (table.className.search(/\bsortable\b/) != -1) {
        sorttable.makeSortable(table);
      }
    });
    
  },
  
  makeSortable: function(table) {
    if (table.getElementsByTagName('thead').length === 0) {
      // table doesn't have a tHead. Since it should have, create one and
      // put the first table row in it.
      the = document.createElement('thead');
      the.appendChild(table.rows[0]);
      table.insertBefore(the,table.firstChild);
    }
    // Safari doesn't support table.tHead, sigh
    if (table.tHead === null) { table.tHead = table.getElementsByTagName('thead')[0]; }
    
    if (table.tHead.rows.length != 1) { return; } // can't cope with two header rows
    
    // Sorttable v1 put rows with a class of "sortbottom" at the bottom (as
    // "total" rows, for example). This is B&R, since what you're supposed
    // to do is put them in a tfoot. So, if there are sortbottom rows,
    // for backwards compatibility, move them to tfoot (creating it if needed).
    sortbottomrows = [];
    var i;
    for (i=0; i<table.rows.length; i++) {
      if (table.rows[i].className.search(/\bsortbottom\b/) != -1) {
        sortbottomrows[sortbottomrows.length] = table.rows[i];
      }
    }
    if (sortbottomrows) {
      if (table.tFoot === null) {
        // table doesn't have a tfoot. Create one.
        tfo = document.createElement('tfoot');
        table.appendChild(tfo);
      }
      for (i=0; i<sortbottomrows.length; i++) {
        tfo.appendChild(sortbottomrows[i]);
      }
      sortbottomrows = [];
    }
    
    // work through each column and calculate its type
    headrow = table.tHead.rows[0].cells;
    for (i=0; i<headrow.length; i++) {
      // manually override the type with a sorttable_type attribute
      if (!headrow[i].className.match(/\bsorttable_nosort\b/)) { // skip this col
        mtch = headrow[i].className.match(/\bsorttable_([a-z0-9]+)\b/);
        if (mtch) { override = mtch[1]; }
        if (mtch && typeof sorttable["sort_"+override] == 'function') {
          headrow[i].sorttable_sortfunction = sorttable["sort_"+override];
        } else {
          headrow[i].sorttable_sortfunction = sorttable.guessType(table,i);
        }
        // make it clickable to sort
        headrow[i].sorttable_columnindex = i;
        headrow[i].sorttable_tbody = table.tBodies[0];
        $(headrow[i]).click(function(e) {

          var checkbox_reverse = false;
          if (this.className.search(/\bsorttable_checkbox\b/) != -1) {
            checkbox_reverse = (this.className.search(/\bsorttable_sorted\b/) != -1) ? true : false;
          }
          else {//regular procedure: sorttable_checkbox NOT found
            if (this.className.search(/\bsorttable_sorted\b/) != -1) {
              // if we're already sorted by this column, just 
              // reverse the table, which is quicker
              sorttable.reverse(this.sorttable_tbody);
              this.className = this.className.replace('sorttable_sorted',
                                                      'sorttable_sorted_reverse');
              this.removeChild(document.getElementById('sorttable_sortfwdind'));
              sortrevind = document.createElement('span');
              sortrevind.id = "sorttable_sortrevind";
              sortrevind.innerHTML = stIsIE ? '&nbsp<font face="webdings">6</font>' : '&nbsp;&#x25BE;';
              this.appendChild(sortrevind);
              return;
            }
            if (this.className.search(/\bsorttable_sorted_reverse\b/) != -1) {
              // if we're already sorted by this column in reverse, just 
              // re-reverse the table, which is quicker
              sorttable.reverse(this.sorttable_tbody);
              this.className = this.className.replace('sorttable_sorted_reverse',
                                                      'sorttable_sorted');
              this.removeChild(document.getElementById('sorttable_sortrevind'));
              sortfwdind = document.createElement('span');
              sortfwdind.id = "sorttable_sortfwdind";
              sortfwdind.innerHTML = stIsIE ? '&nbsp<font face="webdings">5</font>' : '&nbsp;&#x25B4;';
              this.appendChild(sortfwdind);
              return;
            }
          }

          // remove sorttable_sorted classes
          theadrow = this.parentNode;
          forEach(theadrow.childNodes, function(cell) {
            if (cell.nodeType == 1) { // an element
              cell.className = cell.className.replace('sorttable_sorted_reverse','');
              cell.className = cell.className.replace('sorttable_sorted','');
            }
          });
          sortfwdind = document.getElementById('sorttable_sortfwdind');
          if (sortfwdind) { sortfwdind.parentNode.removeChild(sortfwdind); }
          sortrevind = document.getElementById('sorttable_sortrevind');
          if (sortrevind) { sortrevind.parentNode.removeChild(sortrevind); }
          
          this.className += ' sorttable_sorted';
          sortfwdind = document.createElement('span');
          sortfwdind.id = "sorttable_sortfwdind";
          sortfwdind.innerHTML = stIsIE ? '&nbsp<font face="webdings">5</font>' : '&nbsp;&#x25B4;';
          this.appendChild(sortfwdind);

          // build an array to sort. This is a Schwartzian transform thing,
          // i.e., we "decorate" each row with the actual sort key,
          // sort based on the sort keys, and then put the rows back in order
          // which is a lot faster because you only do getInnerText once per row
          row_array = [];
          var j;
          col = this.sorttable_columnindex;
          rows = this.sorttable_tbody.rows;
          for (j=0; j<rows.length; j++) {
            row_array[row_array.length] = [sorttable.getInnerText(rows[j].cells[col]), rows[j]];
          }
          /* If you want a stable sort, uncomment the following line */
          //sorttable.shaker_sort(row_array, this.sorttable_sortfunction);
          /* and comment out this one */
          row_array.sort(this.sorttable_sortfunction);

          tb = this.sorttable_tbody;
          for (j=0; j<row_array.length; j++) {
            tb.appendChild(row_array[j][1]);
          }
          row_array = [];
          if (checkbox_reverse) { 
            sorttable.reverse(this.sorttable_tbody);
            this.className = this.className.replace('sorttable_sorted', 'sorttable_sorted_reverse');
            this.removeChild(document.getElementById('sorttable_sortfwdind'));
            sortrevind = document.createElement('span');
            sortrevind.id = "sorttable_sortrevind";
            sortrevind.innerHTML = stIsIE ? '&nbsp<font face="webdings">6</font>' : '&nbsp;&#x25BE;';
            this.appendChild(sortrevind);
          }         
        });
      }
    }
  },
  
  guessType: function(table, column) {
    // guess the type of a column based on its first non-blank row
    sortfn = sorttable.sort_alpha;
    for (var i=0; i<table.tBodies[0].rows.length; i++) {
      text = sorttable.getInnerText(table.tBodies[0].rows[i].cells[column]);
      if (text != '') {
        if (text.match(/^\d{1,3}\.?\d{1,2} [KMGT]?B$/)) {
          return sorttable.sort_filesize;
        }
        if (text.match(/^\d+\.\d+\.\d+ \d+\:\d+\:\d+$/)) {
          return sorttable.sort_rldate;
        }
        if (text.match(/^-?[�$�]?[\d,.]+%?$/)) {
          return sorttable.sort_numeric;
        }
        // check for a date: dd/mm/yyyy or dd/mm/yy 
        // can have / or . or - as separator
        // can be mm/dd as well
        possdate = text.match(sorttable.DATE_RE);
        if (possdate) {
          // looks like a date
          first = parseInt(possdate[1], 10);
          second = parseInt(possdate[2], 10);
          if (first > 12) {
            // definitely dd/mm
            return sorttable.sort_ddmm;
          } else if (second > 12) {
            return sorttable.sort_mmdd;
          } else {
            // looks like a date, but we can't tell which, so assume
            // that it's dd/mm (English imperialism!) and keep looking
            sortfn = sorttable.sort_ddmm;
          }
        }
      }
    }
    return sortfn;
  },
  
  getInnerText: function(node) {
    // gets the text we want to use for sorting for a cell.
    // strips leading and trailing whitespace.
    // this is *not* a generic getInnerText function; it's special to sorttable.
    // for example, you can override the cell text with a customkey attribute.
    // it also gets .value for <input> fields.
    
    hasInputs = (typeof node.getElementsByTagName == 'function') &&
                 node.getElementsByTagName('input').length;
    
    if (node.getAttribute("sorttable_customkey") !== null) {
      return node.getAttribute("sorttable_customkey");
    }
    else if (typeof node.textContent != 'undefined' && !hasInputs) {
      return node.textContent.replace(/^\s+|\s+$/g, '');
    }
    else if (typeof node.innerText != 'undefined' && !hasInputs) {
      return node.innerText.replace(/^\s+|\s+$/g, '');
    }
    else if (typeof node.text != 'undefined' && !hasInputs) {
      return node.text.replace(/^\s+|\s+$/g, '');
    }
    else {
      switch (node.nodeType) {
        case 3:
          if (node.nodeName.toLowerCase() == 'input') {
            return node.value.replace(/^\s+|\s+$/g, '');
          }
          break;
        case 4:
          return node.nodeValue.replace(/^\s+|\s+$/g, '');
          break;
        case 1:
        case 11:
          if (node.firstChild.nodeName.toLowerCase() == 'input' && node.firstChild.type.toLowerCase() == 'checkbox') {
            return 'checkbox'+(node.firstChild.checked?1:0);
          }
          var innerText = '';
          for (var i = 0; i < node.childNodes.length; i++) {
            innerText += sorttable.getInnerText(node.childNodes[i]);
          }
          return innerText.replace(/^\s+|\s+$/g, '');
          break;
        default:
          return '';
      }
    }
  },
  
  reverse: function(tbody) {
    // reverse the rows in a tbody
    newrows = [];
    var i;
    for (i=0; i<tbody.rows.length; i++) {
      newrows[newrows.length] = tbody.rows[i];
    }
    for (i=newrows.length-1; i>=0; i--) {
       tbody.appendChild(newrows[i]);
    }
    newrows = [];
  },
  
  /* sort functions
     each sort function takes two parameters, a and b
     you are comparing a[0] and b[0] */
  sort_checkbox: function(a,b) {
    var as = a[0].replace('checkbox','');
    var bs = b[0].replace('checkbox','');
    return (parseFloat(as) - parseFloat(bs));
  },
  sort_rldate: function(a,b) {
    var as = a[0].split(/[\.\: ]/);
    var bs = b[0].split(/[\.\: ]/);
    as = as[2]+as[1]+as[0]+as[3]+as[4]+as[5];
    bs = bs[2]+bs[1]+bs[0]+bs[3]+bs[4]+bs[5];
    return (parseFloat(as) - parseFloat(bs));
  },
  sort_filesize: function(a,b) {
    var am = a[0].split(" "); am = am[1];
    var bm = b[0].split(" "); bm = bm[1];
    var aa = parseFloat(a[0]); if (isNaN(aa)) { aa = 0; }
    var bb = parseFloat(b[0]); if (isNaN(bb)) { bb = 0; }
    if (am == bm) { return aa - bb; }
    if (am == 'B') { return -1; }
    if (bm == 'B') { return 1; }
    if (am == 'KB') { return -1; }
    if (bm == 'KB') { return 1; }
    if (am == 'MB') { return -1; }
    if (bm == 'MB') { return 1; }
  },
  sort_numeric: function(a,b) {
    aa = parseFloat(a[0].replace(/[^0-9.-]/g,''));
    if (isNaN(aa)) { aa = 0; }
    bb = parseFloat(b[0].replace(/[^0-9.-]/g,'')); 
    if (isNaN(bb)) { bb = 0; }
    return aa-bb;
  },
  sort_alpha: function(a,b) {
    if (a[0].toLowerCase()==b[0].toLowerCase()) { return 0; }
    if (a[0].toLowerCase()<b[0].toLowerCase()) { return -1; }
    return 1;
  },
  sort_ddmm: function(a,b) {
    mtch = a[0].match(sorttable.DATE_RE);
    y = mtch[3]; m = mtch[2]; d = mtch[1];
    if (m.length == 1) { m = '0'+m; }
    if (d.length == 1) { d = '0'+d; }
    dt1 = y+m+d;
    mtch = b[0].match(sorttable.DATE_RE);
    y = mtch[3]; m = mtch[2]; d = mtch[1];
    if (m.length == 1) { m = '0'+m; }
    if (d.length == 1) { d = '0'+d; }
    dt2 = y+m+d;
    if (dt1==dt2) { return 0; }
    if (dt1<dt2) { return -1; }
    return 1;
  },
  sort_mmdd: function(a,b) {
    mtch = a[0].match(sorttable.DATE_RE);
    y = mtch[3]; d = mtch[2]; m = mtch[1];
    if (m.length == 1) { m = '0'+m; }
    if (d.length == 1) { d = '0'+d; }
    dt1 = y+m+d;
    mtch = b[0].match(sorttable.DATE_RE);
    y = mtch[3]; d = mtch[2]; m = mtch[1];
    if (m.length == 1) { m = '0'+m; }
    if (d.length == 1) { d = '0'+d; }
    dt2 = y+m+d;
    if (dt1==dt2) { return 0; }
    if (dt1<dt2) { return -1; }
    return 1;
  },
  
  shaker_sort: function(list, comp_func) {
    // A stable sort function to allow multi-level sorting of data
    // see: http://en.wikipedia.org/wiki/Cocktail_sort
    // thanks to Joseph Nahmias
    var b = 0;
    var t = list.length - 1;
    var swap = true;

    while(swap) {
        swap = false;
        var i, q;
        for(i = b; i < t; ++i) {
            if ( comp_func(list[i], list[i+1]) > 0 ) {
                q = list[i]; list[i] = list[i+1]; list[i+1] = q;
                swap = true;
            }
        } // for
        t--;

        if (!swap) { break; }

        for(i = t; i > b; --i) {
            if ( comp_func(list[i], list[i-1]) < 0 ) {
                var q = list[i]; list[i] = list[i-1]; list[i-1] = q;
                swap = true;
            }
        } // for
        b++;
    } // while(swap)
  }  
};

/* ******************************************************************
   Supporting functions: bundled here to avoid depending on a library
   ****************************************************************** */

// Dean's forEach: http://dean.edwards.name/base/forEach.js
/*
	forEach, version 1.0
	Copyright 2006, Dean Edwards
	License: http://www.opensource.org/licenses/mit-license.php
*/

// array-like enumeration
if (!Array.forEach) { // mozilla already supports this
	Array.forEach = function(array, block, context) {
		for (var i = 0; i < array.length; i++) {
			block.call(context, array[i], i, array);
		}
	};
}

// generic enumeration
Function.prototype.forEach = function(object, block, context) {
	for (var key in object) {
		if (typeof this.prototype[key] == "undefined") {
			block.call(context, object[key], key, object);
		}
	}
};

// character enumeration
String.forEach = function(string, block, context) {
	Array.forEach(string.split(""), function(chr, index) {
		block.call(context, chr, index, string);
	});
};

// globally resolve forEach enumeration
var forEach = function(object, block, context) {
	if (object) {
		var resolve = Object; // default
		if (object instanceof Function) {
			// functions have a "length" property
			resolve = Function;
		} else if (object.forEach instanceof Function) {
			// the object implements a custom forEach method so use that
			object.forEach(block, context);
			return;
		} else if (typeof object == "string") {
			// the object is a string
			resolve = String;
		} else if (typeof object.length == "number") {
			// the object is array-like
			resolve = Array;
		}
		resolve.forEach(object, block, context);
	}
};

