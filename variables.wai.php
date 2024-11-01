<?php
global $wai_dir, $wai_base, $wai_path, $wai_upload_path, $wai_upload_base, $wai_regions;
$text_domain="lol";
$wai_dir=dirname(plugin_basename(__FILE__)); //plugin absolute server directory name
$wai_base=get_option('siteurl')."/wp-content/plugins/".$wai_dir; //URL to plugin directory
//$wai_base=str_replace("http://$_SERVER[HTTP_HOST]", "", $wai_base);
$wai_path=ABSPATH."wp-content/plugins/".$wai_dir; //absolute server path to plugin directory
//$prev=$wpdb->get_results("select ID, guid from ".$wpdb->prefix."posts where post_content like '%[STORE-LOCATOR%' AND post_type<>'revision' LIMIT 1", ARRAY_A);
$view_link="| <a href='".get_option('siteurl')."/wp-admin/admin.php?page=$wai_dir/view-locations.php'>".__("Manage Locations", $text_domain)."</a>";// | <a href='{$prev[0][guid]}' target='_blank'>".__("Preview User Interface", $text_domain)."</a>";
$web_domain=$_SERVER['HTTP_HOST'];
$map_character_encoding=(get_option('wai_map_character_encoding')!="")? "&amp;oe=".get_option('wai_map_character_encoding') : "";
$wai_upload_path=ABSPATH."wp-content/uploads/sl-uploads"; //absolute server path to store locator uploads directory
$wai_upload_base=get_option('siteurl')."/wp-content/uploads/sl-uploads"; //URL to store locator uploads directory
$wai_regions = array(
    'Default' => '',
    'Austria' => 'AT',
    'Australia' => 'AU',
    'Bosnia and Herzegovina' => 'BA',
    'Belgium' => 'BE',
    'Brazil' => 'BR',
    'Canada' => 'CA',
    'Switzerland' => 'CH',
    'Czech Republic' => 'CZ',
    'Germany' => 'DE',
    'Denmark' => 'DK',
    'Spain' => 'ES',
    'Finland' => 'FI',
    'France' => 'FR',
    'Italy' => 'IT',
    'Japan' => 'JP',
    'Netherlands' => 'NL',
    'Norway' => 'NO',
    'New Zealand' => 'NZ',
    'Poland' => 'PL',
    'Russia' => 'RU',
    'Sweden' => 'SE',
    'Taiwan' => 'TW',
    'United Kingdom' => 'UK',
    'USA' => 'US'
);
?>