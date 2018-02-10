<?php defined('ABSPATH') or exit;


function estis_pn_admin_menu_view()
{
    $api_key = get_option(ESTIS_API_PREFIX . '_api_key');

    if (isset($_POST[ESTIS_API_PREFIX . '_api_key']) && !empty($_POST[ESTIS_API_PREFIX . '_api_key'])) {
        $api_key = $_POST[ESTIS_API_PREFIX . '_api_key'];
        update_option(ESTIS_API_PREFIX . '_api_key', $api_key);
    }

    if (preg_match("/^[a-z\d]{40}$/i", $api_key)) {
        $estis_pn_array['status'] = 400;

        $user_params = array('fields' => json_encode(array('login', 'email', 'name')));
        $user_url = 'https://v1.estismail.com/mailer/users?' . http_build_query($user_params);
        $user_data = estis_pn_remote_get($user_url, $api_key);

        if ($user_data) {
            $estis_pn_array['status'] = 200;
            $estis_pn_array['user'] = $user_data['user'];
        }
        update_option(ESTIS_API_PREFIX . '_array', $estis_pn_array);

    } elseif (!$api_key) {
        $estis_pn_array['status'] = 100;
    } else {
        $estis_pn_array['status'] = 400;
    }

    switch ($estis_pn_array['status']) {
        case 100: {
            $message = __('Not connected', 'estis_pn_translate');
            $class = 'alert-warning';
        }
            break;
        case 400: {
            $message = __('Invalid API key', 'estis_pn_translate');
            $class = 'alert-danger';
        }
            break;
        case 200: {
            $message = __('Connected', 'estis_pn_translate');
            $class = 'alert-info';
        }
            break;
    }
    ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-6 col-xs-12 col-sm-12">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <span class="caption-subject font-dark bold uppercase"><?php _e('API connection', 'estis_pn_translate'); ?></span>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <form method="post" action="" class="estis_form estisPostApiKeyForm">
                            <div class="alert <?php echo($class) ?>"><?php echo($message) ?></div>
                            <div class="form-group">
                                <h3><?php _e('Please, enter your API key', 'estis_pn_translate'); ?></h3>
                                <input type="text" name="estis_pn_api_key" class="form-control estisApiKeyinput"
                                       value="<?php echo($api_key); ?>"/>
                            </div>
                            <div class="form-group">
                                <input type="submit" value="<?php _e('Connect', 'estis_pn_translate'); ?>"
                                       class="btn btn-success api_key_button"/>
                                <a href="https://my.estismail.com/settings/profile#tab_1_5" target="_blank"
                                   class="btn btn-info get_api_key_href"><?php _e('Get your API key', 'estis_pn_translate'); ?></a>
                                <a href="https://estismail.com/"
                                   class="estis_pn_readme"><?php _e('ReadMe', 'estis_pn_translate'); ?></a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}
?>