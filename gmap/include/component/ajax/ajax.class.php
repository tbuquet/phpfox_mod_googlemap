<?php
defined('PHPFOX') or exit('NO DICE!');

/**
* Class to handle the server side of the Ajax calls
*
* @package	gmap
* @author	Thibault Buquet
* @link		https://github.com/tbuquet/phpfox_mod_googlemap/
* @version	1.0
*/
class Gmap_Component_Ajax_Ajax extends Phpfox_Ajax
{
	/**
	* Check if a string starts with a specific needle
	*
	* @param input		$input 		string to analyse
	* @param needle		$needle		needle to be used for analysis
	*
	* @return true is $input starts with $needle, otherwise false
	*/
	public function startsWith($input, $needle)
	{
		return $needle === "" || strpos($input, $needle) === 0;
	}
	
	/**
	* Return a list of potential users that fits the current research of a user
	*
	* @param startsWith		URL parameter containing the string typed by the user
	*
	* @return $aOutput (via Ajax)
	*/
	public function getUsersAutoComplete(){
		$aCountries = Phpfox::getService('gmap')->getAllLocations();
		$sSearchString = mb_strtolower($this->get('startsWith'));
		
		$aOutput = array();
		
		if($aCountries != null && is_array($aCountries))
		{
			foreach($aCountries as $aUsers)
			{
				foreach($aUsers as $aUser)
				{
					if($this->startsWith(mb_strtolower($aUser['full_name']), $sSearchString))
					{
						$aEntity['label'] = html_entity_decode($aUser['full_name']);
						$aEntity['value'] = $aUser['user_name'];
						$aOutput[] = $aEntity;
					}
				}
			}
		}
		
		$this->call(json_encode($aOutput));
	}
}

?>
