<form id="item_save_form" method="post" enctype="multipart/form-data">
  <table class="edit page" id="item_edit_table">
    
	{$fields}
	
	{$default}
    
	{$system}
    
  </table>
  <input type="hidden" id="caption_field_name" value="{$caption_field}" />
  <input type="checkbox" title="Вернуться к списку" name="_back" checked="true"/> Вернуться к списку<br /><br />
  <input type="hidden" name="action" value="edit_item" />
  <input class="inp" type="image" alt="Сохранить" title="Сорханить" src="" />
  <input class="inp" type="image" alt="Отмена" title="Отмена" onclick="window.history.back(); return!1" src="" />
</form>