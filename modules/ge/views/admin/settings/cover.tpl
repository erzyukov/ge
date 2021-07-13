<h3>Настройки сайта</h3>

<div>

<h4>смена пароля</h4>
<form method="post" action="">
	<table>
		<tr>
			<td>Текущий пароль</td>
			<td><input type="password" name="old_password" /></td>
		</tr>
		<tr>
			<td>Новый пароль</td>
			<td><input type="password" name="password" /></td>
		</tr>
		<tr>
			<td>Подтверждение</td>
			<td><input type="password" name="password_confirm" /></td>
		</tr>
	</table>
	<input type="hidden" name="action" value="change_password" />
	<input type="submit" value="сменить" />
</form>



</div>