var DineApp = {
    form: null,
};
DineApp.getPartnerInfo = function(){
    var ajaxOption = {
          type: 'GET',
          url: DINEAPP_CONFIG.LOGIN_DINEAPP_URL,
          dataType: 'jsonp',
          data: {
              //app_id: jQuery('#da_app_id').val(),
              //app_secret: jQuery('#da_app_secret').val()
              manager_account: jQuery('#da_manager_account').val(),
              manager_password: jQuery('#da_manager_password').val()
          },
          success: function(response) {
              if (response.status === 'ok') {
                  var partner_id = response.data.PR_ID;
                  var page_name = response.data.page_name;
                  var widget_code = response.widget_code;
                  var app_id = response.data.app_id;
                  var app_secret = response.data.secret;
                  var server_url = response.server_url;
                  jQuery('#da_partner_id').val(partner_id);
                  jQuery('#da_partner_page_name').val(page_name);
                  jQuery('#da_widget_code').val(widget_code);
                  jQuery('#da_app_id').val(app_id);
                  jQuery('#da_app_secret').val(app_secret);
                  jQuery('#da_server_url').val(server_url);
                  DineApp.form.submit();
              }
              else {
                  // FIXME show proper error message
                  alert('invalid dineapp account and password');
              }
          }
    }


    jQuery.ajax(ajaxOption);
};


DineApp.submitPartnerInfo = function(){
    jQuery('#register_dineapp_form').submit(function(e){
        e.preventDefault();
        DineApp.getPartnerInfo();
    });

}


jQuery(document).ready(function(){
    jQuery('#register_dineapp_form').submit(function(e){
        DineApp.form = this;
        e.preventDefault();
        DineApp.getPartnerInfo();
    });
});
