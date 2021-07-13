<tr>
	<td style="padding-left: {$padding}px;">
		<span id="menu_element_title_{$id}" class="menu_element_click" onclick="setElementEditalble(this.id);">{$title}</span>
		<input id="input_menu_element_title_{$id}" type="hidden" name="title[{$id}]" value="{$title}" />
	</td>
	<td>
		<span id="menu_element_module_{$id}" class="menu_element_click" onclick="setElementSelectEditalble(this.id);fillElementModuleSelect({$id});">
			{if $module}{$module}{else}* * * * * * * * * * * *{/if}
		</span>
		<div id="select_menu_element_module_{$id}" style="display: none;">
			<select id="menu_select_module_{$id}" name="module_id[{$id}]" onchange="onMenuSelectModuleChange({$id});"></select>
		</div>
		<input type="hidden" id="menu_module_id_{$id}" value="{$module_id}" />
	</td>
	<td>
		<span id="menu_element_item_{$id}" class="menu_element_click" onclick="setElementSelectEditalble(this.id);fillElementItemSelect({$id});">
			{if $item}{$item}{else}* * * * * * * * * * * *{/if}
		</span>
		<div id="select_menu_element_item_{$id}" style="display: none;">
			<select id="menu_select_item_{$id}" name="item_id[{$id}]"></select>
		</div>
		<input type="hidden" id="menu_item_id_{$id}" value="{$item_id}" />
	</td>
	<td>
		<a href="{$href}" title="Посмотреть ссылку" target="_blank">[V]</a> 
		<span id="menu_element_href_{$id}" class="menu_element_click" onclick="setElementEditalble(this.id);">{$href}</span>
		<input id="input_menu_element_href_{$id}" type="hidden" name="direct_link[{$id}]" value="" />
	</td>
	<td><center><input type="hidden" name="id[{$id}]" value="{$id}" /><input class="inp2" type="text" name="outorder[{$id}]" value="{$outorder}" /></center></td>
	<td><center><input type="checkbox" name="delete[{$id}]" /></center></td>
</tr>