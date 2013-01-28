<?php
define("DINEAPP_PLUGIN_NAME", "dineapp");

function da_save_options($data)
{
    foreach($data as $key => $value) {
        da_save_option($key, $value);
    }
}


function da_save_option($key, $value) {
    $daKey = 'da_' . $key;
    update_option($daKey, $value);
}


function da_get_option($key)
{
    $daKey = 'da_' . $key;
    return get_option($daKey);
}


function da_show_forge_key_page()
{
    echo <<<EOD
<h1>DineApp for WordPress</h1></br>
<div class='eztable-logo'></div>

<h3>Please Login your DineApp main manager account:</h3>
    <form id="register_dineapp_form" action="" method="POST">
    <div class="control-group">
            <label for="manager_account">Account:</label>
        <div class="controls">
            <input id="da_manager_account" placeholder="Name or email" type="text" name="da_manager_account" />
        </div>
      </div>

      <div class="control-group">
            <label for="manager_password">Password:</label>
        <div class="controls">
            <input id="da_manager_password" placeholder="Password" name="da_manager_password" type="password"/>
        </div>
      </div>
        <button type="submit" class="btn btn-large btn-primary" type="button" >Start Using</button>
        <a href="http://www.dineapp.com/" target="_blank">Create a DineApp</a>
            <input type="hidden" name="da_action" value="da_gen_key" />
            <input id="da_partner_id" type="hidden" name="da_partner_id" value="" />
            <input id="da_partner_page_name" type="hidden" name="da_partner_page_name" value="" />
            <input id="da_widget_code" type="hidden" name="da_widget_code" value="" />
            <input id="da_app_id" type="hidden" name="da_app_id" value="" />
            <input id="da_app_secret" type="hidden" name="da_app_secret" value="" />
            <input id="da_server_url" type="hidden" name="da_server_url" value="" />
    </form>

    <!-- include jQuery -->
    <script type="text/javascript">
        var DINEAPP_CONFIG = {
                'LOGIN_DINEAPP_URL': '{$GLOBALS['DINEAPP']['config']['LOGIN_DINEAPP_URL']}'
            }
        
    </script>

EOD;
    
    
    wp_enqueue_script(
        'da_admin_login', 
        plugins_url('/' . da_get_plugin_name() . '/admin/js/admin_login.js'), 
        array('jquery'),
        true
    );
}

function da_get_plugin_name() {
    return DINEAPP_PLUGIN_NAME;
}


function da_add_admin_page()
{
    add_menu_page(
        // page title
        __('DineApp Plugin Setting', 'da_test'), 
        // menu title
        __('DineApp', 'da_test'), 
        // capability
        'manage_options', 
        // menu_slug
        'DineApp-setting-page', 
        // function
        'da_admin_setting'
    );
}


function da_show_config_page($msg = null) {
    // show logo
    echo "<div class='eztable-logo'></div>";
    echo "<h1>DineApp for WordPress</h1><br />";
    
    $appId = da_get_option('app_id');
    $show_tab_checked = da_get_option('show_tab') == 1 ? 'checked' : '';
    $show_widget_checked = da_get_option('show_widget') == 1 ? 'checked' : '';
    $config_page_html = <<<EOD
<p>
<h2>Main Settings</h2>
App Id: {$appId}<strong>
</p>

<hr>
<p>
<h2>Page Customization</h2>
<form action="" method="post">
  <h3>
    <input type="checkbox" value="1" id="da_show_tab" name="da_show_tab" {$show_tab_checked}>
    <label>Booking Tab</label>
  </h3>
  <p class="description">
    Booking Tab creates a new tab on your blog to provide reservation features.
  </p>

  <!-- Widget -->
  <h3>
    <input type="checkbox" value="1" id="da_show_widget" name="da_show_widget" {$show_widget_checked}>
    <label>Widget Tab</label>
  </h3>
  <p class="description">
    Widget brings more dynamic to your blog. It provides reservation function to your articles.
  </p>

  <table class="form-table" id="show_widget">
    <tbody>
      <tr valign="top">
        <th scope="row">
          <label>Color</label>
        </th>
        <td>
          <select name="color">
            <option>Blue</option>
            <option>Red</option>
            <option>Yellow</option>
          </select>
        </td>
      </tr>
    </tbody>
  </table>
  <hr/>
  <input type="hidden" name="da_action" value="da_save_config" />
  <input type="submit" name="submitChange" value="Save Change">
</form>
</p>
EOD;
    echo $config_page_html;
    echo "<p style = \"color:red;\">" . $msg . "</p><br />";
}


function da_admin_setting()
{	
    // Fetch key from DB, if not present, user is new 
    $partnerPageName = da_get_option('partner_page_name');
    
    // actions
    if ( isset($_POST['da_action']) ) {
        switch ($_POST['da_action']) {
            case 'da_gen_key':
                $data = array(
                    'app_id' => $_POST['da_app_id'],
                    'app_secret' => $_POST['da_app_secret'],
                    'partner_id' => $_POST['da_partner_id'],
                    'partner_page_name' => $_POST['da_partner_page_name'],
                    'widget_code' => $_POST['da_widget_code'],
                    'server_url' => $_POST['da_server_url'],
                );
                da_save_options($data);
                break;

            case 'da_save_config':
                $da_show_tab = (isset($_POST['da_show_tab']) && 
                                intval($_POST['da_show_tab']) == 1) ?
                                1 : 0;
                $da_show_widget = (isset($_POST['da_show_widget']) && 
                                intval($_POST['da_show_widget']) == 1) ?
                                1 : 0;
                switch ($da_show_tab) {
                    case 1:
                        da_show_tab();
                        $msg .= 'Enable DineApp tab<br />';
                        break;
                    case 0:
                        da_disable_tab();
                        $msg .= 'Disable DineApp tab<br />';
                        break;
                }
                switch ($da_show_widget) {
                    case 1:
                        $msg .= 'Enable DineApp widget<br />';
                        break;
                    case 0:
                        $msg .= 'Disable DineApp widget<br />';
                        break;
                }
                
                $configs = array(
                    'show_tab' => $da_show_tab,
                    'show_widget' => $da_show_widget,
                );
                da_save_options($configs);
                break;

            default:
                echo "unknown action";
                break;
        }
        da_show_config_page($msg);
    } 
    // show config page
    else if (!$partnerPageName) {
        // wordpress not registered and not one of the settting action
        da_show_forge_key_page();
    }
    else {
        da_show_config_page();
    }
    
}

function da_show_tab()
{
    $tabId = da_get_option('tab_id');
    if ( $tabId == null ) {
        global $current_user;
        $partnerPageName = da_get_option('partner_page_name');
        $serverUrl = da_get_option('server_url');
        $config_page_html = <<<EOD
<iframe src="{$serverUrl}/tab_booking/tab_booking.php?page_name={$partnerPageName}" width="700" height="600" border="0" marginwidth="0" marginheight="0" frameborder="0"></iframe>
EOD;
        $myPost = array(
            'post_title'    => 'DineApp booking',
            'post_content'  => $config_page_html,
            'comment_status' => 'close',
            'post_status'   => 'publish',
            'post_author'   => $current_user->id,
            'post_type'     => 'page',
        );
        
        // Insert the post into the database
        $postId = wp_insert_post( $myPost );
        da_save_option('tab_id',$postId);
    }
    else {
        $myPost = array(
            'ID' => $tabId,
            'post_status' => 'publish',
        );
        wp_update_post($myPost);
    }
}

function da_disable_tab()
{
    $tabId = da_get_option('tab_id');
    if ( $tabId != null ) {
        $myPost = array(
            'ID' => $tabId,
            'post_status' => 'private',
        );
        wp_update_post($myPost);
    }
}
