<?php defined('SYSPATH') or die('No direct script access.');

	/**
	 * Интерфейс для генераторов контента.
	 * 
	 * @package GE2
	 * @author Oliver (aka Erzukov Aleksey)
	 * @copyright  (c) 2007-2010 Global Ltd.
	 * @license    http://global.su/license
	 */

	interface GE_Interface_Content_Generator{
		
		/*
		 * Возвращает содержимое страницы
		 * 
		 * @return string
		 */
		public function content();
		
		/*
		 * Возвращает строку навигации страницы
		 * 
		 * @return string
		 */
		public function navigation();
		
		/*
		 * Возвращает заголовок страницы (<title>)
		 * 
		 * @return string
		 */
		public function title();
		
		/*
		 * Возвращает меню страницы
		 * 
		 * @return string
		 */
		public function menu();
		
		/*
		 * Возвращает ключевые слова страницы (<meta keywords>)
		 * 
		 * @return string
		 */
		public function keywords();
		
		/*
		 * Возвращает описание страницы (<meta description>)
		 * 
		 * @return string
		 */
		public function description();

		/*
		 * Возвращает пользовательские скрипты
		 * 
		 * @return string
		 */
		public function js();

		/*
		 * Возвращает пользовательские стили
		 * 
		 * @return string
		 */
		public function css();
		
	}