<h3>Редактирование абонемента пользователя: <u>{$login}</u></h3>
<form action="" method="post">
	<h4>Дата окончания абонемента: </h4>
	<input class="input_date" id="cal_expire" type="text" name="expire" value="{$expire}" readonly="readonly" />
	<a href="#" onclick="return!1;"><img id="cal_expire_trigger" src="/rs/admin/images/get_date.jpg" alt="выбрать" title="Выбрать дату" height="16px" /></a>
	<br />
	<h4>Дни посещения спортзала:</h4>
	<table class="table">
		<tr>
		{foreach from=$day item=r}
			<th width="30px">{$r}</th>
		{/foreach}
		</tr>
		<tr>
		{foreach from=$day key=k item=r}
			<th><input type="checkbox" name="day[{$k}]" {if isset($day_value[$k])}checked="true"{/if} /></th>
		{/foreach}
		</tr>
	
	</table>
	<br />
	<input type="hidden" name="action" value="save_ticket" />
	<input class="inp" type="submit" value="Сохранить" />
</form>