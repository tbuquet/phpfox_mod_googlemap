<?php
defined('PHPFOX') or exit('NO DICE!');
$aUser = $this->get('val');
Phpfox::getService('gmap')->refreshUserGeolocation(Phpfox::getUserId(), $aUser['country_iso'], $aUser['city_location'], $aUser['postal_code']);
?>