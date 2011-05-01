<?php
function rl_rename() {
	global $list, $PHP_SELF;
?>
<form method="post" action="<?php echo $PHP_SELF; ?>"><input type="hidden" name="act" value="rename_go" />
		<table align="center" style="text-align: left;">
			<tr>
				<td>
				<table>
<?php
	for($i = 0; $i < count ( $_GET ["files"] ); $i ++) {
		$file = $list [$_GET ["files"] [$i]];
?>
<tr>
	<td align="center"><input type="hidden" name="files[]" value="<?php echo $_GET ["files"] [$i]; ?>" /><b><?php echo basename ( $file ["name"] ); ?></b></td>
</tr>
<tr>
	<td><?php echo lang(201); ?>:&nbsp;<input type="text" name="newName[]" size="25"
		value="<?php echo basename ( $file ["name"] ); ?>" /></td>
</tr>
<tr>
	<td>&nbsp;</td>
</tr>
<?php
	}
?>
                                  </table>
				</td>
				<td><input type="submit" value="Rename" /></td>
			</tr>
			<tr>
				<td></td>
			</tr>
		</table>
		</form>
<?php
}

function rename_go() {
	global $list, $options;
	$smthExists = FALSE;
	for($i = 0; $i < count ( $_POST ["files"] ); $i ++) {
		$file = $list [$_POST ["files"] [$i]];
		
		if (file_exists ( $file ["name"] )) {
			$smthExists = TRUE;
			$newName = dirname ( $file ["name"] ) . PATH_SPLITTER . stripslashes(basename($_POST["newName"][$i]));
			$filetype = strrchr ( $newName, "." );
			
			if (is_array ( $options['forbidden_filetypes'] ) && in_array ( strtolower ( $filetype ), $options['forbidden_filetypes'] )) {
				printf(lang(82),$filetype);
				echo "<br /><br />";
			} else {
				if (@rename ( $file ["name"], $newName )) {
					printf(lang(194),$file['name'],basename($newName));
					echo "<br /><br />";
					$list [$_POST ["files"] [$i]] ["name"] = $newName;
				} else {
					printf(lang(202),$file['name']);
					echo "<br /><br />";
				}
			}
		} else {
			printf(lang(145),$file['name']);
			echo "<br /><br />";
		}
	}
	if ($smthExists) {
		if (! updateListInFile ( $list )) {
			echo lang(9)."<br /><br />";
		}
	}
}
?>