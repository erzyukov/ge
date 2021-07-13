<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Widget_User extends ORM {

	
	// Validation rules
	protected $_rules = array(
		'login' => array(
			'not_empty'  => NULL,
			'min_length' => array(3),
			'max_length' => array(30),
			'regex'			=> array('/[a-zA-Z_0-9]{3,30}/'),
		),
		'mail' => array(
//			'mail'  => NULL,
			'min_length' => array(6),
			'max_length' => array(30),
		),
		'password' => array(
			'not_empty'  => NULL,
			'min_length' => array(3),
			'max_length' => array(30),
		),
		'last_login' =>array(),
		'data' =>array(
			'not_empty'  => NULL,
		),
		'isactive' =>array(),
		'cookie' =>array(),
	);

	// Validation callbacks
	protected $_callbacks = array(
		'login' => array('login_available'),
	);
	
	
	/**
     * Перекрытие инициализации объекта
     */
	protected function _initialize()
	{
		if ($table_name = GE::pref('wid', 'user'))
		{
			$this->_table_name = $table_name;
		}
	 
		parent::_initialize();
	}	

	
	
    public function add_user($data){
    	$this->values($data)->save();
		return $this->id;
	}
	
	public function validate_add($array){
		$array = Validate::factory($array)
					->rules('login', $this->_rules['login'])
					->rules('password', $this->_rules['password'])
					->filter('login', 'trim')
					->filter('password', 'trim');
		
		foreach ($this->_callbacks as $field => $callbacks){
			foreach ($callbacks as $callback){
				$array->callback($field, array($this, $callback));
			}
		}
		return $array;
	}
	
	public function validate_save($array){
		$r_login = $this->_rules['login'];
		unset($r_login['not_empty']);
		$r_pass = $this->_rules['password'];
		unset($r_pass['not_empty']);
		$array = Validate::factory($array)
					->rules('login', $r_login)
					->rules('mail', $this->_rules['mail'])
					->rules('password', $r_pass)
					->rules('data', $this->_rules['data'])
					->filter('login', 'trim')
					->filter('password', 'trim');
					
		foreach ($this->_callbacks as $field => $callbacks){
			foreach ($callbacks as $callback){
				$array->callback($field, array($this, $callback));
			}
		}
		return $array;
	}
	
	
	/**
	 * Validates login information from an array, and optionally redirects
	 * after a successful login.
	 *
	 * @param  array    values to check
	 * @param  string   URI or URL to redirect to
	 * @return boolean
	 */
	public function login(array & $array, $redirect = FALSE)
	{
		$array = Validate::factory($array)
			->filter(TRUE, 'trim')
			->rules('login', $this->_rules['login'])
			->rules('password', $this->_rules['password']);

		// Login starts out invalid
		$status = FALSE;

		if ($array->check())
		{
			// Attempt to load the user
			$this->where('login', '=', $array['login'])->find();

			if ($this->loaded() AND Wauth::instance()->login($this, $array['password']))
			{
				if (is_string($redirect))
				{
					// Redirect after a successful login
					Request::instance()->redirect($redirect);
				}

				// Login is successful
				$status = TRUE;
			}
			else
			{
				$array->error('login', 'invalid');
			}
		}

		return $status;
	}

	/**
	 * Does the reverse of unique_key_exists() by triggering error if username exists
	 * Validation Rule
	 *
	 * @param    Validate  $array   validate object
	 * @param    string    $field   field name
	 * @param    array     $errors  current validation errors
	 * @return   array
	 */
	public function login_available(Validate $array, $field)
	{
		if ($this->unique_key_exists($array[$field])) {
			$array->error($field, 'login_available', array($array[$field]));
		}
	}
	
	/**
	 * Tests if a unique key value exists in the database
	 *
	 * @param   mixed        value  the value to test
	 * @return  boolean
	 */
	public function unique_key_exists($value)
	{
		return (bool) DB::select(array('COUNT("*")', 'total_count'))
						->from($this->_table_name)
						->where('login', '=', $value)
						->execute($this->_db)
						->get('total_count');
	}
	
	public function save(){
		
		if (array_key_exists('password', $this->_changed))
			$this->_object['password'] = Wauth::instance()->hash_password($this->_object['password'], Wauth::instance()->find_salt($this->_object['password']));

		return parent::save();
	}
	
	
	
//	public function validate($field, $value, & $error = NULL) {
//		switch ($field) {
//			case 'email': 
//				if (!preg_match('/^[^0-9][A-z0-9_]+([.][A-z0-9_]+)*[@][A-z0-9_]+([.][A-z0-9_]+)*[.][A-z]{2,4}$/', $value)) {
//					$error = 'Некорректный e-mail';
//					return false;
//				}
//				break;
//			case 'password': 
//				if (strlen($value) > 50) {
//					$error = 'Слишком длинный пароль!';
//					return false;
//				}
//				if (strlen($value) < 6) {
//					$error = 'Слишком короткий пароль!';
//					return false;
//				}
//				break;
//			case 'captcha': 
//				if (!Captcha::valid($value)) {
//					$error = 'Неверные символы!';
//					return false;
//				}
//		}
//		return true;
//	}
/*	
	public function set_register_data ($data, $fields) {
		foreach($fields as $field => $settings){
			if (isset($data[$field])) {
				$value = $data[$field];
				switch ($field) {
					case 'password_confirm': 
						break;
					case 'password': 
						$this->password = Wauth::instance()->hash_password($value);
						break;
					default: $this->$field = (isset($value))? $value : NULL;
				}
			}
		}
		if (isset($data['discount'])) {
			$this->discount = $data['discount'];
		}
		$this->save();
//		$sender = new Controller_Sender(Request::factory('/'));
//		$sender->notify_new_user($this);
//		$sender->user_notify($this);
		return $this;
	}
	
	public function set_edit_data ($data, $fields) {
		foreach($fields as $field => $settings){
			if (isset($data[$field])) {
				$value = $data[$field];
				switch ($field) {
					case 'password_confirm': 
						break;
					case 'password': 
						if (!empty($data['password']) && !empty($data['password_confirm'])) {
							$this->password = Wauth::instance()->hash_password($value);
						}
						break;
					default: $this->$field = (isset($value))? $value : NULL;
				}
			}
		}
		$this->save();
		return $this;
	}
	
	public function set_info_data ($data, $fields) {
		foreach($fields[(($this->islegal)? 'legal' : 'private')] as $field){
			if (isset($data[$field['field']])) {
				$value = $data[$field['field']];
				switch ($field['field']) {
					case 'old_password':
					case 'password_confirm':
						break;
					case 'password': 
						$this->password = (!empty($value))? Wauth::instance()->hash_password($value) : $this->password;
						break;
					case 'birth': 
						$this->birth = strtotime($value);
						break;
					default: $this->$field['field'] = (isset($value))? $value : NULL;
				}
			}
			elseif ($field['type'] == 'checkbox') {
				$this->$field['field'] = 0;
			}
		}
		$this->issynchronized = 0;
		$this->save();
	}
*/

/*	
    public function edit_user($data, $id){
		unset($data['password_confirm']);
    	if(!empty($data['password'])) {
    	    $data['password'] = Wauth::instance()->hash_password($data['password'], Wauth::instance()->find_salt($data['password']));   
    	}
    	else {
    		unset($data['password']);
    	}
		unset($data['x'], $data['y']);
    	DB::update($this->_table_name)->set($data)->where('id', '=', $id)->execute();
	}
	
	public function notify_email($subject, $message) {
		Email::send(
		    $this->email, 
		    Gengine::config('sitemail.mail_main.address'), 
		    'Сообщение с сайта '.$_SERVER['SERVER_NAME'].'. '.$subject, 
		    $message, 
		    TRUE
		);
	}
*/

/*
    public function notify_sms($subject, $message) {
		//TODO: отправка смс...
//	
//		 * 
//		 * Мегафон Дальний Восток
//         * Адрес: +7924номер_телефона@sms.megafondv.ru
//         * 
//         * МТС в Приморском крае:
//         * Адрес: номер_телефона@sms.primtel.ru
//         * 
//         * Акос
//         * Адрес: +7номер_ткелефона@sms.akos.ru
//         * 
//         * НТК
//         * Адрес номер_телефона@sms.vntc.ru 
//		 
    	if (empty($this->phone)) {
    		return false;
    	}
    	//Преобразуем к виду 9502855303
    	$number = preg_replace('/[^\d]/', '', $this->phone);
    	$number = ($number[0] == '8' || $number[0] == '7')? substr($number, 1) : $number;
    	$operators = array (
    	    'sms.megafondv.ru' => array('/924[\d]/', '+7'),
    	    'sms.primtel.ru' => array('/914[\d]/', '7'),
    	    'sms.akos.ru' => array('/(90248|950)[\d]/', '+7'),
    	    'sms.vntc.ru' => array('/(902|951|908|909|904)[\d]/', '7')
    	);
    	$info = NULL;
    	foreach ($operators as $server => $info) {
    		if (preg_match($info[0], $number)) {
    			$info = array($server, $info[1]);
    			break;
    		}
    	}
    	if ($server) {
			Email::send(
		        $info[1].$number.'@'.$info[0], 
		        Gengine::config('sitemail.mail_main.address'), 
		        'Сообщение с сайта '.$_SERVER['SERVER_NAME'].'. '.$subject, 
		        $message, 
		        TRUE
		    );
    	}
	}
	
	public function get_primary_address() {
		$addresses = $this->get_addresses();
		return $addresses[0];
	}
	
	public function get_addresses() {
		return ORM::factory('waddress')->where('id', 'IN', DB::select('address_id')->from(Gengine::wpref().'address_rel')->where('user_id', '=', $this->id))->
		order_by('primary', 'DESC')->find_all()->as_array();
	}
	
	public function get_orders() {
		return ORM::factory('widget_order')->where('user_id', '=', $this->id)->order_by('date', 'DESC')->find_all()->as_array();
	}
	
	public function delete($id = NULL) {
		$this->id = ($id)? $id : $this->id;
		foreach ($this->get_orders() as $key => $order) {
			$order->delete();
		}
		parent::delete($id);
	}
*/
	
	
} // End Model_Admin_User