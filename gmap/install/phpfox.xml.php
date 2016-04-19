<module>
	<data>
		<module_id>gmap</module_id>
		<product_id>gmap</product_id>
		<is_core>0</is_core>
		<is_active>1</is_active>
		<is_menu>1</is_menu>
		<menu><![CDATA[a:1:{s:16:"gmap.module_gmap";a:1:{s:3:"url";a:1:{i:0;s:4:"gmap";}}}]]></menu>
		<phrase_var_name>module_gmap</phrase_var_name>
	</data>
	<blocks>
		<block type_id="0" m_connection="gmap.index" module_id="gmap" component="filter" location="3" is_active="1" ordering="1" disallow_access="" can_move="0">
			<title>Google Map</title>
			<source_code />
			<source_parsed />
		</block>
	</blocks>
	<hooks>
		<hook module_id="custom" hook_type="component" module="custom" call_name="custom.component_ajax_updatefields__1" added="1461048392" version_id="3.8.0" />
	</hooks>
	<components>
		<component module_id="gmap" component="gmap" m_connection="gmap.index" module="gmap" is_controller="1" is_block="0" is_active="1" />
		<component module_id="gmap" component="filter" m_connection="" module="gmap" is_controller="0" is_block="1" is_active="1" />
	</components>
	<phrases>
		<phrase module_id="gmap" version_id="3.8.0" var_name="module_gmap" added="1461048392">Google Map</phrase>
		<phrase module_id="gmap" version_id="3.8.0" var_name="search" added="1461048392">Search</phrase>
		<phrase module_id="gmap" version_id="3.8.0" var_name="focus_on" added="1461048392">Focus on</phrase>
		<phrase module_id="gmap" version_id="3.8.0" var_name="friends" added="1461048392">Friends</phrase>
	</phrases>
	<install><![CDATA[
		$sTable = Phpfox::getT('gmap');
		$sql = "CREATE TABLE IF NOT EXISTS `" . $sTable . "` (
			`user_id` int(10) unsigned NOT NULL,
			`lat` float(10,6) DEFAULT NULL,
			`lng` float(10,6) DEFAULT NULL,
			`address` text NOT NULL,
			`not_found` tinyint(4) NOT NULL,
			PRIMARY KEY (`user_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";

		Phpfox::getLib('phpfox.database')->query($sql);
		
		$sTable = Phpfox::getT('gmap_countries');
		$sql = "CREATE TABLE IF NOT EXISTS `" . $sTable . "` (
			`country_iso` varchar(2) NOT NULL,
			`lat` float(10,6) DEFAULT NULL,
			`lng` float(10,6) DEFAULT NULL,
			`northeast_lat` float(10,6) DEFAULT NULL,
			`northeast_lng` float(10,6) DEFAULT NULL,
			`southwest_lat` float(10,6) DEFAULT NULL,
			`southwest_lng` float(10,6) DEFAULT NULL,
			PRIMARY KEY (`country_iso`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";

		Phpfox::getLib('phpfox.database')->query($sql);		
	]]></install>
</module>