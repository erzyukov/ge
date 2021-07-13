<?php defined('SYSPATH') or die('No direct script access.');

	/**
	 * Класc управления пользователями панели администрирования.
	 * 
	 * @package Gengine
	 * @author Oliver (aka Erzukov Aleksey)
	 * @copyright  (c) 2007-2010 Global Ltd.
	 * @license    http://global.su/license
	 */
// TODO ! Расписать комментарии ко всем функциям класса
class Widget_User_Auth {
	
	private static $instance = NULL;
	protected $session = NULL;
	protected $config = array();
	
	public static function instance() {
		if (self::$instance === null) {
			self::$instance = new self;
			self::$instance->init();
		}
		return self::$instance;
	}
	
	private function init(){
		$this->config = Kohana::config('user');
		$this->config['salt_pattern'] = preg_split('/,\s*/', $this->config['salt_pattern']);
        $this->session = Session::instance('database');
	}
	
	
	
	
	
	/**
	 * Gets the currently logged in user from the session.
	 * Returns FALSE if no user is currently logged in.
	 *
	 * @return  mixed
	 */
	public function get_user()
	{
		if ($this->logged_in())
		{
			return $this->session->get($this->config['session_key']);
		}

		return FALSE;
	}
	
	/**
	 * Check if there is an active session. Optionally allows checking for a
	 * specific role.
	 *
	 * @return  boolean
	 */
	public function logged_in()
	{
		return (bool)$this->session->get($this->config['session_key'], FALSE);
	}
	
	/**
	 * Attempt to log in a user by using an ORM object and plain-text password.
	 *
	 * @param   string   username to log in
	 * @param   string   password to check against
	 * @return  boolean
	 */
	public function login($username, $password, $hashed = false)
	{
		if (empty($password))
			return FALSE;

		if (is_string($password) && !$hashed)
		{
			// Get the salt from the stored password
			//$salt = $this->find_salt($this->password($username));
			
			// Create a hashed password using the salt from the stored password
			$password = $this->hash_password($password);//, $salt
		}

		return $this->_login($username, $password);
	}
	
	/**
	 * Get the stored password for a username.
	 *
	 * @param   mixed   username
	 * @return  string
	 */
	public function password($user)
	{
		if ( ! is_object($user))
		{
			$username = $user;

			// Load the user
			$user = ORM::factory('widget_user');
			$user->where('login', "=", $username)->find();
		}

		return $user->password;
	}
	
	/**
	 * Log out a user by removing the related session variables.
	 *
	 * @param   boolean  completely destroy the session
	 * @return  boolean
	 */
	public function logout($destroy = FALSE)
	{
		if ($destroy === TRUE)
		{
			// Destroy the session completely
			Session::instance()->destroy();
		}
		else
		{
			// Remove the user from the session
			$this->session->delete($this->config['session_key']);

			// Regenerate session_id
			$this->session->regenerate();
		}

		// Double check
		return ! $this->logged_in();
	}
	
	
	/**
	 * Finds the salt from a password, based on the configured salt pattern.
	 *
	 * @param   string  hashed password
	 * @return  string
	 */
	public function find_salt($password)
	{
		$salt = '';

		foreach ($this->config['salt_pattern'] as $i => $offset)
		{
			// Find salt characters, take a good long look...
			$salt .= substr($password, $offset + $i, 1);
		}

		return $salt;
	}

	/**
	 * Creates a hashed password from a plaintext password, inserting salt
	 * based on the configured salt pattern.
	 *
	 * @param   string  plaintext password
	 * @return  string  hashed password string
	 */
	public function hash_password($password, $salt = FALSE)
	{
		if ($salt === FALSE)
		{
			// Create a salt seed, same length as the number of offsets in the pattern
			//$salt = substr($this->hash(uniqid(NULL, TRUE)), 0, count($this->config['salt_pattern']));
			$salt = $this->find_salt($password);
		}

		// Password hash that the salt will be inserted into
		$hash = $this->hash($salt.$password);

		// Change salt to an array
		$salt = str_split($salt, 1);

		// Returned password
		$password = '';

		// Used to calculate the length of splits
		$last_offset = 0;

		foreach ($this->config['salt_pattern'] as $offset)
		{
			// Split a new part of the hash off
			$part = substr($hash, 0, $offset - $last_offset);

			// Cut the current part out of the hash
			$hash = substr($hash, $offset - $last_offset);

			// Add the part to the password, appending the salt character
			$password .= $part.array_shift($salt);

			// Set the last offset to the current offset
			$last_offset = $offset;
		}

		// Return the password, with the remaining hash appended
		return $password.$hash;
	}
	
	/**
	 * Perform a hash, using the configured method.
	 *
	 * @param   string  string to hash
	 * @return  string
	 */
	public function hash($str)
	{
		return hash($this->config['hash_method'], $str);
	}
	
	/**
	 * Logs a user in.
	 *
	 * @param   string   username
	 * @param   string   password
	 * @return  boolean
	 */
	protected function _login($user, $password){
		if ( ! is_object($user)){
			$username = $user;

			// Load the user
			$user = ORM::factory('widget_user');
			$user->where('email', "=", $username)->where('isactive', '=', '1')->find();
		}

		// If the passwords match, perform a login
		if ($user->password === $password){
			// Finish the login
			$this->complete_login($user);

			return TRUE;
		}

		// Login failed
		return FALSE;
	}
	
	public function check_password($password){

		$salt = $this->find_salt($password);
		$password = $this->hash_password($password, $salt);
		$cur = $this->get_user()->as_array();
		if ($cur['password'] === $password){
			return true;
		}
		return false;
	}

	protected function complete_login($user){
		// Set the last login date
		$user->last_login = time();

		// Save the user
		$user->save();
		
		// Regenerate session_id
		$this->session->regenerate();

		// Store username in session
		$this->session->set($this->config['session_key'], $user);

		return TRUE;
	}
	
	private function __construct() {}
	
	private function __clone() {}
	
} // End Gengine_Admin_Auth