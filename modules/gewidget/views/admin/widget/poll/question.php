<?php defined('SYSPATH') or die('No direct script access.'); ?>
		<tr>
			<td class="lt-td1">
				<input value="<?=$caption?>" name="caption[<?=$id?>]" type="text" />
			</td>
			<td>
				<a title="Редактировать список ответов" href="<?=$_list_href?>">
					<img alt="Редактировать содержимое" title="Редактировать содержимое" src="/rs/admin/images/ic1.gif" />
				</a>
			</td>
			<td>
				<span><input name="active_list[<?=$id?>]" value="1" type="checkbox" <?=$_checked?> /></span>
				<span><input name="delete_list[<?=$id?>]" value="1" type="checkbox" /></span>
			</td>
		</tr>