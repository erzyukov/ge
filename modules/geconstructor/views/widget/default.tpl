<h3>Управление виджетами</h3>

<table>
  <tr>
    <th width="50%">Установленные виджеты</th>
    <th width="50%">Список доступных виджетов</th>
  </tr>
  <tr height="300px">
    <td>
	    <form id="delete_widget" action="/constructor/process/delete_widget" method="post">
	    	<table>
	    		<tr>
	    			<th>Название виджета</th>
	    			<th width="10%">Удалить</th>
	    		</tr>
	    		{foreach from=$installed item=item}
		    		<tr>
		    			<td title="{$item.description}">{$item.title}</td>
		    			<td><input type="checkbox" name="widget[]" value="{$item.name}" /></td>
		    		</tr>
	    		{/foreach}
	    	</table>
	    </form>
	</td>
    <td>
	    <form id="install_widget" action="/constructor/process/install_widget" method="post">
	    	<table>
	    		<tr>
	    			<th>Название виджета</th>
	    			<th width="10%">Установить</th>
	    		</tr>
	    		{foreach from=$available item=item}
		    		<tr>
		    			<td title="{$item.description}">{$item.title}</td>
		    			<td><input type="checkbox" name="widget[]" value="{$item.name}" /></td>
		    		</tr>
	    		{/foreach}
	    	</table>
	    </form>
	</td>
  </tr>
  <tr>
    <th><input type="submit" class="" value="Удалить" onclick="$('delete_widget').submit();" /></th>
    <th><input type="submit" class="" value="Установить" onclick="$('install_widget').submit();" /></th>
  </tr>
</table>

