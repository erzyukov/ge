<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Admin_User extends ORM {

	protected $_table_name = 'gsys_manage_user';
	
	// Relationships
	protected $_belongs_to = array
		(
			'group' => array('model' => 'admin_group', 'foreign_key' => 'group_id')
		);

	// Rules
	protected $_rules = array
	(
		'login'			=> array
		(
			'not_empty'		=> NULL,
			'min_length'		=> array(4),
			'max_length'		=> array(30),
			'regex'			=> array('/[a-zA-z_0-9]{3,30}/'),
		),
		'password'			=> array
		(
			'not_empty'		=> NULL,
			'min_length'		=> array(5),
			'max_length'		=> array(40),
		),
		'password_confirm'	=> array
		(
			'matches'		=> array('password'),
		),
		'name'			=> array
		(
			'not_empty'		=> NULL,
			'min_length'		=> array(4),
			'max_length'		=> array(50),
		),
	);
		
	// Columns to ignore
	protected $_ignored_columns = array('password_confirm');

	/**
	 * Validates login information from an array, and optionally redirects
	 * after a successful login.
	 *
	 * @param  array    values to check
	 * @param  string   URI or URL to redirect to
	 * @return boolean
	 */
	public function login(array & $array, $redirect = FALSE){
		$array = Validate::factory($array)
			->filter(TRUE, 'trim')
			->rules('login', $this->_rules['login'])
			->rules('password', $this->_rules['password']);

		// Login starts out invalid
		$status = FALSE;

		if ($array->check()){
			// Attempt to load the user
			$this->where('login', '=', $array['login'])
				->where('isactive', '=', 1)
				->find();

			if ($this->loaded() AND Manageauth::instance()->login($this, $array['password'])){

				if (is_string($redirect)){
					// Redirect after a successful login
					Request::instance()->redirect($redirect);
				}

				// Login is successful
				$status = TRUE;
			}
			else{
				$array->error('login', 'invalid');
			}
		}

		return $status;
	}
	
	
	/**
	 * Validates an array for a matching password and password_confirm field.
	 *
	 * @param  array    values to check
	 * @param  string   save the user if
	 * @return boolean
	 */
	public function change_password(array $array, $save = FALSE)
	{
		$array = Validate::factory($array)
			->filter(TRUE, 'trim')
			->rules('password', $this->_rules['password'])
			->rules('password_confirm', $this->_rules['password_confirm']);

		if ($status = $array->check())
		{
			// Change the password
			$this->password = $array['password'];

			if ($save !== FALSE AND $status = $this->save())
			{
				if (is_string($save))
				{
					// Redirect to the success page
					Request::instance()->redirect($save);
				}
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

  	/**
	 * Saves the current object. Will hash password if it was changed
	 *
	 * @chainable
	 * @return  $this
	 */
	public function save(){
		
		if (array_key_exists('password', $this->_changed))
			$this->_object['password'] = Manageauth::instance()->hash_password($this->_object['password'], Manageauth::instance()->find_salt($this->_object['password']));

		return parent::save();
	}
	
	
	
	
	
	
	
	
	
	
	
} // End Model_Admin_User