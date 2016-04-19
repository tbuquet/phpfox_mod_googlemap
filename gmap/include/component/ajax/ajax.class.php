<?php
defined('PHPFOX') or exit('NO DICE!');
class Gmap_Component_Ajax_Ajax extends Phpfox_Ajax
{
	public function startsWith($haystack, $needle)
	{
		return $needle === "" || strpos($haystack, $needle) === 0;
	}
	
	public function getUsersAutoComplete(){
		$aUsers = Phpfox::getService('gmap')->getAllLocations();
		$sSearchString = mb_strtolower($this->get('startsWith'));
		
		$aOutput = array();
		
		foreach($aUsers as $aCountries)
		{
			foreach($aCountries as $aUser)
			{
				if($this->startsWith(mb_strtolower($aUser['full_name']), $sSearchString))
				{
					$aEntity['label'] = html_entity_decode($aUser['full_name']);
					$aEntity['value'] = $aUser['user_name'];
					$aOutput[] = $aEntity;
				}
			}
		}
		
		$this->call(json_encode($aOutput));
	}
}

?>
