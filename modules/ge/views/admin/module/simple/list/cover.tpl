
<div class="new-element">
	<h4>Добавить элемент в раздел</h4>
	<form action="" method="post">
		<span>Название: </span> <input value="" name="{$caption_field}" type="text" /> &nbsp; &nbsp;
		<span>Порядок: </span> <input value="{$auto_outorder}" name="outorder" type="text" /> &nbsp; &nbsp;
		<input value="Добавить" class="inp" type="submit" />
		<input name="action" value="add_item" type="hidden">
		{if $module_parent_id}<input name="parent_id" value="{$module_parent_id}" type="hidden">{/if}
	</form>
</div>



<h4>Список элементов раздела</h4>
<form action="" method="post" name="UpdateList">
	<table class="list-table">
		<tr>
			<th class="lt-td3">&nbsp;</th>
			<th class="lt-td1">&nbsp;</th>
			<th>&nbsp;</th>
			<th class="lt-td2">
				<img class = "header_title_icon" alt="Активная позиция" title="Активная позиция" src="/rs/admin/images/active.png" />
			</th>
			<th class="lt-td2">
				<img class = "header_title_icon" alt="Удалить позицию" title="Удалить позицию" src="/rs/admin/images/delete.png" />
			</th>
		</tr>
		<tr>
			<th class="lt-td3">№</th>
			<th class="lt-td1">Название</th>
			<th>Действие</th>
			<th class="lt-td2">
				<span><input class = "a" title="Выделить все" type="checkbox" /></span>
			</th>
			<th class="lt-td2">
				<span><input class = "d" title="Выделить все" type="checkbox" /></span>
			</th>
		</tr>

		<!-- список елементов -->
		{foreach from=$list item=rec}
			<tr>
				<td class="lt-td3">
					<input value="{$rec.outorder}" name="outorder_list[{$rec.id}]" type="text" />
				</td>
				<td class="lt-td1">
					<center><input value="{$rec._caption}" name="title_list[{$rec.id}]" type="text" /></center>
				</td>
				<td>
					<a title="Редактировать содержимое" href="{$rec._edit_href}">
						<img alt="Редактировать содержимое" title="Редактировать содержимое" src="/rs/admin/images/ic1.gif" />
					</a> &nbsp;
					{if isset($rec['_sub_href'])}
					<a title="Список подразделов" href="{$rec._sub_href}">
						<img alt="Список подразделов" title="Список подразделов" src="/rs/admin/images/ic3.gif" />
					</a> &nbsp;
					{/if}
				</td>
				<td {if $rec._checked}class = "active"{/if}>
					<span><input class = "simple a" name="active_list[{$rec.id}]" value="1" type="checkbox" {$rec._checked} /></span>
				</td>
				<td>
					<span><input class = "simple d" name="delete_list[{$rec.id}]" value="1" type="checkbox" /></span>
				</td>
			</tr>
		{/foreach}

	</table>

	<input class="inp" alt="Изменить данные" title="Изменить данные" value="" name="" type="image" />
	<input class="inp" alt="Сбросить данные" title="Сбросить данные" value="" name="" onclick="document.UpdateList.reset(); return!1;" type="image" />
	<input name="action" value="edit_list" type="hidden" />
	{if $module_parent_id}<input name="parent_id" value="{$module_parent_id}" type="hidden">{/if}
</form>