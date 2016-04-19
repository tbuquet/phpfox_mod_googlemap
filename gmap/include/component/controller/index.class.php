<?php
defined('PHPFOX') or exit('NO DICE!');

/**
* Controller class for gmap
*
* @package	gmap
* @author	Thibault Buquet
* @link		https://github.com/tbuquet/phpfox_mod_googlemap/
* @version	1.0
*/
class Gmap_Component_Controller_Index extends Phpfox_Component
{
	/**
	* Load the required information for the page "gmap"
	*/
	public function process()
	{
		Phpfox::isUser(true);
		
		$this->template()->setBreadcrumb('Google Map');
		
		$username = $this->request()->get('req2');
		Phpfox::getService('gmap')->generateGoogleMap($username);
			
		$this->template()->setHeader(array(Phpfox::getService('gmap')->getGoogleMapJS()))
		->setHeader(array(
			'gmap.css' => 'module_gmap',
			'gmap.js' => 'module_gmap'		
			));
	}
	
	/**
	 * Garbage collector.
	 */
	public function clean()
	{
	}
}

?>