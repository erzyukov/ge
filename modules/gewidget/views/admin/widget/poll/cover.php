<?php defined('SYSPATH') or die('No direct script access.'); ?>
<h4>Редактор опросов</h4>


<div class="new-element">
	<h4>Добавить опрос:</h4>
	<form action="" method="post">
		Название: <input value="" name="caption" type="text" /> &nbsp; &nbsp;
		<input class="inp" value="Добавить" type="submit" />
		<input name="action" value="add_question" type="hidden">
	</form>
</div>


<h4>Список опросов:</h4>
<form action="" method="post">
	<table class="list-table">
		<tr>
			<th class="lt-td1">Название</th>
			<th class="lt-td2">Действие</th>
			<th class="lt-td2">
				<div>
					<span><input title="Выделить все" type="checkbox" /></span>
					<span><input title="Выделить все" type="checkbox" /></span>
				</div>
			</th>
		</tr>

		<!-- список елементов -->
		<?=$list?>

	</table>

	<input class="inp" alt="Изменить данные" title="Изменить данные" value="" name="" type="image" />
	<input name="action" value="edit_question" type="hidden" />
</form>