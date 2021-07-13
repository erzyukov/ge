<h1>Справочник Назначений</h1>
<div style="text-align: right; width: 100%;">
	<a href="/admin/widget/cargotracker/edit">Назад</a>
</div>
<form action="" method="post" onsubmit="return onReferenceSubmit(this);">
	<input type="submit" value="Добавить" />
	<input type="text" name="value" value="" style="width: 90%;" />
	<input type="hidden" name="action" value="add_reference" />
	<input type="hidden" name="reference" value="destination" />
</form>
<br />
<form action="" method="post">
	<ul>
		{foreach from=$list item=r}
		<li>
			<input type="text" name="value[{$r->id}]" value="{$r->value}" style="width: 95%;" />
			<input type="checkbox" name="delete[{$r->id}]" />
		</li>
		{/foreach}
	</ul>
	<input type="hidden" name="action" value="save_reference" />
	<input type="hidden" name="reference" value="destination" />
	<input type="submit" value="Сохранить" />
</form>