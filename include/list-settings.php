<?php defined('ABSPATH') or exit;



function estis_pn_list_view()
{
    //args for getting posts (WP docs)
	$args = array(
		'numberposts' => -1,
		'category'    => 0,
		'orderby'     => 'date',
		'order'       => 'DESC',
		'include'     => array(),
		'exclude'     => array(),
		'meta_key'    => '',
		'meta_value'  =>'',
		'post_type'   => 'post',
		'suppress_filters' => true, // stopped SQL filters
	);

	//all posts, we have
	$posts = get_posts( $args );

    //default time for time functions here
    date_default_timezone_set('Europe/Kiev');
    $templates_db = get_option(ESTIS_API_PREFIX . '_makets');
    $api_key = get_option(ESTIS_API_PREFIX . '_api_key');
    $date_valid = true;
    //default error value
    $estis_pn_plug_err = '';
    //for template checker
    $template_valid = true;

    //getting lists
    $lists_params = array('fields' => json_encode(array('id', 'title', 'about', 'status', 'subscribe_page')));
    $lists_url = API_URL . '/mailer/lists?' . http_build_query($lists_params);
    $lists_data = estis_pn_remote_get($lists_url, $api_key);

    //swap default array index to id
    if ($lists_data) {
        foreach ($lists_data['lists'] as $key => $value) {
            $lists_data['lists'][$value['id']] = $value;
            unset($lists_data['lists'][$key]);
        }
    }

    //getting sender email id
    $sender_email_url = API_URL . '/mailer/senderemails';
    $sender_email_data = estis_pn_remote_get($sender_email_url, $api_key);

    //Collecting data to send on estis
    if (isset($_POST) && !empty($_POST) && ($sender_email_data)) {

        //processing SendMail Theme
        if(isset($_POST[ESTIS_API_PREFIX . '_mail_theme']) && !empty($_POST[ESTIS_API_PREFIX . '_mail_theme'])){
            $SendMailTheme = $_POST[ESTIS_API_PREFIX . '_mail_theme'];
        }else{
	        $SendMailTheme = '';
	        $estis_pn_plug_err = __('Please, write a SendMail theme', 'estis_pn_translate');
        }

        //processing post
	    if(isset($_POST[ESTIS_API_PREFIX . '_posts']) && !empty($_POST[ESTIS_API_PREFIX . '_posts'])){
            $post_params = get_post($_POST[ESTIS_API_PREFIX . '_posts']);
            if(!isset($post_params)){
	            $estis_pn_plug_err = __('Selected post missed','estis_pn_translate');
            }
	    }else{
		    $estis_pn_plug_err = __('You should have at least one post','estis_pn_translate');
	    }

        //processing Date
        if (isset($_POST[ESTIS_API_PREFIX . '_send_date']) && !empty($_POST[ESTIS_API_PREFIX . '_send_date'])) {
            $current_date = date('U');
            $send_date_param = (strtotime($_POST[ESTIS_API_PREFIX . '_send_date']));

            //checking, if data is valid
            if ($current_date >= $send_date_param) {
	            $estis_pn_plug_err = __("Invalid date value",'estis_pn_translate');
                $date_valid = false;
            }
        }

        //template checker
        if (!isset($_POST[ESTIS_API_PREFIX . '_template']) || empty($_POST[ESTIS_API_PREFIX . '_template'])) {
            $estis_pn_plug_err = __('No template', 'estis_pn_translate');
            $template_valid = false;
        }

        //push list id, if it's set. Later, we will json encode for this. This code was here to stop api queries. if we got false params
        $selected_lists = [];
        if (isset($_POST['estis_included_list']) && !empty($_POST['estis_included_list'])) {
            foreach ($_POST['estis_included_list'] as $value) {
                array_push($selected_lists, $value);
            }
        }

        //processing empty lists error
        if (!$selected_lists) {
            $estis_pn_plug_err = __('Empty lists', 'estis_pn_translate');
        }

        //push exclude lists to know, that they not included lists
        $excluded_lists = [];
        if (isset($_POST['estis_exluded_list']) && !empty($_POST['estis_exluded_list'])) {
            foreach ($_POST['estis_exluded_list'] as $value) {
                array_push($excluded_lists, $value);
            }
        }

        //check, if lists not the same
        if ($excluded_lists === $selected_lists && $selected_lists) {
            $estis_pn_plug_err = __('Icluded lists are the same, as Excluded lists', 'estis_pn_translate');
        }

        //checking template
        if(!isset($templates_db[$_POST[ESTIS_API_PREFIX . '_template']])){
	        $selected_template = '';
	        $estis_pn_plug_err = __('Template missing', 'estis_pn_translate');
        }else{
	        $selected_template = $templates_db[$_POST[ESTIS_API_PREFIX . '_template']];
        }

        //check, if we have all required params
        if ($date_valid && $selected_lists && $template_valid && !$estis_pn_plug_err && $sender_email_data) {
            //collecting data from UI form

            $estis_pn_email = $_POST[ESTIS_API_PREFIX . '_email'];

            //prepare template body to render
            if (isset($post_id)) {
                $selected_template['body'] = estis_pn_template_render($selected_template['body'], $post_params);
            }

            //pushing makets on estis
            $makets_url = API_URL . "/mailer/makets";
            $makets_params = [
                'title' => $selected_template['title'],
                'body' => (estis_pn_template_render($selected_template['body'], $post_params))
            ];

            //get id of rendered maket
            $estis_maket_id = estis_pn_remote_post($makets_url, $makets_params, $api_key);

            //success message to user if maket was uploaded
            $status = 200;
            $class = "updated";
            $mes = __("Maket was uploaded", 'estis_pn_translate');

            //maket error
            if (!$estis_maket_id) {
                $status = 400;
                $class = "notice-error";
                $mes = __("Maket uploading was failed", 'estis_pn_translate');
            } else {

                //maket id param
                $current_maket = (json_decode($estis_maket_id, 1));

                $send_url = API_URL . "/mailer/sends";
                $send_params = [
                    'maket_id' => $current_maket['id'],
                    'letter_title' => $SendMailTheme,
                    'included_lists' => json_encode($selected_lists),
                ];

                if ($excluded_lists) {
                    $send_params['excluded_lists'] = json_encode($excluded_lists);
                }

                //collecting unrequired data (DATE)
                if (isset($current_date) && !empty($current_date)) {
                    $send_params['date'] = $send_date_param;
                };
                //collecting unrequired data (SENDER_EMAIL_ID)
                if (isset($estis_pn_email) && !empty($estis_pn_email)) {
                    $send_params['sender_email_id'] = (int)$estis_pn_email;
                };

                //sending sendf
                $send = estis_pn_remote_post($send_url, $send_params, $api_key);

                //Send was successfully created
                $send_status = 200;
                $send_err_mes = __('Send was successfully created', 'estis_pn_translate');
                $send_class = "updated";

                //errors processing
                if (!$send) {
                    //setting send error params
                    $send_status = 400;
                    $send_class = "notice-error";
                    $send_mes = get_option('estis_pn_remote_post_err');
                    $send_mes = json_decode($send_mes, 1);

                    //get send err code
                    $subject = $send_mes['name'];
                    $pattern = "/^.*#([0-9]{5})/";
                    preg_match($pattern, $subject, $matches);
                    $send_err_code = ($matches['1']);
                    $send_err_mes = estis_pn_send_err_checker($send_err_code);
                }
            }
        }
    }
    ?>
    <!--main plug err-->
	<?php if (isset($estis_pn_plug_err) && !empty($estis_pn_plug_err)): ?>
    <div id="message" class="notice-error notice is-dismissible"">
    <p><?php echo($estis_pn_plug_err) ?>.</p>
    <button type="button" class="notice-dismiss">
        <span class="screen-reader-text">Dismiss this notice.</span>
    </button>
    <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
    </div>
    <?php endif; ?>

    <!--maket uploading-->
	<?php if (isset($status) && !empty($status)): ?>
    <div id="message" class="<?php echo($class) ?> notice is-dismissible">
        <p><?php echo($mes) ?>.</p>
        <button type="button" class="notice-dismiss">
            <span class="screen-reader-text">Dismiss this notice.</span>
        </button>
        <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
    </div>
    <?php endif; ?>

    <!--send creation-->
	<?php if (isset($send_status) && !empty($send_status)): ?>
    <div id="message" class="<?php echo($send_class) ?> notice is-dismissible">
        <p><?php echo($send_err_mes) ?>.</p>
        <button type="button" class="notice-dismiss">
            <span class="screen-reader-text">Dismiss this notice.</span>
        </button>
         <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
    </div>
    <?php endif; ?>


    <!--unauthorized-->
	<?php if (!$sender_email_data && !empty($_POST)): ?>
    <div id="message" class="notice-error notice is-dismissible">
        <p><?php _e('Please, use valid api key', 'estis_pn_translate') ?>.</p>
        <button type="button" class="notice-dismiss">
            <span class="screen-reader-text">Dismiss this notice.</span>
        </button>
        <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
    </div>
    <?php endif; ?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 col-xs-12 col-sm-12">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <span class="caption-subject font-dark bold uppercase"><?php _e('Estismail Sends-create constructor','estis_pn_translate') ?></span>
                        </div>
                    </div>
                    <div class="portlet-body">


                        <form action="" class="form-horizontal" method="POST" id="estisPnSendMailsForm">
                            <div class="wrap">
                                <div class="form-wrap">
                                    <div id="icon-plugins" class="icon32"></div>
                                    <div class="form-group">
                                        <label for="elp" class="col-sm-2 control-label"><?php _e('Mail theme','estis_pn_translate') ?>
                                            <p class="description"><?php _e('Write your SendMail theme', 'estis_pn_translate') ?>.</p>
                                        </label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control" name="estis_pn_mail_theme" placeholder="<?php _e('mail theme', 'estis_pn_translate') ?>">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="elp" class="col-sm-2 control-label"><?php _e('Posts', 'estis_pn_translate') ?>
                                            <p class="description"><?php _e('Select your post','estis_pn_translate') ?>  .</p>
                                        </label>
	                                    <?php if ($posts): ?>
                                        <div class="col-sm-6">
                                                <select id="elp_sent_type" class="form-control" name="estis_pn_posts">
	                                                <?php foreach ($posts as  $value): ?>
                                                        <option value="<?php echo($value->ID) ?>"><?php echo($value->post_title) ?></option>
	                                                <?php endforeach; ?>
                                                </select>
                                        </div>
                                        <?php else: ?>
                                            <div class="col-sm-6">
                                                <?php _e('No posts','estis_pn_translate') ?>
                                            </div>
	                                    <?php endif; ?>
                                    </div>

                                    <div class="form-group">
                                        <label for="elp" class="col-sm-2 control-label"><?php _e('Template','estis_pn_translate') ?>
                                            <p class="description"><?php _e('Select your template', 'estis_pn_translate') ?> .</p></label>
                                        <div class="col-sm-6">
	                                        <?php if ($templates_db): ?>
                                                <select id="elp_sent_type" name="estis_pn_template" class="form-control">
			                                        <?php foreach ($templates_db as $key => $value): ?>
                                                        <option value="<?php echo($key) ?>"><?php echo($value['title']) ?></option>
			                                        <?php endforeach; ?>
                                                </select>
	                                        <?php else: ?>
                                                <h4><?php _e('No Templates', 'estis_pn_translate') ?></h4>
	                                        <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="elp" class="col-sm-2 control-label"><?php _e('Subscriber Email', 'estis_pn_translate') ?>
                                            <p class="description"><?php _e('Select your Subscriber Email','estis_pn_translate') ?></p></label>
	                                    <?php if (isset($sender_email_data) && !empty($sender_email_data)): ?>
                                        <div class="col-sm-6">
                                            <select name="estis_pn_email" id="" class="form-control">
		                                        <?php foreach ($sender_email_data['sender_emails'] as $value): ?>
                                                    <option value="<?php echo($value['id']); ?>"><?php echo($value['email']); ?></option>
		                                        <?php endforeach; ?>
                                            </select>
                                        </div>
	                                    <?php else: ?>
                                            <div class="col-sm-4">
                                                <h4><?php _e('No sender emails', 'estis_pn_translate') ?></h4>
                                            </div>
	                                    <?php endif ?>
                                    </div>

                                    <div class="form-group">
                                        <label for="elp" class="col-sm-2 control-label"><?php _e('Select your list','estis_pn_translate') ?>
                                            <p class="description"><?php _e('Select your list','estis_pn_translate') ?>.</p></label>
	                                    <?php if ($lists_data): ?>
                                        <div class="col-sm-10">
                                            <div class="estisMultiContainer">
                                                <h5>Included lists</h5>
                                                <a href='#' id='select-all'><?php _e('select all', 'estis_pn_translate') ?></a>
                                                <a href='#' id='deselect-all' class="des"><?php _e('deselect all', 'estis_pn_translate') ?></a>
                                                <select  multiple="multiple" class="estisMultiSelect" name="estis_included_list[]"  >
	                                                <?php foreach ($lists_data['lists'] as $value): ?>
                                                        <option value="<?php echo($value['id']) ?>"><?php echo($value['title']) ?></option>
	                                                <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="estisMultiContainer">
                                                <h5>Excluded lists</h5>
                                                <a href='#' id='select-all-ex'><?php _e('select all', 'estis_pn_translate') ?></a>
                                                <a href='#' id='deselect-all-ex' class="des"><?php _e('deselect all', 'estis_pn_translate') ?></a>
                                                <select  name="estis_exluded_list[]" class="estisMultiSelect" multiple="multiple">
	                                                <?php foreach ($lists_data['lists'] as $value): ?>
                                                        <option value="<?php echo($value['id']) ?>"><?php echo($value['title']) ?></option>
	                                                <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
	                                    <?php else: ?>
                                            <div class="col-sm-10">
                                                <h4><?php _e('No lists', 'estis_pn_translate') ?></h4>
                                            </div>
	                                    <?php endif; ?>
                                    </div>

                                    <div class="form-group">
                                        <label for="elp" class="col-sm-2 control-label"><?php _e('Date','estis_pn_translate') ?>
                                            <p class="description"><?php _e('Choose date &amp; time to your send','estis_pn_translate') ?></p></label>
                                        <div class="col-sm-6">
                                            <input id="datetimepicker" type="text" name="estis_pn_send_date" placeholder="<?php _e('now','estis_pn_translate') ?>" class="form-control">
                                        </div>
                                    </div>
                                    <div class="clearfix">
                                        <input type="submit" id="Submit" class="btn btn-success pull-left" value="<?php _e('Send Email', 'estis_pn_translate') ?>">
                                    </div>

                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    wp_reset_postdata();
}