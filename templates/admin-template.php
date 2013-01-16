<div class='wrap'>
    <h1>DineApp for WordPress</h1>
  <!--
DineApp App Id: <?php echo $VARS['app_config']['id']; ?><strong>
-->
  <div>
    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post"  class="form-horizontal">
      <h2>Booking Tab Settings</h2>
      <table class="form-table">
        <tbody>

        <!-- enable booking tab -->
        <tr valign="top">
          <th scope="row">Enable Booking Tab</th>
          <td> 
            <fieldset><legend class="screen-reader-text"><span>Enable Booking Tab</span></legend><label for="tab_enable">
                <input name="tab_enable" type="checkbox" id="tab_enable" value="1" <?php echo $VARS['tab_config']['enable_checked']; ?> >
                Enable</label>
          </fieldset></td>
        </tr>

        <!-- tab title -->
        <tr valign="top">
          <th scope="row"><label for="tab_title">Tab Title</label></th>
          <td><input name="tab_title" type="text" id="tab_title" value="<?php echo $VARS['tab_config']['title'] ?>" class="regular-text ltr">
            <p class="description">This would be shown as the tab title</p></td>
        </tr>

        </tbody>
      </table>

      <h2>Sidebar Widget Settings</h2>
      <table class="form-table">
        <tbody>

        <!-- enable sidebar widget -->
        <tr valign="top">
          <th scope="row">Enable Sidebar Widget</th>
          <td> 
            <fieldset><legend class="screen-reader-text"><span>Enable Sidebar Widget</span></legend><label for="widget_enable">
                <input name="widget_enable" type="checkbox" id="widget_enable" value="1" <?php echo $VARS['widget_config']['enable_checked']; ?> >
                Enable</label>
          </fieldset></td>
        </tr>

        <!-- widget title -->
        <tr valign="top">
          <th scope="row"><label for="widget_title">Widget Title</label></th>
          <td><input name="widget_title" type="text" id="widget_title" value="<?php echo $VARS['widget_config']['title'] ?>" class="regular-text ltr">
            <p class="description">This would be shown as the sidebar title</p></td>
        </tr>

        </tbody>
      </table>
      <p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="Save Changes"></p>
      <input type="hidden" name="da_action" value="update_configs" />
    </form>
  </div>
</div>

