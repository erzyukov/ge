<?php defined('SYSPATH') or die('No direct script access.');

	/**
	 * Надстройка над кохановским контроллером-шаблонизатором.
	 * Организатор структуры индексных шаблонов сайта.
	 * 
	 * @package GE2
	 * @author Oliver (aka Erzukov Aleksey)
	 * @copyright  (c) 2007-2010 Global Ltd.
	 * @license    http://global.su/license
	 */
// TODO ! Расписать комментарии ко всем функциям класса
class Controller_Constructor extends Controller_Template {

	public $template = 'smarty:default';
	public $content;
	public $paction;
	public $id;

	public function __construct($request)
	{
		parent::__construct($request);
		$this->paction = $request->param('paction');
		$this->id = $request->param('id', NULL);
		
		if (Kohana::config('ge.production')){
			$this->template = 'smarty:production';
			return false;
		}
		
	}
	
	/**
	 * главная страница конфигуратора, отображается общая информация по установкам
	 */
	public function action_index(){
		
		$data = array();
		
		$data['sys_tables'] = Constructor_Sys::get_tables(GE::pref('sys'));
		$data['mod_tables'] = Constructor_Mod::get_tables(GE::pref('mod'));
		
		$this->content = View::factory('smarty:info/default', $data);
	}
	
	/**
	 * Редактирование языков сайта
	 */
	public function action_lang(){
		
		$data = array();
		$cur_lang = (isset($_GET['lang']))? $_GET['lang']: GE::lang('uri');
		
		$lang = ORM::factory('lang');
		$data['cur'] = $lang->where('uri', '=', $cur_lang)->find()->as_array();
		$list = $lang->find_all()->as_array();
		foreach ($list as $item)
			$data['list'][] = $item->as_array();
		
		$this->content = View::factory('smarty:lang/default', $data);
	}

	/**
	 * Управление виджетами сайта
	 */
	public function action_widget(){
		
		$data = array();
		
		// находим установленные
		$installed = array();
		$widget = ORM::factory('widget');
		$list = $widget->get_list();
		foreach($list as $item){
			$item['description'] = Kohana::config($item['name'].'.description');
			$installed[$item['name']] = $item;
		}
		
		// находим возможные для установки
		$files = Kohana::list_files('gewidget/config', array(MODPATH));

		$available = array();
		foreach($files as $file){
			
			$info = pathinfo($file);
			$name = $info['filename'];
			
			if (! array_key_exists($name, $installed)){
				$config = Kohana::config($name);
				
				$available[] = array(
					'title' => $config->title,
					'description' => $config->description,
					'name' => $name,
				);
			}
		}
		
		$data['installed'] = $installed;
		$data['available'] = $available;

		$this->content = View::factory('smarty:widget/default', $data);
	}
	
	/**
	 * Редактирование модулей движка сайта
	 */
	public function action_module(){

		$data = array();
		// список типов модулей
		$data['types'] = Kohana::config('constructor.module_type');
		
		// список модулей
		$module = ORM::factory('module');
		$module_list = $module->get_list();
		$module_tree = array();
		foreach ($module_list as $k => $v){
			$v['title'] = $v['translate'][GE::lang('id')]['title'];
			$module_list[$k]['title'] = $v['title'];
			$module_tree[$v['parent_id']][$k] = $v;
			$module_tree[$v['parent_id']][$k]['href'] = Route::url('constructor', array('action' => Request::instance()->param('action', 'module'), 'id' => $v['id']));
		}
		$data['list'] = $module_list;
		$data['mtree'] = $module_tree;

		// данные модуля
		$data['data'] = array();
		$data['model'] = array();
		if (($this->id)){
			$tmp_module = $module->find($this->id);
			$data['data'] = $tmp_module->as_array();
			$data['data']['title'] = $module->find($this->id)->trmodules->where('lang_id', '=', GE::lang('id'))->find()->title;
			$data['data']['seo_changefreq_option'] = $module->get_changefreq_options();
			$data['data']['seo_priority_option'] = $module->get_priority_options();
			$data['data']['child'] = $tmp_module->child_exists();
			
			$model = unserialize($data['data']['model']);
			if ($model) {
				foreach ($model as $k => $field){
					switch ($field['type']){
						case 'image':
							$tmp = array();
							foreach ($field['size'] as $sk => $size)
								$field['size'][$sk] = implode('.', $size);
							$model[$k]['size'] = implode(';', $field['size']);
							break;
						case 'select':
							$model[$k]['values'] = implode(',', $field['values']);
							break;
					}
				}
			}
			$data['model'] = ($model)? $model : array();
		}



		$data['field'] = Kohana::config('constructor.field_type');
		$data['field_ref_type'] = Kohana::config('constructor.reference_type');
		$data['field_sel_type'] = Kohana::config('constructor.select_type');

		
		$this->content = View::factory('smarty:module/default', $data);
	}
	
	/**
	 * Обработка post-запросов
	 */
	public function action_process(){
		$redirect = '/constructor/';
		switch($this->paction){
			case 'create_sys':
				Constructor_Sys::create_sys_tables();
				break;
			case 'delete_sys':
				if (isset($_POST['table']) AND $data = array_keys($_POST['table']))
					Constructor_Sys::delete_sys_tables($data);
				break;
			case 'add_lang':
				$uri = Constructor_Lang::create_lang($_POST);
				$redirect = '/constructor/lang/?lang='.$uri;
				break;
			case 'edit_lang':
				$uri = Constructor_Lang::edit_lang($_POST);
				$redirect = '/constructor/lang/?lang='.$uri;
				break;
			case 'delete_lang':
				$uri = Constructor_Lang::delete_lang($_POST);
				$redirect = '/constructor/lang/?lang='.$uri;
				break;
			case 'add_module':
				$id = Constructor_Mod::create_module($_POST);
				$redirect = '/constructor/module/id'.$id;
				break;
			case 'save_module':
				$id = Constructor_Mod::save_module($_POST);
				$redirect = '/constructor/module/id'.$id;
				break;
			case 'add_field':
				$id = Constructor_Mod::create_field($_POST);
				$redirect = '/constructor/module/id'.$id;
				break;
			case 'save_field':
				$id = Constructor_Mod::save_field($_POST);
				$redirect = '/constructor/module/id'.$id;
				break;
			case 'impotr_module':
				$id = Constructor_Mod::import_field($_POST);
				$redirect = '/constructor/module/id'.$id;
				break;
			case 'delete_module':
				$id = Constructor_Mod::delete_module($_POST);
				$redirect = '/constructor/module/';
				break;
			case 'install_widget':
				Constructor_Wid::install_widget($_POST);
				$redirect = '/constructor/widget/';
				break;
			case 'delete_widget':
				Constructor_Wid::delete_widget($_POST);
				$redirect = '/constructor/widget/';
				break;
		}
		Request::instance()->redirect($redirect);
	}
	
	public function before(){
		parent::before();
		$this->template
			->bind('content', $this->content);
	}
	
	public function after(){
		
		parent::after();
	}
	
	
}