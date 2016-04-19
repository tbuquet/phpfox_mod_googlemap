<?php
defined('PHPFOX') or exit('NO DICE!');
class Gmap_Component_Controller_Index extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
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
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
	}
}

?>