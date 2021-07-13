<h3>Управление модулями сайта</h3>

<table width="100%" height="100%">
  <tr>
    <td style="height: 350px;">
    	{if isset($data.type)&&$data.type!='custom'}
    	<h4>Поля выбранного модуля</h4>
		
		<div style="height: 230px; overflow: scroll;">
			
			<form action="/constructor/process/save_field" method="post">
				<input type="hidden" name="id" value="{$data.id}" />
				<table>
					{foreach from=$model key=k item=item}
					<tr>
						<td>
							{$item.field}
							<input type="hidden" name="data_id[{$k}]" value="{$k}" />
							<input type="hidden" name="field[{$k}]" value="{$item.field}" />
							<input type="hidden" name="type[{$k}]" value="{$item.type}" />
						</td>
						<td><input type="text" name="title[{$k}]" value="{$item.title}" title="Заголовок" /></td>
						<td><input type="checkbox" name="required[{$k}]" {($item.required)?'checked="true"':''} title="Обязательно для заполнения" /></td>
						<td>{$field[$item.type].title}</td>
						<td><input type="text" name="order[{$k}]" value="{$item.order}" size="2" title="Порядок вывода" /></td>
						<td>
							{if $item.type == 'image'}
							<div>
								<table class="table_no_border">
									<tr>
										<td>Размеры (x1.y1;x2.y2):</td>
									</tr>
									<tr>
										<td><input type="text" name="size[{$k}]" value="{$item.size}" size="14" /></td>
									</tr>
								</table>
							</div>
							{/if}
							{if $item.type == 'reference'}
							<div>
								<table class="table_no_border">
									<tr>
										<td>Модуль:</td>
										<td><input type="text" name="reference_module[{$k}]"  value="{$item.module}" size="2" /></td>
									</tr>
									<tr>
										<td>Тип:</td>
										<td>
											<select name="reference_type[{$k}]">
												{foreach from=$field_ref_type key=type item=ref_type}
												
													<option value="{$type}" {($type==$item.ref_type)?'selected="true"':''}>{$ref_type.title}</option>
												{/foreach}
											</select>
										</td>
									</tr>
									<tr>
										<td>Поле связи:</td>
										<td><input type="text" name="reference_pk_field[{$k}]" value="{$item.pk_field}" size="2" /></td>
									</tr>
									<tr>
										<td>Поле заголовка:</td>
										<td><input type="text" name="reference_value_field[{$k}]" value="{$item.value_field}" size="2" /></td>
									</tr>
								</table>
							</div>
							{/if}
							{if $item.type == 'select'}
							<div>
								<table class="table_no_border">
									<tr>
										<td>Тип:</td>
										<td>
											<select name="select_type">
												{foreach from=$field_sel_type key=type item=sel_type}
													<option value="{$type}" {($type==$item.sel_type)?'selected="true"':''}>{$sel_type.title}</option>
												{/foreach}
											</select>
										</td>
									</tr>
									<tr>
										<td>Значения(,):</td>
										<td><input type="text" name="select_values[{$k}]" value="{$item.values}" size="12" /></td>
									</tr>
								</table>
							</div>
							{/if}
						
						</td>
						<td><input type="checkbox" name="delete[{$k}]" title="Удалить поле" /></td>
					</tr>
					{/foreach}
				</table>
				<input class="inp" type="image" value="Сохранить изменения" />
			</form>
		</div>
		
		
		
		<div>
		
			<form action="/constructor/process/add_field" method="post">
				<input type="hidden" name="id" value="{$data.id}" />
				<table>
					<tr>
						<th>Поле</th>
						<th>Заголовок</th>
						<th><span title="Обязательное">О</span></th>
						<th>Тип</th>
						<th>Порядок</th>
						<th>Дополнительно</th>
					</tr>
					<tr>
						<td><input type="text" name="field" size="10" /></td>
						<td><input type="text" name="title" size="10" /></td>
						<td><input type="checkbox" name="required" /></td>
						<td>
							<select name="type" onchange="on_select_field_type(this.value)">
								{foreach from=$field key=type item=item}
									<option value="{$type}">{$item.title}</option>
								{/foreach}
							</select>
						</td>
						<td><input type="text" size="2" name="order" /></td>
						<td rowspan="2">
							<div id="selected_image" style="display: none;">
								<table class="table_no_border">
									<tr>
										<td>Размеры (x1.y1;x2.y2):</td>
									</tr>
									<tr>
										<td><input type="text" name="size" size="14" /></td>
									</tr>
								</table>
							</div>
							<div id="selected_reference" style="display: none;">
								<table class="table_no_border">
									<tr>
										<td>Модуль:</td>
										<td><input type="text" name="reference_module" size="2" /></td>
									</tr>
									<tr>
										<td>Тип:</td>
										<td>
											<select name="reference_type">
												{foreach from=$field_ref_type key=type item=item}
													<option value="{$type}">{$item.title}</option>
												{/foreach}
											</select>
										</td>
									</tr>
									<tr>
										<td>Поле связи:</td>
										<td><input type="text" name="reference_pk_field" size="2" /></td>
									</tr>
									<tr>
										<td>Поле заголовка:</td>
										<td><input type="text" name="reference_value_field" size="2" /></td>
									</tr>
								</table>
							</div>
							<div id="selected_select" style="display: none;">
								<table class="table_no_border">
									<tr>
										<td>Тип:</td>
										<td>
											<select name="select_type">
												{foreach from=$field_sel_type key=type item=item}
													<option value="{$type}">{$item.title}</option>
												{/foreach}
											</select>
										</td>
									</tr>
									<tr>
										<td>Значения(,):</td>
										<td><input type="text" name="select_values" size="12" /></td>
									</tr>
								</table>
							</div>
						</td>
					</tr>
					<tr>
						<td colspan="5" align="center"><input class="inp" type="image" value="Добавить" /></td>
					</tr>
				</table>
			</form>
		
		</div>
		{/if}
    </td>
    <td rowspan="2" width="300px">
    	<h4>Список установленных модулей</h4>
    	<ul class="ul_disc">
			{if isset($mtree[0])}
				{foreach from=$mtree[0] item=item}
					<li>
						<a href="{$item.href}">{$item.title}</a>&nbsp;
						<a class = "requested" rel = "{$item.id}" title = "Удалить модуль" href = "#"><img src = "/rs/images/delete.png" alt = "[Удалить модуль]" /></a>
					</li>
					{if isset($mtree[$item.id])}
						<ul class="ul_disc">
						{foreach from=$mtree[$item.id] item=sub}
							<li>
								<a href="{$sub.href}">{$sub.title}</a>&nbsp;
								<a class = "requested" rel = "{$sub.id}" title = "Удалить модуль" href = "#"><img src = "/rs/images/delete.png" alt = "[Удалить модуль]" /></a>
							</li>
						{/foreach}
						</ul>
					{/if}
				{/foreach}
			{/if}
    	</ul>

    	<div style="height: 200px;"></div>
    	<div>
    		<h4>Создать модуль</h4>
    		<form action="/constructor/process/add_module" method="post">
    			<table>
    				<tr>
    					<td>Имя</td>
    					<td><input type="text" name="name" /></td>
    				</tr>
    				<tr>
    					<td>Родитель</td>
    					<td>
    						<select name="parent_id">
    							<option value="0" selected="true">Корень</option>
    							{foreach from=$list item=item}
    								{if $item.parent_id==0&&!$item.child}
    									<option value="{$item.id}" >{$item.title}</option>
    								{/if}
					    		{/foreach}
    						</select>
    					</td>
    				</tr>
    				<tr>
    					<td>Тип</td>
    					<td>
    						<select name="type">
    							{foreach from=$types key=key item=item}<option value="{$key}">{$item.title}</option>{/foreach}
    						</select>
    					</td>
    				</tr>
    				<tr>
    					<td>Заголовок</td>
    					<td><input type="text" name="title" /></td>
    				</tr>
    				<tr>
    					<td colspan="2" align="center"><input class="inp" type="image" value="Создать" /></td>
    				</tr>
    			</table>
    			
    			
    			
    			
    		</form>
    	</div>
    	
    </td>
  </tr>
  <tr>
    <td>
    	{if !empty($data)}
    	<h4>Настройки модуля</h4>
		<form action="/constructor/process/save_module" method="post">
			<input type="hidden" name="id" value="{$data.id}" />
			<table width="100%">
				<tr>
					<td width="25%">Имя</td>
					<td width="25%"> {$data.name} </td>
					<td width="25%">Тип</td>
					<td width="25%"> {$data.type} </td>
				</tr>
				<tr>
					<td>Родительский модуль</td>
					<td>
					
    					<select name="parent_id">
    						<option value="0" selected="true">Корень</option>
    						{foreach from=$list item=item}
    							{if ($item.id!=$data.id)&&($item.id==$data.parent_id||($item.parent_id==0&&!$item.child&&!$data.child))}
    								<option value="{$item.id}" {($item.id==$data.parent_id)?'selected="true"':''}>{$item.title}</option>
    							{/if}
				    		{/foreach}
    					</select>
					</td>
					<td>Заголовок</td>
					<td><input type="text" name="title" value="{$data.title}" /></td>
				</tr>
				<tr>
					<td>Поле заголовок</td>
					<td><input type="text" name="caption_field" value="{$data.caption_field}" /></td>
					<td>Элементов на стр.</td>
					<td><input type="text" name="maxnum" value="{$data.maxnum}" /></td>
				</tr>
				<tr>
					<td>Контроллер</td>
					<td><input type="text" name="controller" value="{$data.controller}" /></td>
					<td>Отображение в sitemap</td>
					<td><input type="checkbox" name="sitemap_show" {($data.sitemap_show == 1)? 'checked="true"': ''}/></td>
				</tr>
				<tr>
					<td>SEO приоритет</td>
					<td>
						<select name="seo_priority">
							{foreach from=$data.seo_priority_option item=item}
								<option value="{$item}" {($item==$data.seo_priority)?'selected="true"':''}>{$item}</option>
							{/foreach}
						</select>
					</td>
					<td>SEO частота обновления</td>
					<td>
						<select name="seo_changefreq">
							{foreach from=$data.seo_changefreq_option item=item}
								<option value="{$item}" {($item==$data.seo_changefreq)?'selected="true"':''}>{$item}</option>
							{/foreach}
						</select>
					</td>
				</tr>
				<tr>
					<td>Порядок сортировки</td>
					<td colspan="3"><input type="text" name="outorder" value="{$data.outorder}" /></td>
				</tr>
				<tr>
					<td colspan="4" align="center"><input class="inp" type="image" value="Сохранить" /></td>
				</tr>
			</table>
			
		</form>
		{/if}
    </td>
  </tr>
  <tr>
  	<td colspan="2">
  		{if !empty($data)}
  		<h4>Импорт/Экспорт настроек выбранного модуля</h4>
  		<form action="/constructor/process/impotr_module" method="post">
  			<input type="hidden" name="id" value="{$data.id}" />
  			<textarea cols="100" rows="2" name="model">{$data.model}</textarea>
  			<br />
  			<input class="inp" type="image" value="Импортировать" />
  		</form>
  		{/if}
  	</td>
  </tr>
</table>



