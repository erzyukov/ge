<h1>Редактирование данных пользователя: <u>{$login}</u></h1>
<div style="text-align: right; width: 100%;">
	<a href="/admin/widget/cargotracker/edit">Назад</a>
</div>

{if count($errors)}
	<ul>
		{foreach from=$errors item=er}
		<li style="color:red;">{$er}</li>
		{/foreach}
	</ul>
{/if}

<form method="post" action="" enctype="multipart/form-data">
<table style="border: 0px;"><tr><td style="border: 0px; width: 400px;" class="container-td">
	<table class="user-table" style="width: 100%;">
		{foreach from=$conf item=r key=k}
			<tr>
				<td>{$r.title}</td>
				<td>{$field[$k]}</td>
			</tr>
		{/foreach}
	</table>
</td><td style="border: 0px;" class="container-td">
	<table class="user-table">
		<tr>
			<td>Сменить логин</td>
			<td><input type="text" name="login" value="{$login}" /></td>
		</tr>
		<tr>
			<td>Сменить E-mail</td>
			<td><input type="text" name="mail" value="{$mail}" /></td>
		</tr>
		<tr>
			<td>Сменить пароль</td>
			<td><input type="text" name="password" value="" /></td>
		</tr>
	</table>
</td></tr></table>

	<input type="hidden" name="action" value="save_user" />
	<input type="submit" value="Сохранить" />
</form>

<h3>Привязать груз к пользователю</h3>
<form action="" method="post">

	Номер: <input type="text" name="number" value="" />
	<input type="submit" value="Привязать" />
	<input type="hidden" name="action" value="bind_cargo" />
	
</form>

<h3>Список грузов пользователя:</h3>
<form action="" method="post">
	<table width="100%">
		<tr>
			<th width="80px">Номер</th>
			<th width="50px">Дата</th>
			<th width="30px">Время</th>
			<th>Последнее событие</th>
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
</form>






