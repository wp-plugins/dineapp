<?php

$GLOBALS['DINEAPP'] = array();
$GLOBALS['DINEAPP']['config'] = array(
    // login URL
    // FIXME: use a seperated api subdomain. e.g. http://api-dev.dineapp.com/manager/register_wordpress_plugin
    'LOGIN_DINEAPP_URL' => "http://www.dineapp.com/register/ajax/wordpress_login",

    // sidebar wiget
    // FIXME: generate and pass back by API
    'SIDEBAR_WIDGET_URL' => 'http://widget.dineapp.com/blog_booking/view.php',

    // tab widget
    // FIXME: generate and pass back by API
    'TAB_WIDGET_URL' => 'http://widget.dineapp.com/tab_booking/tab_booking.php',

);
