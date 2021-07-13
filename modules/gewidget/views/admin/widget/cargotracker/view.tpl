<h1>Редактирование последнего события</h1>
<div style="text-align: right; width: 100%;">
	<a href="/admin/widget/cargotracker/edit">Назад</a>
</div>

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
		<td><input type="text" value="{$value->number}" readonly="readonly" style="width: 100%;" /> </td>
		<td><input type="text" name="date" style="width: 100%;" value="{date('d.m.Y', $value->date)}" /></td>
		<td><input type="text" name="time" style="width: 100%;" value="{date('H:i', $value->date)}" /></td>
		<td class="ui-widget"><input type="text" id="ac_action" name="caction" style="width: 100%;" value="{$value->action}" /></td>
		<td><input type="text" name="receiver" style="width: 100%;" value="{$value->receiver}" /></td>
		<td class="ui-widget"><input type="text" id="ac_from" name="from" style="width: 100%;" value="{$value->from}" /></td>
		<td class="ui-widget"><input type="text" id="ac_to" name="to" style="width: 100%;" value="{$value->to}" /></td>
		<td>
			<input type="text" name="container" value="{$value->container}" style="width: 100%;" /><br />
			<input type="text" name="conosamet" value="{$value->conosamet}" style="width: 100%;" /><br />
			<input type="text" name="carriage" value="{$value->carriage}" style="width: 100%;" />
		</td>
	</tr>
</table>
	<input type="hidden" name="action" value="change_track" />
	<input type="submit" value="Изменить" style="width:100%" />
	<input type="hidden" name="id" value="{$value->id}" />
</form>
<ul>
	<li><i>Формат даты: дд.мм.гггг. Фомат времени: чч:мм</i></li>
	<li><i>Если дату и время оставить пустыми, то проставится текущее время.</i></li>
</ul>

<div style="text-align: right; width: 100%;">
	<form action="" method="post" onclick="return window.confirm('Вы действительно хотите удалить текущее событие?');">
		<input type="hidden" name="action" value="delete_track" />
		<input type="submit" value="Удалить" />
		<input type="hidden" name="id" value="{$value->id}" />
	</form>
</div>


<h4>Список грузов: предыдущие действия для выбранного груза</h4>
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
	{foreach from=$prev item=r}
	<tr>
		<td>{$r->number}</td>
		<td>{date('d.m.Y', $r->date)}</td>
		<td>{date('H:i', $r->date)}</td>
		<td>{$r->action}</td>
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
