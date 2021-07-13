<h4>Установленные таблицы:</h4>
<table>
	<tr>
		<td>Системные ({count($sys_tables)})</td>
		<td>Модули ({count($mod_tables)})</td>
		<td>Виджеты (0)</td>
		<td>Другие (0)</td>
	</tr>
	<tr>
		<td>
			<form id="sys_table_list_form" action="/constructor/process/" method="post">
				<table class="install_table_list">
					{foreach from=$sys_tables item=item}
						<tr>
							<td>{$item}</td>
							<td><input type="checkbox" name="table[{$item}]" /></td>
						</tr>
					{/foreach}
				</table>
			</form>
		</td>
		<td>
			<form id="mod_table_list_form" action="/constructor/process/" method="post">
				<table class="install_table_list">
					{foreach from=$mod_tables item=item}
						<tr>
							<td>{$item}</td>
							<td><input type="checkbox" name="table[{$item}]" /></td>
						</tr>
					{/foreach}
				</table>
			</form>
		</td>
		<td></td>
		<td></td>
	</tr>
	<tr>
		<td>
			<center>
				<form onsubmit="sys_table_action(this); return!1;">
					<select name="action">
						<option value="">Действие</option>
						<option value="create_sys">Сгенерировать</option>
						<option value="empty_sys">Очистить выделенные</option>
						<option value="delete_sys">Удалить выделенные</option>
					</select><br /><br />
					<input class="inp" type="image" value="Выполнить" />
				</form>
				<!--<form method="post" action="/constructor/process/create_sys" onsubmit="">
					<input class="inp" type="image" value="Сформировать" />
				</form><br />
				<input class="inp" type="image" value="Очистить таблицы" /><br /><br />
				<input class="inp" type="image" value="Удалить таблицы" />
			--></center>
		</td>
		<td>
			<center>
				<input class="inp" type="image" value="Очистить таблицы" /><br /><br />
				<input class="inp" type="image" value="Удалить таблицы" />
			</center>
		</td>
		<td>
			<center>
				<input class="inp" type="image" value="Очистить таблицы" /><br /><br />
				<input class="inp" type="image" value="Удалить таблицы" />
			</center>
		</td>
		<td>
			<center>
				<input class="inp" type="image" value="Очистить таблицы" /><br /><br />
				<input class="inp" type="image" value="Удалить таблицы" />
			</center>
		</td>
	</tr>
	<tr>
		<td colspan="4">
			<center>
				<input class="inp" type="image" value="Очистить все таблицы" /><br /><br />
				<input class="inp" type="image" value="Удалить все таблицы" />
			</center>
		</td>
	</tr>
</table>


			<h4>Обнуление движка</h4>
			<form method="post" action="/constructor/process/create_sys" onsubmit="">
				<input class="inp" type="image" value="Запустить" />
			</form><br />
