    <tr><th colspan="2"><br /><strong>Системная информация</strong></th></tr>
    <tr>
    	<th>Заголовок страницы (title)</th>
    	<td><input class="inp2" type="text" name="sys[title]" value="{$title}" /></td>
    </tr>
    <tr>
    	<th>Ключевые слова(meta:keywords)</th>
    	<td><input class="inp2" type="text" name="sys[keywords]" value="{$keywords}" /></td>
    </tr>
    <tr>
    	<th>Описание (meta:description)</th>
    	<td><input class="inp2" type="text" name="sys[description]" value="{$description}" /></td>
    </tr>
    <tr>
    	<th>Короткий Url <a href="" onclick="updateShortUrl();return!1;">[***]</a></th>
    	<td><input id="item_short_url" class="inp2" type="text" name="sys[short_url]" value="{$short_url}" /></td>
    </tr>
    <tr>
    	<th>Выводить в карте сайта</th>
    	<td><input type="checkbox" name="sys[sitemap_show]" {if $sitemap_show==1}checked="true"{/if} /></td>
    </tr>
    <tr>
    	<th>Приоритет (sitemap)</th>
    	<td>
    		<select name="sys[priority]">
    			{foreach from=$priority_list item=rec}
    				<option value="{$rec}" {if $rec==$priority}selected="true"{/if}>{$rec}</option>
    			{/foreach}
    		</select>
    	</td>
    </tr>
    <tr>
    	<th>Частота обновления (sitemap)</th>
    	<td>
    		<select name="sys[changefreq]">
    			{foreach from=$changefreq_list item=rec}
    				<option value="{$rec}" {if $rec==$changefreq}selected="true"{/if}>{$rec}</option>
    			{/foreach}
    		</select>
    	</td>
    </tr>
    <tr>
    	<th>Пользователь</th>
    	<td>{$user}</td>
    </tr>
    <tr>
    	<th>Последние изменения</th>
    	<td>{$lastmod}</td>
    </tr>
