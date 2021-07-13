<h4>Редактор баннеров</h4>
<div align="right"><a href="#" target="_blank">Посмотреть расположение баннеров</a></div>


<div class="new-element">
	<h4>Добавить баннер:</h4>
	<form action="" method="post">
		Название: <input value="" name="caption" type="text" /> &nbsp; &nbsp;
		<input class="inp" value="Добавить" type="submit" />
		<input name="action" value="add_banner" type="hidden">
	</form>
</div>


<h4>Список банеров:</h4>
<form action="" method="post" name="UpdateList">
	<table class="list-table">
		<tr>
			<th class="lt-td3">&nbsp;</th>
			<th class="lt-td1">&nbsp;</th>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
			<th class="lt-td2">
				<img class = "header_title_icon" alt="Активная позиция" title="Активная позиция" src="/rs/admin/images/active.png" />
			</th>
			<th class="lt-td2">
				<img class = "header_title_icon" alt="Удалить позицию" title="Удалить позицию" src="/rs/admin/images/delete.png" />
			</th>
		</tr>
		<tr>
			<th class="lt-td3">Расположение</th>
			<th class="lt-td1">Название</th>
			<th><span title="Число показов баннера">П</span>(<span title="Всего">В</span>)</th>
			<th><span title="Число кликов баннера">К</span>(<span title="Всего">В</span>)</th>
			<th></th>
			<th class="lt-td2">
				<span><input class = "a" title="Выделить все" type="checkbox" /></span>
			</th>
			<th class="lt-td2">
				<span><input class = "d" title="Выделить все" type="checkbox" /></span>
			</th>
		</tr>

		<!-- список елементов -->
		{foreach from=$list item=r}
			<tr>
				<td class="lt-td3">
					<input value="{$r.position}" name="position_list[{$r.id}]" type="text" />
				</td>
				<td class="lt-td1">
					<center><input value="{$r.caption}" name="title_list[{$r.id}]" type="text" /></center>
				</td>
				<td>
					<b>{$r._show}</b>&nbsp;({$r.max_show})
				</td>
				<td>
					<b>{$r._click}</b>&nbsp;({$r.max_click})
				</td>
				<td>
					<a title="Редактировать содержимое" href="{$r._edit_href}">
						<img alt="Редактировать содержимое" title="Редактировать содержимое" src="/rs/admin/images/ic1.gif" />
					</a>
				</td>
				<td {if $r._checked}class = "active"{/if}>
					<span><input class = "simple a" name="active_list[{$r.id}]" value="1" type="checkbox" {$r._checked} /></span>
				</td>
				<td>
					<span><input class = "simple d" name="delete_list[{$r.id}]" value="1" type="checkbox" /></span>
				</td>
			</tr>
		{/foreach}

	</table>

	<input class="inp" alt="Изменить данные" title="Изменить данные" value="" name="" type="image" />
	<input name="action" value="edit_list" type="hidden" />
</form>