<?php defined('ABSPATH') or exit;

function estis_pn_templates_preview()
{
    $current_maket_id = isset($_GET['id']) ? sanitize_text_field($_GET['id']) : '0';
    $makets_db = get_option(ESTIS_API_PREFIX . '_makets');
    $estis_pn_name = (get_plugin_data(ESTIS_PN_ABS_PATH));

    //params to get posts below
    $args = array(
        'numberposts' => -1, //display all posts ( we can grep last 5, for example )
        'category' => 0,
        'orderby' => 'date',
        'order' => 'DESC',
        'include' => array(),
        'exclude' => array(),
        'meta_key' => '',
        'meta_value' => '',
        'post_type' => 'post',
        'suppress_filters' => true, // SQL filters stop
    );

    if (!array_key_exists($current_maket_id, $makets_db)) {
        $estis_errors[] = __('This maket does not exist', 'estis_pn_translate');
        ?>
        <div class="error fade">
            <p><strong><?php echo $estis_errors[0]; ?></strong></p>
        </div>
        <?php
    } else {
        $current_maket = $makets_db[$current_maket_id];
        $maket_preview = $current_maket['body'];
        $all_posts = get_posts($args);

        if (isset($_POST['estis_form_submit']) && $_POST['estis_form_submit'] == 'yes') {

            $post_id = isset($_POST['estis_post_id']) ? ($_POST['estis_post_id']) : '';
            if ($post_id) {
                $selected_post = get_post($post_id);
                $maket_preview = estis_pn_template_render($maket_preview, $selected_post);
            }
        }
        ?>
        <div class="wrap">
            <div id="icon-plugins" class="icon32"></div>
            <div class="portlet-title">
                <div class="caption">
                    <span class="caption-subject font-dark bold uppercase"><?php _e($estis_pn_name['Name'], 'estis_pn_translate'); ?></span>
                </div>
            </div>
            <form method="post" action="" class="">

                <!--post title for template-->
                <label for="tag-link" class="tag"><?php _e('Select post for preview', 'estis_pn_translate'); ?></label>
                <?php if ($all_posts): ?>
                    <select name="estis_post_id" type="text" id="estis_post_id">
                        <option value=""></option>
                        <?php foreach ($all_posts as $value): ?>
                            <option value="<?php echo($value->ID) ?>">
                                <?php echo($value->post_title) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php else: ?>
                    <?php echo _e('No Posts found!', 'estis_pn_translate'); ?>
                <?php endif; ?>
                <div class="tool-box">
                    <div style="padding:15px;background-color:#FFFFFF;">
                        <?php
                        echo stripslashes($maket_preview);
                        ?>
                    </div>
                    <input type="hidden" name="estis_form_submit" value="yes"/>
                    <div class="table-nav bottom">
                        <a href="<?php echo admin_url(); ?>admin.php?page=estis_pn_templates-view">
                            <input class="btn btn-info"
                                   type="button"
                                   value="<?php _e('Back', 'estis_pn_translate'); ?>"/></a>
                        <a href="<?php echo admin_url(); ?>admin.php?page=estis_pn_templates-edit&amp;id=<?php echo $current_maket_id; ?>">
                            <input class="btn btn-primary"
                                   type="button"
                                   value="<?php _e('Edit', 'estis_pn_translate'); ?>"/></a>
                        <a href="<?php echo admin_url(); ?>admin.php?page=estis_pn_templates-preview&amp;id=<?php echo $current_maket_id; ?>">
                            <input class="btn btn-success"
                                   type="submit"
                                   value="<?php _e('Change Post', 'estis_pn_translate'); ?>"/></a>
                    </div>
                </div>
        </div>
        <?php
    }
}