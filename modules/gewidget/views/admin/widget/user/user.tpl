<h3>{$user.fio} ({$user.email})</h3>
<form action="/admin/widget/user/{$user.id}/edituser" method="post">
	<table class="list-table">
		<tr>
			{foreach name="config" from=$data key="field" item="item"}
				<tr>
					<th>
						{$item.title}
					</th>
					<td>
						{if $item.type=="text"||$item.type=="password"}
							<input type = "{$item.type}" name = "{$field}" value = "{if isset($user[$field])&&$item.type!="password"}{$user[$field]}{/if}" />
						{elseif $item.type=="textarea"}
							<textarea name = "{$field}">{if isset($user[$field])}{$user[$field]}{/if}</textarea>
						{/if}
					</td>
				</tr>
			{/foreach}
		</tr>
	</table>
	<input class="inp" alt="Сохранить изменения" title="Сохранить изменения" value="" name="" type="image" />
</form>