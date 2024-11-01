<?php

   //wp_deregister_script('jquery');
   wp_register_script('jquery', ("http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"), false, '1.4.2');
   if(!is_admin()){
       wp_enqueue_script('jquery', null, null, '1.4.2', true);
       wp_register_script('jqueryui', ("http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.9/jquery-ui.min.js"), true, '1.8.9');
       wp_enqueue_script('jqueryui', null, null, '1.8.9', true);
   }

/*-----------------------------------*/
function wai_add_options_pageo() {
	global $wai_dir, $wai_base, $wai_upload_base, $text_domain, $map_character_encoding;
	//$api=get_option('store_locator_api_key');
	//add_menu_page('Edit Locations', 'View Locations', 9, '$wai_dir/options-store-locator.php');
	add_menu_page(__("Where Am I", $text_domain), __("Where Am I", $text_domain), 9, $wai_dir.'/news-upgrades.php');
            add_submenu_page($wai_dir.'/news-upgrades.php', __("News & Upgrades", $text_domain), __("News & Upgrades", $text_domain), 9, $wai_dir.'/news-upgrades.php');
            add_submenu_page($wai_dir.'/news-upgrades.php', __("Manage Address", $text_domain), __("Manage Address", $text_domain), 9, $wai_dir.'/address.php');
            add_submenu_page($wai_dir.'/news-upgrades.php', __("Edit Pointer Location", $text_domain), __("Edit Pointer Location", $text_domain), 9, $wai_dir.'/edit-locations.php');
            add_submenu_page($wai_dir.'/news-upgrades.php', __("Map Designer", $text_domain), __("Map Designer", $text_domain), 9, $wai_dir.'/map-designer.php');
            add_submenu_page($wai_dir.'/news-upgrades.php', __("Localization", $text_domain)." &amp; ".__("Google API Key", $text_domain),  __("Localization", $text_domain)." &amp; ".__("Google API Key", $text_domain), 9, $wai_dir.'/api-key.php');
            add_submenu_page($wai_dir.'/news-upgrades.php', __("ReadMe", $text_domain), __("ReadMe", $text_domain), 9, $wai_dir.'/readme.php');
            //add_submenu_page($wai_dir.'/news-upgrades.php', 'Export Locations', 'Generate CSV Import File [+]', 9, $wai_dir.'/export-locations.php');
            //add_submenu_page($wai_dir.'/news-upgrades.php', 'Statistics', 'Statistics [+]', 9, $wai_dir.'/statistics.php');
}
?>
