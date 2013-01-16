<?php
require_once __DIR__ . '/config/config.php';

class DineApp
{
    const PLUGIN_NAME = "dineapp";
    const DEFAULT_TAB_ENABLE = true;
    const DEFAULT_TAB_TITLE = 'Online Reservation';

    const DEFAULT_SIDEBAR_WIDGET_ENABLE = true;
    const DEFAULT_SIDEBAR_WIDGET_TITLE = 'Online Booking (by DineApp)';

    const OPTION_APP_CONFIG = 'app_config';
    const OPTION_TAB_CONFIG = 'tab_config';
    const OPTION_WIDGET_CONFIG = 'widget_config';

    const OPTION_WIDGET_NAME = 'widget_name';
    const OPTION_SHOW_TAB = 'show_tab';
    const OPTION_SHOW_WIDGET = 'show_widget';

    public static function get_template_path()
    {
        return dirname(__FILE__) . '/templates/';
    }


    public static function save_options($data)
    {
        foreach($data as $key => $value) {
            self::save_option($key, $value);
        }
    }


    public static function save_option($key, $value)
    {
        $daKey = 'da_' . $key;
        update_option($daKey, $value);
    }


    public static function get_option($key)
    {
        $daKey = 'da_' . $key;
        return get_option($daKey);
    }


    public static function show_login_page()
    {
        $VARS = array();
        $template = self::get_template_path() . 'forge-key-template.php';
        echo self::easy_render($VARS, $template);

        wp_enqueue_script(
            'da_admin_login', 
            plugins_url('/' . self::get_plugin_name() . '/admin/js/admin_login.js'), 
            array('jquery'),
            true
        );
    }

    public static function get_plugin_name() 
    {
        return self::PLUGIN_NAME;
    }


    public static function register_admin_panel()
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
            array('DineApp', 'show_admin_index')
        );
    }


    public static function show_config_page($msg = null) 
    {
        $app_config = self::get_app_config();
        $tab_config = self::get_tab_config();
        $widget_config = self::get_widget_config();

        // set checkbox
        $tab_config['enable_checked'] = 
            $tab_config['enable'] == 1 ? 'checked' : '';

        $widget_config['enable_checked'] = 
            $widget_config['enable'] == 1 ? 'checked' : '';

        // render html in admin-template
        $VARS = array(
            'app_config' => $app_config,
            'tab_config' => $tab_config,
            'widget_config' => $widget_config,
        );
        $template = self::get_template_path() . 'admin-template.php';

        echo self::easy_render($VARS, $template);
    }


    public function easy_render($VARS, $filename) {
        if (is_file($filename)) {
            ob_start();
            include $filename;
            return ob_get_clean();
        }
        return false;
    }


    public static function init_configs()
    {
        // app config
        $app_config = array(
            'app_id' => $_POST['da_app_id'],
            'app_secret' => $_POST['da_app_secret'],
            'server_url' => $_POST['da_server_url'],
        );
        self::save_app_config($app_config);

        // tab config
        $tab_config = array(
            'enable' => 1,
            'partner_id' => $_POST['da_partner_id'],
            'partner_page_name' => $_POST['da_partner_page_name'],
            'server_url' => $_POST['da_server_url'],
        );
        self::save_tab_config($tab_config);
        self::install_booking_tab();

        // widget config
        $widget_config = array(
            'enable' => 1,
            'widget_code' => $_POST['da_widget_code'],
            'server_url' => $_POST['da_server_url'],
        );
        self::save_widget_config($widget_config);

    }


    public static function update_configs()
    {
        // update tab configs
        $tab_config = self::get_tab_config();
        if ( isset($_POST['tab_enable']) && 
            intval($_POST['tab_enable']) == 1) {
            $tab_config['enable'] = 1;

        } else {
            $tab_config['enable'] = 0;
            DineApp::uninstall_booking_tab();
        }
        if ( isset($_POST['tab_title']) && ($tab_config['title'] != $_POST['tab_title']) ) {
            $tab_config['title'] = $_POST['tab_title'];
            self::set_tab_title($_POST['tab_title']);
        }
        self::save_tab_config($tab_config);

        // install tab if not installed yet
        if ($tab_config['enable'] == 1 && !$tab_config['post_id']) {
            DineApp::install_booking_tab();
        }

        // update widget configs
        $widget_config = self::get_widget_config();
        if ((isset($_POST['widget_enable']) && 
            intval($_POST['widget_enable']) == 1) ) {
            $widget_config['enable'] = 1;
        } else {
            $widget_config['enable'] = 0;
        }
        if (isset($_POST['widget_title'])) {
            $widget_config['title'] = $_POST['widget_title'];
        }
        self::save_widget_config($widget_config);
    }


    public static function handle_dineapp_action()
    {
        switch ($_POST['da_action']) {
            case 'init_configs':
                self::init_configs();
                break;

            case 'update_configs':
                self::update_configs();
                break;

            default:
                die("unknown action");
                break;
        }
    }

    public static function show_admin_index()
    {	
        // actions
        if ( isset($_POST['da_action']) ) {
            self::handle_dineapp_action();
        } 

        // Fetch key from DB, if not present, user is new 
        $app_config = self::get_app_config();

        if ($app_config) {
            self::show_config_page($msg);
        } else {
            self::show_login_page();
        }
    }

    public static function install_booking_tab()
    {
        $dineapp_config = $GLOBALS['DINEAPP']['config'];
        $tab_config = self::get_tab_config();
        if ( !$tab_config['post_id'] ) {
            global $current_user;
            $partner_page_name = $tab_config['partner_page_name'];
            $config_page_html = <<<EOD
<iframe id="dineapp_tab_booking_iframe" src="{$dineapp_config['TAB_WIDGET_URL']}?page_name={$partner_page_name}" width="700" height="600" border="0" marginwidth="0" marginheight="0" frameborder="0"></iframe>
EOD;

            $tab_title = 
            $tab_booking_post = array(
                'post_title'    => $tab_config['title'],
                'post_content'  => $config_page_html,
                'comment_status' => 'close',
                'post_status'   => 'publish',
                'post_author'   => $current_user->id,
                'post_type'     => 'page',
            );

            // Insert the post into the database
            $post_id = wp_insert_post( $tab_booking_post );

            // update tab id
            $tab_config['post_id'] = $post_id;
            self::save_tab_config($tab_config);
        }
        else {
            $tab_booking_post = array(
                'ID' => $tab_config['post_id'],
                'post_title'    => $tab_config['title'],
                'post_status' => 'publish',
            );
            wp_update_post($tab_booking_post);
        }
    }


    public static function uninstall_booking_tab()
    {
        $tab_config = self::get_tab_config();
        $post_id = $tab_config['post_id'];
        $force_delete = false;
        // TODO double check it is generated by dineapp
        wp_delete_post( $post_id, $force_delete );
    }

    public static function deactivate_booking_tab()
    {
        $tab_config = self::get_tab_config();
        $post_id = $tab_config['post_id'];
        if ( $post_id ) {
            $tab_booking_post = array(
                'ID' => $post_id,
                'post_status' => 'private',
            );
            wp_update_post($tab_booking_post);
        }
    }


    public static function on_activated()
    {
        // self::install_booking_tab();
    }


    public static function on_deactivated()
    {
        self::deactivate_booking_tab();
    }


    public static function get_app_config()
    {
        $app_config = array();
        $app_config_json = self::get_option(self::OPTION_APP_CONFIG);
        if ($app_config_json) {
            $app_config = json_decode($app_config_json, true);
        }

        return $app_config;
    }

    public static function save_app_config($app_config)
    {
        $app_config_json = json_encode($app_config);
        return self::save_option(self::OPTION_APP_CONFIG, $app_config_json);
    }

    public static function get_default_tab_config()
    {
        return array(
            'enable' => self::DEFAULT_TAB_ENABLE,
            'title' => self::DEFAULT_TAB_TITLE,
        );
    }

    public static function get_custom_tab_config()
    {
        $tab_config = array();
        $tab_config_json = self::get_option(self::OPTION_TAB_CONFIG);
        if ($tab_config_json) {
            $tab_config = json_decode($tab_config_json, true);
        }

        return $tab_config;
    }

    public static function get_tab_config()
    {
        return array_merge(
            self::get_default_tab_config(),
            self::get_custom_tab_config()
        );
    }

    public static function save_tab_config($tab_config)
    {
        $tab_config_json = json_encode($tab_config);
        return self::save_option(self::OPTION_TAB_CONFIG, $tab_config_json);
    }

    public static function set_tab_title($tab_title)
    {
        $tab_config = self::get_tab_config();
        $tab_booking_post = array(
            'ID' => $tab_config['post_id'],
            'post_title'    => $tab_title,
        );
        wp_update_post($tab_booking_post);
    }


    public function get_default_widget_config()
    {
        return array(
            'enable' => self::DEFAULT_SIDEBAR_WIDGET_ENABLE,
            'title' => self::DEFAULT_SIDEBAR_WIDGET_TITLE,
        );
    }

    public static function get_custom_widget_config()
    {
        $widget_config = array();
        $widget_config_json = self::get_option(self::OPTION_WIDGET_CONFIG);
        if ($widget_config_json) {
            $widget_config = json_decode($widget_config_json, true);
        }

        return $widget_config;
    }

    public static function get_widget_config()
    {
        return array_merge(
            self::get_default_widget_config(),
            self::get_custom_widget_config()
        );
    }

    public static function save_widget_config($widget_config)
    {
        $widget_config_json = json_encode($widget_config);
        return self::save_option(self::OPTION_WIDGET_CONFIG, $widget_config_json);
    }

    public static function get_widget_title() 
    {
        $widget_config = self::get_widget_config();
        return $widget_config['title'];
    }

    public static function set_widget_title($widget_title)
    {
        $widget_config = self::get_widget_config();
        $widget_config['title'] = $widget_title;
        self::save_widget_config($widget_config);
    }


    public static function register_sidebar_widget()
    {
        $widget_config = self::get_widget_config();

        $widget_params = array(
            'DINEAPP_CONFIG' => $GLOBALS['DINEAPP']['config']
        );

        $widget_config = self::get_widget_config();
        wp_register_sidebar_widget(
            'dineapp_booking_widget',        // your unique widget id
            $widget_config['title'],          // widget title
            array('DineApp', 'display_sidebar_widget'),  // callback function
            array(                  // options
                'description' => 'provide booking function in the sidebar',
            ),
            $widget_config
        );
    }


    public static function display_sidebar_widget($args, $widget_config)
    {
        $dineapp_config = $GLOBALS['DINEAPP']['config'];
        if ( $widget_config['enable'] == 1 ) {
            // get widget configs
            $widget_code = $widget_config['widget_code'];

            // display widget
            echo $before_widget;
            echo $before_title . $widget_config['title'] . $after_title;
            echo $after_widget;

            // print some HTML for the widget to display here
            echo <<<EOD
<iframe src="{$dineapp_config['SIDEBAR_WIDGET_URL']}?code={$widget_code}" width="350" height="410" border="0" marginwidth="0" marginheight="0" frameborder="0"></iframe>
EOD;
        }
    }

}
