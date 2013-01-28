<?php
/*
Plugin Name: DineApp
Plugin URI: http://www.dineapp.com/
Description: Restaurant online booking service.
Version: 1.2.0
Author: DineApp Inc.
Author URI: http://www.dineapp.com/
*/
require_once dirname(__FILE__) . '/config/config.php';
require_once dirname(__FILE__) . '/class-dineapp.php';

// register admin page
add_action('admin_menu', array('DineApp', 'register_admin_panel'));

// register activation & deactivation hook
$pluginMainFile = WP_PLUGIN_DIR . '/dineapp/index.php';
register_activation_hook( $pluginMainFile, array('DineApp', 'on_activated'));
register_deactivation_hook($pluginMainFile, array('DineApp', 'on_deactivated'));

// register widget
DineApp::register_sidebar_widget();

