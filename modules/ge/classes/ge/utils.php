<?php defined('SYSPATH') OR die('No direct access allowed.');

	/**
	 * Вспомогательные функции
	 * 
	 * @package GE2
	 * @author Oliver (aka Erzukov Aleksey)
	 * @copyright  (c) 2007-2010 Global Ltd.
	 * @license    http://global.su/license
	 */


class GE_Utils  {
	
	
	public static function translit_url($string){
		return '';
		
		//TODO потом доделать (translit_url)!!!
		// тупость какая-то.. замуты с кодировкой
		// надо думать что с этим делать
		
		if (mb_check_encoding($string, 'Windows-1251'))
			 $string = iconv('Windows-1251', 'UTF-8', $string);
//echo Kohana::debug(mb_check_encoding($string, 'UTF-8'));
//echo 'Новый Текстовый Раздел';
		$result = '';
		$len = mb_strlen(mb_strtolower($string));
		for ($i = 0; $i < $len; $i++){
			
			$result .= self::translit_url_letter($string[$i]);
		}
		return $result;
	}
	
	public static function translit_url_letter($letter){
		
//echo Kohana::debug(mb_check_encoding($letter, 'UTF-8')); //($letter == iconv('UTF-8', 'Windows-1251', 'о'))? 1: 0;
		switch ($letter){
			case "щ":
				return 'sch';
			case "ч":
				return 'ch';
			case "ш":
				return 'sh';
			case "я":
				return 'ja';
			case "ю":
				return 'ju';
			case "ё":
				return 'jo';
			case "ж":
				return 'zh';
			case "э":
				return 'e';
			case "а":
				return 'a';
			case "б":
				return 'b';
			case "ц":
				return 'c';
			case "д":
				return 'd';
			case "е":
				return 'e';
			case "ф":
				return 'f';
			case "г":
				return 'g';
			case "х":
				return 'h';
			case "и":
				return 'i';
			case "й":
				return 'j';
			case "к":
				return 'k';
			case "л":
				return 'l';
			case "м":
				return 'm';
			case "н":
				return 'n';
			case "о":
				return 'o';
			case "п":
				return 'p';
			 case "р":
				return 'r';
			case "с":
				return 's';
			case "т":
				return 't';
			case "у":
				return 'u';
			case "в":
				return 'v';
			case "ы":
				return 'i';
			case "з":
				return 'z';
			case "Ф":
				return 'f';
			case " ":
				return '_';
			default:
				return '-';
		}
	}
	
}