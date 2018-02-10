<?php defined('ABSPATH') or exit;

function estis_pn_templates_edit()
{
    $estis_pn_name = get_plugin_data(ESTIS_PN_ABS_PATH);
    $current_maket_id = isset($_GET['id']) ? sanitize_text_field($_GET['id']) : '0';
    $makets_db = get_option(ESTIS_API_PREFIX . '_makets');

    $estis_errors = array();
    $estis_success = '';
    $estis_error_found = false;

    if (!array_key_exists($current_maket_id, $makets_db)) {
        $estis_errors[] = __('This maket does not exist', 'estis_pn_translate');
        ?>
        <div class="error fade">
            <p><strong><?php echo $estis_errors[0]; ?></strong></p>
        </div>
        <?php
    } else {
        if (isset($_POST['estis_form_submit']) && $_POST['estis_form_submit'] == 'yes') {

            //Just security thingy that wordPress offers us
            check_admin_referer('estis_form_edit');

            $maket_title = isset($_POST['estis_title']) ? wp_filter_post_kses($_POST['estis_title']) : '';
            if ($maket_title == '') {
                $estis_errors[] = __('Please enter template heading.', 'estis_pn_translate');
                $estis_error_found = true;
            }

            // Sanitize content for allowed HTML tags for post content.
            $maket_body['estis_body'] = isset($_POST['estis_body']) ? wp_filter_post_kses($_POST['estis_body']) : '';

            if ($estis_error_found == false) {
                $estis_success = __('Template was successfully created.', 'estis_pn_translate');
                $maket_body = $_POST['estis_body'];
            }

            if ($estis_error_found == true && isset($estis_errors[0]) == true) {
                ?>
                <div class="error fade">
                    <p><strong><?php echo $estis_errors[0]; ?></strong></p>
                </div>
                <?php
            }

            if ($estis_error_found == FALSE && strlen($estis_success) > 0) {

                $params = array(
                    'id'    => $current_maket_id,
                    'title' => $maket_title,
                    'body'  => $maket_body
                );

                $makets_db[$params['id']] = $params;
                update_option(ESTIS_API_PREFIX . '_makets', $makets_db);
                ?>
                <div class="updated fade">
                    <p><?php echo $estis_success; ?> <a
                                href="<?php echo admin_url(); ?>admin.php?page=estis_pn_templates-view"><b><?php _e('Click here', 'estis_pn_translate'); ?></b></a>
                </div>
                <?php
            }
        }
        $makets_db = get_option(ESTIS_API_PREFIX . '_makets');
        $current_maket = $makets_db[$current_maket_id];
        ?>
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12 col-xs-12 col-sm-12">
                    <div class="portlet light bordered">
                        <div class="portlet-title">
                            <div class="caption">
                                <span class="caption-subject font-dark bold uppercase"><?php _e($estis_pn_name['Name'], 'estis_pn_translate'); ?></span>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <form method="post" action="" class="">
                                <div class="form-group">
                                    <label for="tag-link"><?php _e('Enter template heading.', 'estis_pn_translate'); ?></label>
                                    <input name="estis_title" class="form-control" type="text" id="estis_title"
                                           value="<?php echo stripslashes($current_maket['title']); ?>" size="50" maxlength="225"/>
                                </div>
                                <div class="form-group">
                                    <?php $settings_body = array('textarea_rows' => 10); ?>
                                    <?php wp_editor(stripslashes($current_maket['body']), "estis_body", $settings_body); ?>
                                    <p><?php _e('Please create body portion for your template.', 'estis_pn_translate'); ?>
                                        <?php echo _e('Keywords', 'estis_pn_translate'); ?>: {POST_TITLE}, {POST_DESC},
                                        {AUTHOR}, {EMAIL}, {DATE}</p>
                                </div>
                                <input type="hidden" name="estis_form_submit" value="yes"/>
                                <p class="submit">
                                    <input name="publish" lang="publish" class="btn btn-info add-new-h2"
                                           value="<?php _e('Update Template', 'estis_pn_translate'); ?>" type="submit"/>
                                    <a href="<?php echo admin_url(); ?>admin.php?page=estis_pn_templates-view">
                                        <input class="btn btn-danger action" type="button"
                                               value="<?php _e('Cancel', 'estis_pn_translate'); ?>"/>
                                    </a>
                                </p>
                                <?php wp_nonce_field('estis_form_edit'); ?>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}

