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
            <input type="hidden" name="da_action" value="init_configs" />
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
            'LOGIN_DINEAPP_URL': '<?php echo $GLOBALS['DINEAPP']['config']['LOGIN_DINEAPP_URL']; ?>'
            }

    </script>

