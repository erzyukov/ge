<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru">
  <head>
    <title>{$title}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link href="/rs/admin/css/login.css" rel="stylesheet" type="text/css" />
  </head>
  <body>
	<div>{$message}</div>
	<div class="login">
		<h1>Вход в панель администрирования</h1>
		<form action="/admin/login" method="post">
			<div><span>Логин:</span><input type="text" name="login" /></div>
			<div><span>Пароль:</span><input type="password" name="password" /></div>
			<input class="inp" type="submit" value="войти" />
		</form>
	</div>
	
  </body>
</html>