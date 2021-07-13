<?php defined('SYSPATH') OR die('No direct access allowed.');

return array
(
	'hash_method' => 'sha1',
	'salt_pattern' => '1, 3, 5, 9, 14, 15, 20, 21, 28, 30',
	'lifetime' => 3600,
	'log_try' => 5,
	'session_key' => 'auth_user'
);
