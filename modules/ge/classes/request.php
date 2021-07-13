<?php defined('SYSPATH') or die('No direct script access.');

	/**
	 * Перекрытие Kohana_Request для нужд движка.
	 * 
	 * @package GE2
	 * @author Oliver (aka Erzukov Aleksey)
	 * @copyright  (c) 2007-2010 Global Ltd.
	 * @license    http://global.su/license
	 */

class request extends Kohana_Request {

	public function execute(){

		$request = NULL;

		try{
		    $request = parent::execute();
		}
		catch (GE_Exception_404 $e){
			$request = request::factory('error/404')->execute();
		}
		catch (GE_Exception_403 $e){
			$request = request::factory('error/403')->execute();
		}
		catch (ReflectionException $e){
			$request = request::factory('error/404')->execute();
		}
		catch (Exception $e){
			if ( ! GE::production() ){
				throw $e;
			}
			$request = request::factory('error/500')->execute();
		}
		
		return $request;
		
	}
	
	
}