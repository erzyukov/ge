<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru">
  <head>
    <title>{$title}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <script type="text/javascript" src="/rs/app/highslide/highslide.js"></script>
    <script type="text/javascript" src="/rs/app/highslide/highslide.cfg.js"></script>
    <script type="text/javascript" src="/rs/app/ckeditor/ckeditor.js"></script>
    <script type="text/javascript" src="/rs/app/jscal/jscal2.js"></script>
    <script type="text/javascript" src="/rs/app/jscal/lang/ru.js"></script>
    <script type="text/javascript" src="/rs/admin/js/mootools_core.js"></script>
    <script type="text/javascript" src="/rs/admin/js/main.js"></script>
    <script type="text/javascript" src="/rs/admin/js/widget.js"></script>
    <link href="/rs/app/highslide/highslide.css" rel="stylesheet" type="text/css" />
    <link href="/rs/admin/css/main.css" rel="stylesheet" type="text/css" />
    <link href="/rs/app/jscal/jscal2.css" rel="stylesheet" type="text/css" />
    <link href="/rs/app/jscal/border-radius.css" rel="stylesheet" type="text/css" />
  </head>
  <body>

<table class="cover">

	<tr>
		<td colspan="3" class="top">
			Управление сайтом 
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<select name="lang_select" onchange="window.location='/admin/default/chlang?lang_select='+this.value">
				{foreach from=$lang_list item=rec}<option value="{$rec.id}" {if $cur_lang==$rec.id}selected="true"{/if}>{$rec.title}</option>{/foreach}
			</select> 
			<a href="/admin/logout">выход</a>
		</td>
	</tr>
	<tr>
		<td colspan="3" class="menu"><a href="/admin/">Главная</a> | <a href="/admin/settings">Настройки</a> <!-- Карта сайта ! ФМ ! Настройки --> </td>
	</tr>
	<tr>
		<td class="submenu">
			<b>Модули</b>
			{$module_menu}
		</td>
		<td class="content">
			<div>{$message}</div>
			
			{$content}
		
		</td>
		<td>{$widget_menu}</td>
	</tr>
	<tr>
		<td colspan="3">info</td>
	</tr>

</table>


  </body>
</html>