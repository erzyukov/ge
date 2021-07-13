<?php defined('SYSPATH') or die('No direct script access.');

	/**
	 * Виджет - Индексация
	 * 
	 * @package Gengine
	 * @author Oliver (aka Erzukov Aleksey)
	 * @copyright  (c) 2007-2010 Global Ltd.
	 * @license    http://global.su/license
	 */
// TODO ! Расписать комментарии ко всем функциям класса
class Controller_Admin_Widget_Indexation extends Manage {
	
	protected $view_dir = 'admin/widget/indexation/';
	protected $cur_page_url = '/';
	protected $stem = NULL;

	protected $config = array();
	
	public function __construct($request){
		parent::__construct($request);
		$this->config = Kohana::config('indexation');
		$this->stem = new Widget_Indexation_Stemru();
	}
	
	public function action_edit(){
		$data = array();
		
		$data['info'] = $this->get_indexation_info();
		
		if (isset($_GET['index']) AND $_GET['index'] == 'ok')
			$data['m'] = 'Индексация прошла успешно';
		else
			$data['m'] = 'Не прерывайте ход индексации!';

		$this->content = GE::view($this->view_dir.'cover', $data);
	}
	
	protected function get_indexation_info(){
		$data = array();

		$q = DB::query(Database::SELECT, 'SELECT * FROM '.GE::pref('wid').'search_url')->execute();
		$data['page_count'] = $q->count();

		$q = DB::query(Database::SELECT, 'SELECT * FROM '.GE::pref('wid').'search_keyword')->execute();
		$data['key_count'] = $q->count();
		
		return GE::view($this->view_dir.'info', $data);
	}
	
	public function action_start(){
		if (isset($_POST['action']) AND $_POST['action'] == 'start'){
			$this->indexation();
		}
	}
	
	
	protected function get_page_words($dom){

		return $this->parse_text($dom->plaintext);
		
	}
	
	protected function get_enhanced_page_words($dom){
		$result = array();
		
		foreach ($this->config['tags_weight'] as $tag => $weight) {

			foreach($dom->find($tag) as $item){
				
				$key = $this->parse_text($item->plaintext, $weight);
				$result = array_merge($result, $key);
			}
		}
		
		return $result;
	}
	
	protected function parse_text($text, $weight = 1){
		$key = array();
		
		$text = preg_replace('/&\w+;/i', '', $text);
		$cont_text = preg_replace('/\n|\t|\r/i', ' ', $text);
		$text = preg_replace('/\.|\,/i', ' ', $cont_text);
		$words = explode(' ', $text);
		foreach($words as $word){
			$word = mb_strtolower(trim($word), 'utf-8');
			$word = $this->stem->stem_word($word);
			if ( mb_strlen($word, 'utf-8') > 2 ){
				
				$repit = false;
				foreach($key as $ki => $kv){
					if ($key[$ki]['keyword'] === $word){
						$key[$ki]['weight'] += 1;
						$repit = true;
					}
				}
				if (!$repit){
					
				
					$key[]=array('url' => $this->cur_page_url, 'weight' => $weight, 'keyword' => $word, 'cont' => '');
				}
			}
		}
		return $key;
	}
	
	protected function enhance_keys($keys, $enhanced){
		foreach ($keys as $i => $key){
			foreach ($enhanced as $en_key){
				if ($key['keyword'] == $en_key['keyword'])
					$keys[$i]['weight'] = $key['weight'] * $en_key['weight'];
			}
		}
		return $keys;
	}
	
	
	protected function get_page_link($dom){
		$result = array();
		$modules = GE::module_list();
		foreach($dom->find('a') as $item){
			$part = explode('/', $item->href);
			if (isset($part[1]) AND in_array($part[1], $modules) AND !in_array($item->href, $this->config['exept_link']))
				$result[] = $item->href;
		}
		$result = array_unique($result);
		return $result;
	}
	

	protected function indexation(){
		set_time_limit(0);
		
		DB::query(NULL, 'TRUNCATE TABLE `'.GE::pref('wid').'search_url`')->execute();
		DB::query(NULL, 'TRUNCATE TABLE `'.GE::pref('wid').'search_keyword`')->execute();
		DB::query(NULL, 'TRUNCATE TABLE `'.GE::pref('wid').'search_url_key`')->execute();
		
		$this->site_indexation('/');
		
		$url = Route::get('admin_widget')->uri(array(
			'widget' => 'indexation',
			'controller' => 'indexation',
			'action' => 'edit',
		));
		
		Request::instance()->redirect($url.'?index=ok');
	}
   


	protected function site_indexation($page_url){
		$curl = curl_init(); 
		curl_setopt($curl, CURLOPT_URL, 'http://'.$_SERVER['SERVER_NAME'].$page_url);  
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);  
		$str = curl_exec($curl);  
		curl_close($curl);  
		
		
		$html = Parser::html($str);
		
		$page_title = $html->find('title', 0)->plaintext;
		
		$keys = array();

		if (is_array($this->config['content_class'])){
			foreach($this->config['content_class'] as $class_name){
				$content = $html->find('.'.$class_name, 0);
				if ($content){
					$keys_tmp = $this->get_page_words($content);
					$enhanced = $this->get_enhanced_page_words($content);
					$keys = array_merge($keys, $this->enhance_keys($keys_tmp, $enhanced));
				}
			}
		}
		else{
			$content = $html->find('.'.$this->config['content_class'], 0);
			if ($content){
				$keys = $this->get_page_words($content);
				$enhanced = $this->get_enhanced_page_words($content);
				$keys = $this->enhance_keys($keys, $enhanced);
			}
		}
		
		
		list($url_id, $effect_rows) = 
		DB::insert(GE::pref('wid').'search_url', array('name', 'title'))
			->values(array($page_url, $page_title))->execute();
			
		foreach ($keys as $i => $v){
			list($key_id, $effect_rows) = 
				DB::insert(GE::pref('wid').'search_keyword', array('name'))
				->values(array($v['keyword']))->execute();
			DB::insert(GE::pref('wid').'search_url_key', array('url_id', 'key_id', 'weight', 'cont'))
				->values(array((int)($url_id), (int)($key_id), (int)($v['weight']), $v['cont']))
				->execute();
		}
		
		
		$links = $this->get_page_link($html);
		
		foreach($links as $href){
			
			$q = DB::query(Database::SELECT, 'SELECT * FROM '.GE::pref('wid').'search_url WHERE name=\''.$href.'\'')->execute();
			
			if(!$q->count()){
				$this->site_indexation($href);
			}
		}
	}
	
	
	
	
	
	
	
}