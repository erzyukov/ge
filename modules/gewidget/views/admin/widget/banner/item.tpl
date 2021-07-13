{*<i>* - Чтобы баннер можно было сделать активным, необходимо загрузить изображение</i>*}
<form id="item_save_form" method="post" enctype="multipart/form-data">
  <table class="edit page" id="item_edit_table">
    <tr>
    	<th>Заголовок</th>
    	<td><input type="text" name="caption" value="{$caption}" /></td>
    </tr>
    <tr>
    	<th>Файл</th>
    	<td>
    		<a href="{$path}" target="_blank">открыть</a><br />
    		<input type="file" name="file" />
    	</td>
    </tr>
    <tr>
    	<th>Расположение</th>
    	<td>
    		<a href="#" title="посмотреть расположение баннеров на сайте">посмотреть расположение баннеров на сайте</a><br />
    		<input type="text" name="position" value="{$position}" />
    	</td>
    </tr>
    <tr>
    	<th>Ссылка</th>
    	<td><input type="text" name="href" value="{$href}" /></td>
    </tr>
    <tr>
    	<th>Ширина</th>
    	<td><input type="text" name="width" value="{$width}" /></td>
    </tr>
    <tr>
    	<th>Высота</th>
    	<td><input type="text" name="height" value="{$height}" /></td>
    </tr>
    <tr>
    	<th>Тип баннера</th>
    	<td><select name="type">{foreach from=$type_list item=r key=i}<option value="{$i}" {if $i==$type}selected="true"{/if}>{$r}</option>{/foreach}</select></td>
    </tr>
    <tr>
    	<th>Максимальне число показов</th>
    	<td><input type="text" name="max_show" value="{$max_show}" /></td>
    </tr>
    <tr>
    	<th>Максимальне число кликов</th>
    	<td><input type="text" name="max_click" value="{$max_click}" /></td>
    </tr>
{*
    <tr>
    	<th>Дата окончания показа</th>
    	<td><input type="text" name="max_date" value="{$max_date}" /></td>
    </tr>
    <tr>
    	<th>Страницы для отображения</th>
    	<td>
    		<select name="target" size="10" multiple="true" >
    			{foreach from=$sitemap item=r}
    			<option value="{$r.id}">{$r.caption}</option>
    			{/foreach}
    		</select>
    	</td>
    </tr>
*}
    <tr>
    	<th>Активность</th>
    	<td><input type="checkbox" name="isactive" {$isactive} /></td>
    </tr>
        
  </table>
  <input type="checkbox" title="Вернуться к списку" name="_back" checked="true"/> Вернуться к списку<br /><br />
  <input type="hidden" name="action" value="edit_banner" />
  <input class="inp" type="image" alt="Сохранить" title="Сорханить" src="" />
  <input class="inp" type="image" alt="Отмена" title="Отмена" onclick="window.history.back(); return!1" src="" />
</form>