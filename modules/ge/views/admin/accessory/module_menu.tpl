<ul>
	{foreach from=$list item=rec}
		<li><a href="{$rec.href}">{$rec.title}</a></li>
	{/foreach}
</ul>