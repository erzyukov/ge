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
class GE_Template extends Controller_Template {

	public $template = 'smarty:index';
	public $template_lang = '';
	
	public $js = '';
	public $css = '';
	
	public $title = 'GE2 [Global Engine 2]';
	public $keywords = '';
	public $description = '';
	public $navigation = '';
	public $content = 'Site Content';
	public $part_menu = '';
	public $app_data = array();
	
//	public $debug = '';
	private $_js = array();
	private $_css = array();
	private $_navigation = array();
	protected $nav_name = 'Главная';
	
//	private $_title = array();
//	private $_keywords = array();
//	private $_description = array();
	
	public function __construct($request)
	{
		parent::__construct($request);
//		$this->_js = Gengine::get_resource('js', Gengine::get_path('pth_js'));
//		$this->_css = Gengine::get_resource('css', Gengine::get_path('pth_css'));
//		$this->_js = Arr::merge($this->_js, Gengine::get_resource('app_js', Gengine::get_path('pth_app')));
//		$this->_css = Arr::merge($this->_css, Gengine::get_resource('app_css', Gengine::get_path('pth_app')));
//		
//		$this->add_title(Gengine::config('title'));
//		$this->add_keywords(Gengine::config('keywords'));
//		$this->add_description(Gengine::config('description'));
//		$this->add_navigation(Gengine::config('site.navigation_title'), '/');
//
		$this->_initialize();

	}
	
	protected function _initialize(){}
	
	public function before(){
//		$this->auto_render = FALSE;
		parent::before();
		
		$this->template
			->bind('title', $this->title)
			->bind('description', $this->description)
			->bind('keywords', $this->keywords)
			->bind('js', $this->js)
			->bind('css', $this->css)
			->bind('navigation', $this->navigation)
			->bind('part_menu', $this->part_menu)
			->bind('content', $this->content)
			->bind('app', $this->app_data);
	}
	
	public function after(){
		//		$this->add_widget_resources();
		$this->js = $this->js_build();
		$this->css = $this->css_build();
		$this->navigation = $this->navigation_build();
//_d(get_class($this));
		//_d($this->js);
//		
//
//		$this->title = $this->var_build($this->_title, Gengine::config('site.title_sep'));
//		$this->keywords = $this->var_build($this->_keywords);
//		$this->description = $this->var_build($this->_description);

		//$this->debug = View::factory('profiler/stats');
		
		parent::after();
	}
	
	protected function add_navigation($title, $url, $first = FALSE){
		$item = array('title' => $title, 'url' => $url);
		if (!$first)
			array_push($this->_navigation, $item);
		else
			$this->_navigation = array_pad($this->_navigation, -(count($this->_navigation) + 1), $item);
	}
	
	protected function navigation_build(){
		$this->add_navigation(__($this->nav_name), $this->template_lang.'/', TRUE);
		$result = '';
		if (count($this->_navigation) > 1){
			$data = array();
			$data['sep'] = str_replace(' ', '&#160;', Kohana::config('ge.navigation_separator'));
			$data['list'] = array();
			foreach ($this->_navigation as $item) {
				$item['title'] = __($item['title']);
				$data['list'][] = $item;
			}
			$result = GE::view('accessory/navigation', $data, TRUE);
		}
		return $result;
	}
	
//	protected function set_navigation($value){
//		$this->_navigation = $value;
//	}
	
	protected function add_js($res){
		$this->_js[] = $res;
	}
	
	protected function add_css($res){
		$this->_css[] = $res;
	}
	
	protected function css_build()
	{
		$result = '';
		$dir = Kohana::config('ge.css_dir');
		$path = $_SERVER['DOCUMENT_ROOT'].$dir;
		$list = scandir($path);
		foreach($list as $file){
			if (strpos($file, '.css') !== FALSE)
				$result .= '<link href="'.$dir.$file.'" rel="stylesheet" type="text/css" />'."\r\n";
		}
		
		foreach ($this->_css as $r){
			$result .= '<link href="'.$r.'" rel="stylesheet" type="text/css" />'."\r\n";
		}
		return $result."\n";
	}

	protected function js_build()
	{
		$result = '';
		$dir = Kohana::config('ge.js_dir');
		$path = $_SERVER['DOCUMENT_ROOT'].$dir;
		$list = scandir($path);
		foreach($list as $file){
			if (strpos($file, '.js') !== FALSE)
				$result .= '<script type="text/javascript" src="'.$dir.$file.'"></script>'."\r\n";
		}
		
		foreach ($this->_js as $r){
			$result .= '<script type="text/javascript" src="'.$r.'"></script>'."\r\n";
		}
		return $result."\n";
	}

	protected function get_lang($field = 'uri') {
		$lang = array_keys(Kohana::config('lang.resolve'), I18n::$lang);
		switch($field) {
			case 'id': $lang_id = array_keys(GE::lang_list(), $lang[0]); return $lang_id[0];
			default: return $lang[0];
		}
	}
	
	protected function get_lang_list() {
		$data['list'] = GE::lang_list(NULL, NULL);
		$data['path'] = ($this->request->param('module'))? '/'.$this->request->param('module').'/'.$this->request->param('id') : '';
		return $data;
	}
	
	
}