<?php defined('SYSPATH') or die('No direct script access.'); ?>
		<tr>
			<td class="lt-td1">
				<input value="<?=$caption?>" name="caption[<?=$id?>]" type="text" />
			</td>
			<td>
				<center><?=$result?></center>
			</td>
			<td>
				<center><span><input name="delete_list[<?=$id?>]" value="1" type="checkbox" /></span></center>
			</td>
		</tr>