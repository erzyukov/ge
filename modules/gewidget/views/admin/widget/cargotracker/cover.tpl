<h1>Отслеживание грузов</h1>
<div style="text-align: right; width: 100%;">
	<a href="/admin/widget/cargotracker/action">Справочник Событий</a> &nbsp;&nbsp;|&nbsp;&nbsp;
	<a href="/admin/widget/cargotracker/destination">Справочник Назначений</a>
</div>
<h4>Добавить событие</h4>
<form action="" method="post" onsubmit="return onCargoAddSubmit(this);">
<table width="100%">
	<tr>
		<th width="80px">Номер</th>
		<th width="50px">Дата</th>
		<th width="30px">Время</th>
		<th>Событие</th>
		<th width="90px">Получатель</th>
		<th width="90px">Откуда</th>
		<th width="90px">Куда</th>
		<th width="90px">
			Тип конт<br />
			Коносамент<br />
			№ вагона
		</th>
	</tr>
	<tr>
		<td><input type="text" name="number" style="width: 100%;" /> </td>
		<td><input type="text" name="date" style="width: 100%;" /></td>
		<td><input type="text" name="time" style="width: 100%;" /></td>
		<td class="ui-widget"><input type="text" id="ac_action" name="caction" style="width: 100%;" /></td>
		<td><input type="text" name="receiver" style="width: 100%;" /></td>
		<td class="ui-widget"><input type="text" id="ac_from" name="from" style="width: 100%;" /></td>
		<td class="ui-widget"><input type="text" id="ac_to" name="to" style="width: 100%;" /></td>
		<td>
			<input type="text" name="container" style="width: 100%;" /><br />
			<input type="text" name="conosamet" style="width: 100%;" /><br />
			<input type="text" name="carriage" style="width: 100%;" />
		</td>
	</tr>
</table>
	<input type="hidden" name="action" value="add_track" />
	<input type="submit" value="Добавить" style="width:100%" />
</form>
<ul>
	<li><i>Формат даты: дд.мм.гггг. Фомат времени: чч:мм</i></li>
	<li><i>Если дату и время оставить пустыми, то проставится текущее время.</i></li>
</ul>

<div style="text-align: right; width: 100%;">
	<form action="" method="post">
		{if $search_error==1}<span style="color:red;"><b>Груза с таким номером не найдено!</b></span>{/if}
		<input type="text" name="search" />
		<input type="hidden" name="action" value="search" />
		<input type="submit" value="Найти" />
	</form>
</div>

<h4>Список грузов: последние действия</h4>
<table width="100%">
	<tr>
		<th width="80px">Номер</th>
		<th width="50px">Дата</th>
		<th width="30px">Время</th>
		<th>Событие</th>
		<th width="90px">Получатель</th>
		<th width="90px">Откуда</th>
		<th width="90px">Куда</th>
		<th width="90px">
			Тип конт<br />
			Коносамент<br />
			№ вагона
		</th>
	</tr>
	{foreach from=$list item=r}
	<tr>
		<td><a href="/admin/widget/cargotracker/{$r->id}/view">{$r->number}</a></td>
		<td>{date('d.m.Y', $r->date)}</td>
		<td>{date('H:i', $r->date)}</td>
		<td><a href="/admin/widget/cargotracker/{$r->id}/view">{$r->action}</a></td>
		<td>{$r->receiver}</td>
		<td>{$r->from}</td>
		<td>{$r->to}</td>
		<td>
			{$r->container}<br />
			{$r->conosamet}<br />
			{$r->carriage}
		</td>
	</tr>
	{/foreach}
</table>

<input type="hidden" id="ac_value_action" value='{$action}' />
<input type="hidden" id="ac_value_dest" value='{$dest}' />
