<?php
defined('PHPFOX') or exit('NO DICE!');

/**
* Hook used to update the googlemap lat/lon after an address update from a user
*
* @package	gmap
* @author	Thibault Buquet
* @link		https://github.com/tbuquet/phpfox_mod_googlemap/
* @version	1.0
*/

$aUser = $this->get('val');
Phpfox::getService('gmap')->refreshUserGeolocation(Phpfox::getUserId(), $aUser['country_iso'], $aUser['city_location'], $aUser['postal_code']);
?>