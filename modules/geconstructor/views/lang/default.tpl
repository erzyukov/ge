<h3>Управление языками сайта</h3>

<i>Внимание: При удалении языка, удаляется весь контент! Язык по умолчанию удалить нельзя!</i>

<div>
	<h4>Выбрать язык: </h4>
	<form action="/constructor/process/delete_lang" method="post">
		<select name="short" onchange="window.location='?lang='+this.value;">{foreach from=$list item=item}<option value="{$item.uri}" {if $cur.id==$item.id}selected="true"{/if}>{$item.title}</option>{/foreach}</select>
		<input class="inp" type="image" value="Удалить" />
	</form>
</div>

<br /><br />
<h4>Изменить параметры выбранного языка</h4>
<div>
	<form action="/constructor/process/edit_lang" method="post">
		<table>
			<tr>
				<th>Название языка</th>
				<td><input type="text" name="title" value="{$cur.title}" maxlength="50" tabindex="2" /></td>
			</tr>
			<tr>
				<th>Короткое название</th>
				<td><input type="text" name="short" value="{$cur.short}" maxlength="10" size="15" tabindex="3" /></td>
			</tr>
			<tr>
				<th>URI</th>
				<td>{$cur.uri}</td>
			</tr>
			<tr>
				<th>Формат даты</th>
				<td><input type="text" name="date_format" value="{$cur.date_format}" maxlength="100" tabindex="5" /></td>
			</tr>
			<tr>
				<td colspan="2" style="text-align: right;"><input class="inp" type="image" value="Изменить"  /></td>
			</tr>
		</table>
		<input type="hidden" name="id" value="{$cur.id}" />
	</form>
</div>

<br /><br />

<h4>Добавить язык</h4>
<form action="/constructor/process/add_lang" method="post">
	<table>
		<tr>
			<th>Название языка</th>
			<th>Короткое название</th>
			<th>URI</th>
			<th>Формат даты</th>
			<td rowspan="2" style="vertical-align: bottom;"><input class="inp" type="image" value="добавить" /></td>
		</tr>
		<tr>
			<td><input type="text" name="title" maxlength="50" tabindex="6" /></td>
			<td><input type="text" name="short" maxlength="10" size="15" tabindex="7" /></td>
			<td><input type="text" name="uri" maxlength="5" size="5" tabindex="8" /></td>
			<td><input type="text" name="date_format" maxlength="100" tabindex="9" /></td>
		</tr>
	</table>
</form>