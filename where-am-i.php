<?php
/*
Plugin Name: Where Am I
Plugin URI: http://www.duncanbrown.me.uk/wordpress/where-am-i-plugin/
Description: A widget to show the location of a bricks and morter store or location, including a google map.
Version: 1.4
Author: Duncan Brown
Author URI: http://www.duncanbrown.me.uk
License: GPL2
*/
?>
<?php
/*  Copyright 2010  Duncan Brown  (email : shout@duncanbrown.me.uk)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
?>
<?php
include_once("variables.wai.php");
include_once("functions.wai.php");



//register_activation_hook( __FILE__, 'wai_activate' );
add_action('admin_init', 'wai_init' );
add_action('admin_menu', 'wai_options_add_page');
add_action('update_option_wai', 'wai_get_geocode');


function wai_init(){
    register_setting( 'wai_options', 'wai' );
}

// Add menu page
function wai_options_add_page() {
    add_options_page('Where Am I', 'Where Am I', 'administrator', 'wai_options',  'wai_options_do_page');
}

function wai_get_geocode($wai){
    if($_POST['update-loc']){
        $options = get_option('wai');
        $url = 'http://maps.googleapis.com/maps/api/geocode/';
        $search = $options['street'].' '.$options['other'].' '.$options['city'].' '.$options['county'].' '.$options['postcode'].' '.$options['country'];
        $region = $options['region'];
        $url = sprintf('%s%s?address=%s&sensor=false&region=%s',
           $url,
           'xml',
           urlencode($search),
           $region
        );
        if (_iscurlinstalled()){
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);
            $xml      = new SimpleXMLElement($response);
            $status   = $xml->status;
            if($status == 'OK'){
                $lat = (string) $xml->result->geometry->location->lat;
                $lng = (string) $xml->result->geometry->location->lng;
                $wai['lat'] = $lat;
                $wai['lon'] = $lng;
                update_option('wai',$wai);
            }
        }
    }
}

// Draw the menu page itself
function wai_options_do_page() {
wp_enqueue_script('jquery', null, null, '1.4.2', true);
global $wai_regions;

?>
<style type="text/css" media="screen">
    .wai-wrap{
        width: 100%;
        display: inline-block;
    }
    .wai-first{
        width: 49%;
        float: left;
    }
    .wai-second{
        width: 49%;
        float: right;
    }
    .wai-wrap label {
        float: left;
        width: 10em;
        margin-right: 1em;
    }
    form#wai-options input{
        margin-bottom: 5px;
}
</style>
    <div class="wrap">
        <h2>Where Am I Options</h2>
        <form method="post" action="options.php" id="wai-options">
            <?php settings_fields('wai_options'); ?>
            <?php $options = get_option('wai'); ?>
        <div class="wai-wrap">
            <div class="wai-first">
                <div>
                    <label for="wai[location-name]">Location Name</label>
                    <input type="text" name="wai[location-name]" value="<?php echo $options['location-name']; ?>" />
                </div>
                <div>
                    <label for="street">Street</label>
                    <input type="text" id="street" name="wai[street]" value="<?php echo $options['street'] ?>" />
                </div>
                <div>
                    <label for="other">Other</label>
                    <input type="text" id="other" name="wai[other]" value="<?php echo $options['other'] ?>" />
                </div>
                <div>
                    <label for="city">City</label>
                    <input type="text" id="city" name="wai[city]" value="<?php echo $options['city'] ?>" />
                </div>
                <div>
                    <label for="county">County</label>
                    <input type="text" id="county" name="wai[county]" value="<?php echo $options['county'] ?>" />
                </div>
                <div>
                    <label for="postcode">Postcode</label>
                    <input type="text" id="postcode" name="wai[postcode]" value="<?php echo $options['postcode'] ?>" />
                </div>
                <div>
                    <label for="country">Country</label>
                    <input type="text" id="country" name="wai[country]" value="<?php echo $options['country'] ?>" />
                </div>
                <div>
                    <label for="phone">Phone</label>
                    <input type="text" id="country" name="wai[phone]" value="<?php echo $options['phone'] ?>" />
                </div>
                <div>
                    <label for="wai[region]">Region</label>
                    <?php
                        echo "<select id='wai-region' name='wai[region]'>";
                        foreach($wai_regions as $region => $code) {
                            if(!empty($options['region'])){
                                $selected = ($options['region']==$code) ? 'selected="selected"' : '';
                            }else{
                                $selected = '';
                            }
                                echo "<option value='$code' $selected>$region</option>";
                        }
                        echo "</select>";
                    ?>
                </div>
                <?php if (_iscurlinstalled()){ ?>
                <div>
                    <label for="update-loc">Update Location</label>
                    <input name="update-loc" type="checkbox" value="1" />
                    <em>Select if changing the address.</em>
                </div>
                <?php
                    }else{
                        echo "<span style='background-color: red; weight: strong;'>cURL</span> is NOT installed, enter location coordinates manually on right.";
                    }
                ?>
                <div class="donate">
                    <p>This plugin takes up a great deal of my free time, and I
                        don't get paid for any of the time I put into making
                        fixes and adding features. If you can, please donate.
                        Any contribution will help keep Where Am I plugin
                        up-to-date.</p>
                    <div>
                        <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
                        <input type="hidden" name="cmd" value="_s-xclick">
                        <input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----">
                        <input type="image" src="https://www.paypal.com/en_US/GB/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online." style="margin:auto;display: block;">
                        <img alt="" border="0" src="https://www.paypal.com/en_GB/i/scr/pixel.gif" width="1" height="1">
                        </form>
                    </div>

                </div>
            </div>
            <div class="wai-second">
                <?php
                    if (!empty($options['lat'])  && $options['lon']) {
                ?>
                <script type="text/javascript"
                    src="http://maps.google.com/maps/api/js?sensor=false">
                </script>
                <script type="text/javascript">
                 jQuery.noConflict();
                 $ = jQuery;
                 $(document).ready(function() {
                     lat = $("#wai-lat");
                     lon = $("#wai-lon");
                    var latlng = new google.maps.LatLng(<?php echo $options['lat'] ?>, <?php echo $options['lon'] ?>);
                    var myOptions = {
                      zoom: 15,
                      center: latlng,
                      mapTypeId: google.maps.MapTypeId.ROADMAP
                    };
                    var map = new google.maps.Map(document.getElementById("map_canvas"),
                        myOptions);
                    var marker = new google.maps.Marker({
                      position: latlng,
                      map: map,
                      draggable: true,
                      title:"<?php echo $options['location-name'] ?>"
                    });
                    var contentString = '<div id="content">'+
                        '<h4><?php echo $options['location-name'] ?></h4>'+
                        '<p><?php echo $options['street'] ?></p>'+
                        <?php if(!empty($options['other'])){ ?>
                        '<p><?php echo $options['street'] ?></p>'+
                        <?php } ?>
                        '<p><?php echo $options['city'] ?></p>'+
                        '<p><?php echo $options['county'] ?></p>'+
                        '<p><?php echo $options['postcode'] ?></p>'+
                        '<p><?php echo $options['country'] ?></p>'+
                        '<p><strong>Phone:</strong> <?php echo $options['phone'] ?></p>'+
                        '</div>'
                    var infowindow = new google.maps.InfoWindow({
                        content: contentString
                    });
                    google.maps.event.addListener(marker, 'click', function() {
                      infowindow.open(map,marker);
                    });
                  google.maps.event.addListener(marker, 'dragend', function() {
                      pos = marker.getPosition();
                      lat.val(pos.lat());
                      lon.val(pos.lng())
                  });
                 });
                 </script>
                <div><strong><em><?php echo __('Drag marker to adjust the exact position, when correct click Save Changes.') ?></em></strong></div>
                <div id="map_canvas" style="width:500px; height:500px"></div>
                <?php
                    }else{
                        echo 'No map available';
                    }
                ?>
                <div>
                    <label for="wai[lat]"><?php echo __('Latitude') ?></label>
                    <input type="text" id="wai-lat" name="wai[lat]" value="<?php echo $options['lat'] ?>" />
                </div>
                <div>
                    <label for="wai[lon["><?php echo __('Longitude') ?></label>
                    <input type="text" id="wai-lon" name="wai[lon]" value="<?php echo $options['lon'] ?>" />
                </div>
            </div>
        </div>

            <p class="submit">
            <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
            </p>
        </form>
    </div>
<?php
}
/**
 * FooWidget Class
 */
class WaiWidget extends WP_Widget {
    /** constructor */
    function WaiWidget() {
        parent::WP_Widget(false, $name = 'Where Am I');
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {
        extract( $args );

        $title = apply_filters('widget_title', $instance['title']);
        $data = get_option('wai');
        ?>
              <?php echo $before_widget; ?>
                  <?php if ( $title )
                        echo $before_title . $title . $after_title; ?>
                <div class="wai-widget">
                    <dl>
                        <dt><?php echo $data['location-name'] ?></dt>
                        <dd><?php echo $data['street'] ?></dd>
                        <?php if(!empty($data['other'])){ ?>
                            <dd><?php echo $data['other']; ?></dd>
                        <?php } ?>
                        <dd><?php echo $data['city'] ?></dd>
                        <dd><?php echo $data['county'] ?></dd>
                        <dd><?php echo $data['postcode'] ?></dd>
                        <dd><?php echo $data['country'] ?></dd>
                    </dl>
                        <?php if(!empty($data['lat']) && !empty($data['lon'])){ ?>
                            <a id="wai-show-map" href="http://maps.google.com/maps?ll=<?php echo $data['lat'] ?>,<?php echo $data['lon'] ?>&amp;t=m">View Map</a>
                            <script type="text/javascript"
                                src="http://maps.google.com/maps/api/js?sensor=false">
                            </script>
                            <script type="text/javascript">
                                 jQuery.noConflict();
                                 $ = jQuery;
                                $('<link rel="stylesheet" type="text/css" href="<?php echo get_bloginfo('wpurl') ?>/wp-content/plugins/where-am-i/css/jquery-ui-1.8.5.custom.css" >')
                                   .appendTo("head");
                                $(function() {
                                    $( "#wai-map-dialog" ).dialog({
                                        autoOpen: false,
                                        position: 'center',
                                        minHeight: 500,
                                        minWidth: 500,
                                        resizable: false,
                                        modal:true
                                    });
                                    $(function() {
                                        var i = 0;
                                        $( "a", ".wai-widget" ).button();
                                        $( "#wai-show-map" ).click(function() {
                                            if(i != 1){
                                    var latlng = new google.maps.LatLng(<?php echo $data['lat'] ?>, <?php echo $data['lon'] ?>);
                                    var myOptions = {
                                      zoom: 15,
                                      center: latlng,
                                      mapTypeId: google.maps.MapTypeId.ROADMAP
                                    };
                                    var map = new google.maps.Map(document.getElementById("wai-map-dialog"),
                                        myOptions);
                                    var marker = new google.maps.Marker({
                                      position: latlng,
                                      map: map,
                                      title:"<?php echo $data['location-name'] ?>"
                                    });
                                    var contentString = '<div id="content">'+
                                        '<h4><?php echo $data['location-name'] ?></h4>'+
                                        '<p><?php echo $data['street'] ?></p>'+
                                        <?php if(!empty($data['other'])){ ?>
                                        '<p><?php echo $data['street'] ?></p>'+
                                        <?php } ?>
                                        '<p><?php echo $data['city'] ?></p>'+
                                        '<p><?php echo $data['county'] ?></p>'+
                                        '<p><?php echo $data['postcode'] ?></p>'+
                                        '<p><?php echo $data['country'] ?></p>'+
                                        '<p><strong>Phone:</strong> <?php echo $data['phone'] ?></p>'+
                                        '</div>'
                                    var infowindow = new google.maps.InfoWindow({
                                        content: contentString
                                    });
                                    google.maps.event.addListener(marker, 'click', function() {
                                      infowindow.open(map,marker);
                                    });
                                    i = 1;
                                    }
                                    $( "#wai-map-dialog" ).dialog( "open" );
                                    return false;
                                        });
                                    });
                                });
                            </script>

                        <?php } ?>
                    <?php if(!empty($data['phone'])){ ?>
                    <dl>
                        <dt><?php echo __('Phone').':' ?></dt>
                        <dd><?php echo $data['phone'] ?></dd>
                    </dl>
                    <?php } ?>
                    <div id="wai-map-dialog"></div>
                </div>
              <?php echo $after_widget; ?>
        <?php
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {
	$instance = $old_instance;
	$instance['title'] = strip_tags($new_instance['title']);
        return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {
        $title = esc_attr($instance['title']);
        ?>
            <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
        <?php
    }

} // class FooWidget
// register FooWidget widget
add_action('widgets_init', create_function('', 'return register_widget("WaiWidget");'));

function _iscurlinstalled() {
	if  (in_array  ('curl', get_loaded_extensions())) {
		return true;
	}
	else{
		return false;
	}
}
//Add a link to 'Settings' on the plugin listings page
function wai_settings_link( $links, $file ) {
 	if( $file == 'where-am-i/where-am-i.php' && function_exists( "admin_url" ) ) {
		$settings_link = '<a href="' . admin_url( 'options-general.php?page=wai_options' ) . '">' . __('Settings') . '</a>';
		array_unshift( $links, $settings_link ); // before the other links
	}
	return $links;
}
add_filter( 'plugin_action_links', 'wai_settings_link', 9, 2 );
// [bartag foo="foo-value"]
function whereami_func($atts) {
    $data = get_option('wai');
    $url = get_bloginfo('wpurl');
    $name = $data['location-name'];
    $street = $data['street'];
    $city = $data['city'];
    $county = $data['county'];
    $postcode = $data['postcode'];
    $country = $data['country'];
    if(!empty($data['other'])){
        $other = $data['other'];
        $other = "<dd>$other</dd>";
    }else{
        $other = '';
    }
    if(!empty($data['phone'])){
        $phone = $data['phone'];
    }
    if(!empty($data['lat']) && !empty($data['lon'])){
        $lat = $data['lat'];
        $lon = $data['lon'];
        $map = "<p><a id=\"wai-show-map\" href=\"http://maps.google.com/maps?ll=$lat,$lon&amp;t=m\">View Map</a></p>
                <script type=\"text/javascript\" src=\"http://maps.google.com/maps/api/js?sensor=false\"></script>
                <script type=\"text/javascript\">
                     jQuery.noConflict();
                     $ = jQuery;
                    $('<link rel=\"stylesheet\" type=\"text/css\" href=\"$url/wp-content/plugins/where-am-i/css/jquery-ui-1.8.5.custom.css\" >')
                       .appendTo(\"head\");
                    $(function() {
                        $( \"#wai-map-dialog\" ).dialog({
                            autoOpen: false,
                            position: 'center',
                            minHeight: 600,
                            minWidth: 600,
                            resizable: false,
                            modal:true
                        });
                        $(function() {
                            var i = 0;
                            $( \"a\", \".wai-widget\" ).button();
                            $( \"#wai-show-map\" ).click(function() {
                                if(i != 1){
                        var latlng = new google.maps.LatLng($lat,$lon);
                        var myOptions = {
                          zoom: 15,
                          center: latlng,
                          mapTypeId: google.maps.MapTypeId.ROADMAP
                        };
                        var map = new google.maps.Map(document.getElementById(\"wai-map-dialog\"),
                            myOptions);
                        var marker = new google.maps.Marker({
                          position: latlng,
                          map: map,
                          title:\"$name\"
                        });
                        var contentString = '<div id=\"content\">'+
                            '<dl>'+
                                '<dt>$name</dt>'+
                                '<dd>$street</dd>'+
                                '$other'+
                                '<dd>$city</dd>'+
                                '<dd>$county</dd>'+
                                '<dd>$postcode</dd>'+
                                '<dd>$country</dd>'+
                            '</dl>'+
                            '<p><strong>Phone: </strong>$phone</p>'+
                            '</div>'
                        var infowindow = new google.maps.InfoWindow({
                            content: contentString
                        });
                        google.maps.event.addListener(marker, 'click', function() {
                          infowindow.open(map,marker);
                        });
                        i = 1;
                        }
                        $( \"#wai-map-dialog\" ).dialog( \"open\" );
                        return false;
                            });
                        });
                    });
                </script>
                <style type=\"text/css\" media=\"screen\">
                    #wai-map-dialog{
                        text-align: left;
                    }
                    #wai-map-dialog dl{
                        margin: 0;
                        margin-left: 0;
                        padding-bottom: 0.5em;
                    }
                    #wai-map-dialog dt{
                        font-weight: bold;
                    }
                </style>
                    <div id=\"wai-map-dialog\"></div>";
    }else{
        $map = null;
    }
    $code =
        "<dl>
            <dt>$name</dt>
            <dd>$street</dd>".
            $other
            ."<dd>$city</dd>
            <dd>$county</dd>
            <dd>$postcode</dd>
            <dd>$country</dd>
        </dl>";
    return $code . $map;
}
add_shortcode('where-am-i', 'whereami_func');
?>