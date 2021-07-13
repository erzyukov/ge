<h3>Пользователи</h3>

{if count($errors)}
	<ul>
		{foreach from=$errors item=er}
		<li style="color:red;">{$er}</li>
		{/foreach}
	</ul>
{/if}

<form action="" method="post">
	<input class="inp" alt="Добавить" title="Добавить пользователя" value="" name="" type="image" />
	{literal}<input type="text" name="login" value="Логин" title="Логин" onfocus="if(this.value=='Логин'){this.value=''}" onblur="if(this.value==''){this.value='Логин'}" />{/literal}
	{literal}<input type="text" name="password" value="Пароль" title="Пароль" onfocus="if(this.value=='Пароль'){this.value=''}" onblur="if(this.value==''){this.value='Пароль'}" />{/literal}
	<input type="hidden" name="action" value="add_user" />
</form>
<br />
<form action="" method="post">
	<table class="list-table">
		<tr>
			<th width="150px">
				Логин
			</th>
			<th width="150px">
				Последнее посещение
			</th>
			<th>
				Данные
			</th>
			<th width="120px">
				<span></span>
			</th>
		</tr>
		{foreach from=$list item=r}
			<tr>
				<td>
					{$r.login}
				</td>
				<td>
					{$r.last_login}
				</td>
				<td>
					ФИО : {$r.fio}<br />
					E-mail : {$r.mail}<br />
				</td>
				<td align = "center">
					<a href="/admin/widget/user/{$r.id}/data"><img src="/rs/admin/images/user_data_edit.gif" alt="Данные" title="Редактировать данные" width="16px" />&nbsp;&nbsp;</a>
					<a href="#" onclick="deleteUserForm({$r.id});return!1;"><img src="/rs/admin/images/user_delete.png" alt="Удалить" title="Удалить пользователя" width="16px" /></a>
				</td>
			</tr>
		{/foreach}
	</table>

	<input class="inp" alt="Сохранить изменения" title="Сохранить изменения" value="" name="" type="image" />
</form>

<form id="deleteUserForm" method="POST" action="">
	<input type="hidden" name="id" value="0" />
	<input type="hidden" name="action" value="delete_user" />
</form>
