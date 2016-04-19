<?php
defined('PHPFOX') or exit('NO DICE!');
class Gmap_Component_Block_Filter extends Phpfox_Component
{
	/**
	 * Class process method wnich is used to execute this component.
	 */
	public function process()
	{	
		$aAllCountries = Phpfox::getService('gmap')->getAllCountriesLocations();
		$aSelfUser = Phpfox::getService('user')->getUser(Phpfox::getUserId());
	
		$aRows = Phpfox::getService('friend')->get('friend.is_page = 0 AND uf.city_location != \'\' AND friend.user_id = ' . Phpfox::getUserId(), 'u.full_name ASC', 0, '', false);
		
		$iTotalPeople = 0;
		if($aAllCountries != null && is_array($aAllCountries))
		{
			foreach($aAllCountries as $aCountry)
			{
				$iTotalPeople += (int)$aCountry['total_people'];
			}
		}
		
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
	 * Garbage collector. Is executed after this class has completed
	 * its job and the template has also been displayed.
	 */
	public function clean()
	{
	}
}

?>