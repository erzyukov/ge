<h4>Редактирование меню сайта</h4>
<table class="menu_manage_table" ><tr><td>
	<div class="new-element">
	<form action="" method="post" style="height: 60px;">
	<strong><u>Выбрать меню:</u></strong>
	<select onchange="window.location = this.value;">{$menu_option}</select>
	<input class="inp" type="submit" value="Удалить" />
	<input type="hidden" name="action" value="delete_menu" />
	</form></div>
</td><td>
	<div class="new-element">
	<form action="" method="post" style="height: 60px;">
		<strong><u>Создать меню:</u></strong>
		<input type="text" name="title" />: Заголовок
		<br />
		<input type="text" name="code" />: Код <input  class="inp" type="submit" value="Создать" />
		<input type="hidden" name="action" value="add_menu" />
	</form>
	</div>
</td></tr></table>


<br />

<div class="new-element">
<form action="" method="post">
	<strong><u>Добавить элемент в текущее меню</u></strong>

	<div><span>Заголовок элемента меню:</span><input class="inp1" type="text" name="title" /></div>
	<div><span>Порядок отображения:</span><input class="inp1" type="text" name="outorder" /></div>
	<div><span>Родительский элемент:</span><select name="parent_id"><option value="0">Корневой элемент</option>{$element_option}</select></div>
	<div><span>Прямая ссылка:</span><input class="inp1" type="text" name="direct_link" /> <i>(при указании прямой ссылки, модуль выбирать не надо)</i></div>

	<div><span>Модули сайта:</span><select id="menu_select_module" name="module_id" onchange="onWmenuModuleSelect(this, 'item_select', 'module_item_list');" ><option value="0">Выберите модуль</option>{*$module_option*}</select></div>
	<div><span>Страницы модулей:</span><select name="item_id" id="item_select"><option value="0">Корень модуля</option></select></div>

	<input type="hidden" id="module_item_list" value='{$items_string}' />
	<input  class="inp" type="submit" value="Добавить" />
	<input type="hidden" name="menu_id" value="{$menu_id}" />
	<input type="hidden" name="action" value="add_element" />
</form>
 </div><br />
<div class="new-element">

{* -- массив модулей *}
<input type="hidden" id="module_list_select" value='{$modules_string}' />

{* -- форма редактирования выбранного меню *}
<form action="" method="post">
	<strong><u>Содержимое меню</u></strong>
	<p>
		<i>* Чтобы изменить меню - нажми на требуемый элемент.</i><br />
		<i>* Если изменить Модуль или Страницу, то Ссылка сбросится.</i><br />
		<i>* Если изменить Ссылку, то Модуль и Страница сбросятся.</i><br />
		<i>* Сначала на изменения проверяется Модуль, а потом Ссылка.</i>
	</p>
	<table class="list-table">
		<tr>
			<th width="130px">Заголовок меню</th>
			<th width="130px">Модуль</th>
			<th width="130px">Страница</th>
			<th width="100px">Ссылка</th>
			<th width="30px">№</th>
			<th width="50px">Удалить</th>
		</tr>
		{$element_list}
	</table>
	<input type="hidden" name="action" value="save_menu" />
	<input class="inp" type="submit" value="Сохранить" />
</form>
</div>