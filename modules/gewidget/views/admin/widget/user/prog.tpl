<h3>Редактирование программы пользователя: <u>{$login}</u></h3>
<form action="" method="post">


	<table>
		{foreach from=$day_value item=r key=k}
		<tr>
			<th width="30px" >{$day[$k]}</th>
			<td width="500px">
				<div id="prog_content_{$k}">
					{if isset($prog[$k])}
					{foreach from=$prog[$k] item=r}
					{if $r!=''}
					<input class="prog_input" type="text" name="prog[{$k}][]" value="{$r}" />
					{/if}
					{/foreach}
					{/if}
				</div>
				<input class="prog_input inp" type="button" value="добавить строку" onclick="addProg({$k});" />
				<input id="input_tpl_{$k}" class="prog_input" type="hidden" name="prog[{$k}][]" value="" />
			</td>
		</tr>
		{/foreach}
	</table>

	<br /><br />
	<h4>Дополнительная информация</h4>
	<textarea rows="4" cols="65" name="info">{$info}</textarea>
	
	<br /><br />
	<input type="hidden" name="action" value="save_prog" />
	<input class="inp" type="submit" value="Сохранить" />
</form>