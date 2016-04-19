<?php
defined('PHPFOX') or exit('NO DICE!');

/**
* Service class for gmap
*
* @package	gmap
* @author	Thibault Buquet
* @link		https://github.com/tbuquet/phpfox_mod_googlemap/
* @version	1.0
*/
class Gmap_Service_Gmap extends Phpfox_Service 
{	
	protected $oGoogleMapService = null;

	/**
	 * Class constructor
	 */
	public function __construct()
	{
	}
	
	/**
	* Validate a few cases and requests the googlemap API to obtain the lat/lng of a user address and store it in the gmap SQL table as well as in cache
	*
	* @param userid			$iUserId 		User ID
	* @param countryiso		$sCountryIso	ISO code for the user country
	* @param city			$sCity			City of the user
	* @param postalcode		$sPostalCode	Postal Code of the user
	*
	*/
	public function refreshUserGeolocation($iUserId, $sCountryIso, $sCity, $sPostalCode)
	{
		$aUserRow = $this->getUserAddress($iUserId);
		
		//Address Not found
		if($aUserRow == null || !isset($sCity) || $sCity == '')
		{
			$this->database()->delete(Phpfox::getT('gmap'), 'user_id = \'' . $iUserId . '\'');
			$this->cache()->remove('gmap.locations', 'substr');
			$this->cache()->remove('gmap.countries', 'substr');
			return;
		}

		//Address Found
		//Get Country
		$aCountry = $this->database()->select('c.name as country')
			->from(Phpfox::getT('country'), 'c')		
			->where('c.country_iso = \'' . $sCountryIso . '\'')
			->execute('getSlaveRow');
		
		//Get Full Address
		$addressCode = $sCity . ' ' . $aCountry['country'] . ' ' . $sPostalCode;

		//Get User past location (if exists)
		$aUserLocation = $this->database()->select('f.*')
			->from(Phpfox::getT('gmap'), 'f')		
			->where('f.user_id = \'' . $iUserId . '\'')
			->execute('getSlaveRow');
			
		//Same address, don't do anything
		if($aUserLocation != null && $addressCode == $aUserLocation['address'])
			return;
			
		//Delete cache
		$this->cache()->remove('gmap.locations', 'substr');
		$this->cache()->remove('gmap.countries', 'substr');
		
		//Not same address, do something!
		$this->database()->delete(Phpfox::getT('gmap'), 'user_id = \'' . $iUserId . '\'');
		
		//Insert new value
		if(!isset($oGoogleMapService))
			$oGoogleMapService = Phpfox::getService('gmap.googlemap');

		$return = $oGoogleMapService->geocoding($addressCode);
		if(isset($return))
		{	
			$return[2] = ((float)$return[2]) + ((float)rand(-1000, 1000)) / 100000;
			$return[3] = ((float)$return[3]) + ((float)rand(-1000, 1000)) / 100000;
			$this->database()->insert(Phpfox::getT('gmap'), array(
					'user_id' => $iUserId,
					'lat' => $return[2],
					'lng' => $return[3],
					'address' => $addressCode,
					'not_found' => 0
				)
			);
		}
		else
		{
			$this->database()->insert(Phpfox::getT('gmap'), array(
					'user_id' => $iUserId,
					'address' => $addressCode,
					'not_found' => 1
				)
			);
		}
		
		//Check if new country
		$aCountryLocalisation = $this->database()->select('c.country_iso')
			->from(Phpfox::getT('gmap_countries'), 'c')		
			->where('c.country_iso = \'' . $sCountryIso . '\'')
			->execute('getSlaveRow');
		if($aCountryLocalisation == null)
		{
			//Create new one
			$diffBound = 0;
			if($sCountryIso == 'FR')
				$diffBound = 1;
			$return = $oGoogleMapService->geocoding($aCountry['country']);
			if(isset($return))
			{
				$this->database()->insert(Phpfox::getT('gmap_countries'), array(
						'country_iso' => $sCountryIso,
						'lat' => $return[2],
						'lng' => $return[3],
						'northeast_lat' => ((float)$return[4]['northeast']['lat']) - $diffBound,
						'northeast_lng' => ((float)$return[4]['northeast']['lng']) - $diffBound,
						'southwest_lat' => ((float)$return[4]['southwest']['lat']) + $diffBound,
						'southwest_lng' => ((float)$return[4]['southwest']['lng']) + $diffBound
					)
				);
			}
		}
	}
	
	
	/**
	* Create a list of all the user locations and sort them by countries
	*
	* @return $aOutput	List of all the user locations, sorted by country
	*/
	public function getAllLocations()
	{
		$sCacheId = $this->cache()->set('gmap.locations');
	
		if (!($aRows = $this->cache()->get($sCacheId)))
		{
			$aRows = $this->database()->select('f.*, u.full_name, u.user_name, u.country_iso')
				->from(Phpfox::getT('gmap'), 'f')		
				->join(Phpfox::getT('user'), 'u', 'u.user_id = f.user_id')	
				->where('f.not_found = \'0\'')
				->execute('getSlaveRows');
				
			$this->cache()->save($sCacheId, $aRows);
		}
		
		$aOutput = array();
		
		if($aRows != null && is_array($aRows))
		foreach($aRows as $aRow)
		{
			$aOutput[$aRow['country_iso']][] = $aRow;
		}
		return $aOutput;
	}
	
	/**
	* Create a list of all the countries of the user in the databse
	*
	* @return $aRows	List of all the user countries, sorted alphabetically
	*/
	public function getAllCountriesLocations()
	{
		$sCacheId = $this->cache()->set('gmap.countries');
	
		if (!($aRows = $this->cache()->get($sCacheId)))
		{
			$aRows = $this->database()->select('fc.country_iso, c.name, c.phrase_var_name, fc.*, COUNT(u.user_id) as total_people')
				->from(Phpfox::getT('gmap_countries'), 'fc')		
				->join(Phpfox::getT('country'), 'c', 'fc.country_iso = c.country_iso')	
				->join(Phpfox::getT('user'), 'u', 'u.country_iso = fc.country_iso')
				->join(Phpfox::getT('gmap'), 'f', 'u.user_id = f.user_id')
				->group('u.country_iso')
				->where('f.not_found = \'0\'')
				->execute('getSlaveRows');
				
			$this->cache()->save($sCacheId, $aRows);
		}
		
		if($aRows != null && is_array($aRows))
		{
			foreach($aRows as $key => $aRow)
			{
				if(isset($aRow['phrase_var_name']) && $aRow['phrase_var_name'] != '')
				{
					$aRows[$key]['name'] = Phpfox::getPhrase($aRow['phrase_var_name']);
				}
			}
			
			$aRows = $this->sortByProperty($aRows, 'name');
		}
		
		return $aRows;
	}
	
	/**
	* Sort an array of objects by a specific property of these objects
	*
	* @param array			$array 			Array to sort
	* @param propertyName	$propertyName 	Name of the property to use as a comparator
	*
	* @return $result	$array sorted by $propertyName
	*/
	public function sortByProp($array, $propertyName)
	{
		$sorted = [];

		foreach ($array as $item)
			$sorted[$item[$propertyName]][] = $item;

		ksort($sorted);
		$result = [];

		foreach ($sorted as $subArray) 
			foreach ($subArray as $item)
				$result[] = $item;

		return $result;
	}
	
	/**
	* Get a country localisation
	*
	* @param countryIso			$countryIso 			ISO code for the country
	*
	* @return $aRows	An array of the property of a country (name, lat/lng...)
	*/
	public function getCountryLocalisation($countryIso)
	{
		$aRows = $this->database()->select('c.country_iso, c.name, fc.*')
			->from(Phpfox::getT('gmap_countries'), 'fc')		
			->join(Phpfox::getT('country'), 'c', 'fc.country_iso = c.country_iso')	
			->where('c.country_iso = \'' . $countryIso . '\'')
			->execute('getSlaveRow');
		
		return $aRows;
	}
	
	/**
	* Get a user full address from the Phpfox user fields
	*
	* @param userID		$iUserId 		User ID
	*
	* @return $aRow		An array containing the name, country, city, postal code and country iso of a user
	*/
	public function getUserAddress($iUserId)
	{
		$aRow = $this->database()->select('u.user_id, c.name as country, uf.city_location as city, uf.postal_code, c.country_iso')
			->from(Phpfox::getT('user'), 'u')		
			->join(Phpfox::getT('user_field'), 'uf', 'uf.user_id = u.user_id')	
			->join(Phpfox::getT('country'), 'c', 'c.country_iso = u.country_iso')
			->where('u.user_id = \'' . $iUserId . '\'')
			->execute('getSlaveRow');
		
		//Making sure the information isnt private or limited to friends.
		$aFiltersOut = $this->database()->select('u.user_id')
			->from(Phpfox::getT('user'), 'u')		
			->join(Phpfox::getT('user_privacy'), 'up', 'up.user_id = u.user_id')		
			->where('up.user_privacy = \'profile.view_location\' AND up.user_value != 1 AND u.user_id = \'' . $iUserId . '\'')
			->execute('getSlaveRows');
			

		$found = false;
		foreach($aFiltersOut as $aFilter)
		{
			if($aFilter['user_id'] == $aRow['user_id'])
			{
				$found = true;
				break;
			}
		}
		if(!$found)
			return $aRow;
	}
	
	/**
	* Instanciate a googlemap map, configure it and generate its client side code.
	*
	* @param defaultUser		$defaultUser 		Username, user to zoom on the user at the start of the map
	*/
	public function generateGoogleMap($defaultUser = '')
	{
		if(!isset($oGoogleMapService))
			$oGoogleMapService = Phpfox::getService('gmap.googlemap');

		//Init params
		$oGoogleMapService->setDivId('gmap');
		$oGoogleMapService->setEnableWindowZoom(true);
		$oGoogleMapService->setSize('100%','100%');
		$oGoogleMapService->setLang('en');
		if($defaultUser != '')
			$oGoogleMapService->setDefaultMarker($defaultUser);
		$oGoogleMapService->setDefaultHideMarker(false);
		
		//Get country and viewport
		$aSelfUser = Phpfox::getService('user')->getUser(Phpfox::getUserId());
		$aUserCountryLocalisations = $this->getCountryLocalisation($aSelfUser['country_iso']);
		if($aUserCountryLocalisations != null)
			$oGoogleMapService->setCustomLocationAndViewport($aUserCountryLocalisations);
		
		//Get all localisations
		$aLocalisations = $this->getAllLocations();
		if($aLocalisations != null)
		{
			foreach($aLocalisations as $key => $aCountryUsers)
			{	
				$aformattedArray = array();
				foreach($aCountryUsers as $aData)
				{
					$aformattedArray[] = array($aData['lat'], $aData['lng'], $aData['full_name'], '<div id=\'js_user_tool_tip_cache_' . $aData['user_name'] . '\' style=\'min-width:250px;min-height:200px\'></div>', $aData['user_name']);
				}
				$oGoogleMapService->addArrayMarkerByCoords($aformattedArray,$key);
			}	
		}
		$oGoogleMapService->generate();
	}
	
	/**
	* Generate the JS code of the map
	*/
	public function getGoogleMapJS()
	{
		if(!isset($oGoogleMapService))
			$oGoogleMapService = Phpfox::getService('gmap.googlemap');
	
		return $oGoogleMapService->getGoogleMap();
	}
}

?>
