<?php
defined('PHPFOX') or exit('NO DICE!');

/**
* Class that process the loading of the block "Filter"
*
* @package	gmap
* @author	Thibault Buquet
* @link		https://github.com/tbuquet/phpfox_mod_googlemap/
* @version	1.0
*/
class Gmap_Component_Block_Filter extends Phpfox_Component
{
	/**
	* Load the required information for the bloc "Filter"
	*/
	public function process()
	{	
		$aAllCountries = Phpfox::getService('gmap')->getAllCountriesLocations();
		$aSelfUser = Phpfox::getService('user')->getUser(Phpfox::getUserId());
	
		//Load a list of all the current user's friends
		$aRows = Phpfox::getService('friend')->get('friend.is_page = 0 AND uf.city_location != \'\' AND friend.user_id = ' . Phpfox::getUserId(), 'u.full_name ASC', 0, '', false);
		
		$iTotalPeople = 0;
		if($aAllCountries != null && is_array($aAllCountries))
		{
			foreach($aAllCountries as $aCountry)
			{
				$iTotalPeople += (int)$aCountry['total_people'];
			}
		}
		
		//Add a JS event to redirect the user to their friend's location on the map
		if($aRows != null && is_array($aRows))
		{
			foreach($aRows as $key => $aRow)
			{
				$aRows[$key]['onclick'] = 'markerZoomToName(\''.$aRows[$key]['user_name'].'\')';
			}
		}
		
		$this->template()->assign(array(
			'aAllCountries' => $aAllCountries,
			'iTotalPeople' => $iTotalPeople,
			'aUserCountry' => $aSelfUser,
			'aFriends' => $aRows
		));
		
		return 'block';
	}
	
	/**
	 * Garbage collector.
	 */
	public function clean()
	{
	}
}

?>