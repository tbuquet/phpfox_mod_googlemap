<?php 
defined('PHPFOX') or exit('NO DICE!'); 
?>
<div class="block">
	<div class="global_apps_title title">{phrase var='gmap.search'} ({$iTotalPeople} people)</div>
	<input id="markerValue" type="text" value="" style="width:110px"/><input onclick="markerZoomTo()" id="markerSearch" type="button" value="GO!"/>
</div>
<div class="block">
	<div class="global_apps_title title">{phrase var='gmap.focus_on'}</div>
	<div class="tag_cloud">
		<ul>
			{foreach from=$aAllCountries item=aCountry}
				    <li><a onclick="centerToCountry('{$aCountry.lat}', '{$aCountry.lng}', '{$aCountry.northeast_lat}', '{$aCountry.northeast_lng}', '{$aCountry.southwest_lat}', '{$aCountry.southwest_lng}')" href="javascript:;">{$aCountry.name} ({$aCountry.total_people})</a></li>
			{/foreach}
		</ul>
	</div>
	<div class="clear"></div>
</div>
<div class="block">
	<div class="global_apps_title title">{phrase var='gmap.friends'}</div>
	<div class="sub_section_menu global_apps_title_padding">
			{foreach from=$aFriends name=friend item=aFriend}
					{img user=$aFriend suffix='_50_square' href="javascript:;" onclick=$aFriend.onclick max_width=32 max_height=32}
			{/foreach}
	</div>
</div>