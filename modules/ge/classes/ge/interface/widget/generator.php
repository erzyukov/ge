<?php defined('SYSPATH') or die('No direct script access.');

	/**
	 * Интерфейс генератора данных для виджетов.
	 * 
	 * @package GE2
	 * @author Oliver (aka Erzukov Aleksey)
	 * @copyright  (c) 2007-2010 Global Ltd.
	 * @license    http://global.su/license
	 */

	interface GE_Interface_Widget_Generator{
		
		/*
		 * Возвращает данные виджета
		 * 
		 * @return array
		 */
		public function data();
		

	}