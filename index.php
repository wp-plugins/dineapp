<?php
/*
Plugin Name: DineApp
Plugin URI: http://www.dineapp.com/
Description: Restaurant online booking service.
Version: 1.0
Author: DineApp Inc.
Author URI: http://www.dineapp.com/
*/
require_once dirname(__FILE__) . '/config/config.php';
require_once dirname(__FILE__) . '/admin/DineApp.php';

// register sidebar
// TODO: check if sidebar widget is enabled

function da_register_booking_widget() {
    $widget_option = array(
        'description' => 'DineApp Widget provide online booking functions'
    );

    $widget_params = array(
        'DINEAPP_CONFIG' => $GLOBALS['DINEAPP']['config']
    );

    wp_register_sidebar_widget(
        'DineApp',        // your unique widget id
        'Online Booking (by DineApp)',          // widget name
        'dineapp_widget_display',  // callback function
        $widget_option,
        $widget_params
    );
}

function dineapp_widget_display($args, $params) {
   extract($args);
   if (function_exists('da_get_option') && da_get_option('show_widget') == 1) {
       // get widget configs
       $DINEAPP_CONFIG = $params['DINEAPP_CONFIG'];
       $widgetCode = da_get_option('widget_code');
       $serverUrl = da_get_option('server_url');

       // display widget
       echo $before_widget;
       echo $before_title . $widget_name . $after_title;
       echo $after_widget;

       // print some HTML for the widget to display here
       echo <<<EOD
    <iframe src="{$DINEAPP_CONFIG['SIDEBAR_WIDGET_URL']}?code={$widgetCode}" width="350" height="410" border="0" marginwidth="0" marginheight="0" frameborder="0"></iframe>
EOD;
   }
}


// admin page
// add_action('admin_menu', array('DineApp', 'main'));
add_action('admin_menu', 'da_add_admin_page');
da_register_booking_widget();
