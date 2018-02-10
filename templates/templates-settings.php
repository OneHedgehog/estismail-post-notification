<?php defined('ABSPATH') or exit;

function estis_pn_templates_view()
{
    $estis_pn_name = get_plugin_data(ESTIS_PN_ABS_PATH);
    $makets_db = get_option(ESTIS_API_PREFIX . '_makets');

    //Delete makets from DB
    if (isset($_GET['id'])) {
        unset($makets_db[$_GET['id']]);
        update_option(ESTIS_API_PREFIX . '_makets', $makets_db);
        //wp_redirect($_SERVER['HTTP_REFERER']);
        exit;
    }

    if (isset($_GET['del']) && ($_GET['del'] == 'yes')) {
        delete_option(ESTIS_API_PREFIX . '_makets');
        //wp_redirect($_SERVER['HTTP_REFERER']);
        exit;
    }
    ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-6 col-xs-12 col-sm-12">
                <div class="portlet light bordered">
                    <div class="portlet-title">
                        <div class="caption">
                            <span class="caption-subject font-dark bold uppercase"><?php _e($estis_pn_name['Name'], 'estis_pn_translate'); ?></span>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th scope="col"><?php _e('Template Heading', 'estis_pn_translate'); ?></th>
                                <th scope="col"><?php _e('Action', 'estis_pn_translate'); ?></th>
                            </tr>
                            </thead>
                            <tbody class="estisOverflow">
                            <tr id="estis-pn-templates" class="estisOverflow">
                                <?php if (isset($makets_db) && (!empty($makets_db))): ?>
                                <?php foreach ($makets_db as $key => $maket): ?>
                            </tr>
                            <tr>
                                <td><?php echo esc_html(stripslashes($maket['title'])); ?></td>
                                <td>
                                    <a class="btn btn-xs btn-primary" title="Edit"
                                       href="<?php echo admin_url(); ?>admin.php?page=estis_pn_templates-edit&amp;id=<?php echo $maket['id']; ?>">
                                        <?php _e('Edit', 'estis_pn_translate'); ?></a>
                                    <a class="btn btn-xs btn-info" title="Preview"
                                       href="<?php echo admin_url(); ?>admin.php?page=estis_pn_templates-preview&amp;id=<?php echo $maket['id']; ?>">
                                        <?php _e('Preview', 'estis_pn_translate'); ?></a>
                                    <a class="btn btn-xs btn-danger deleteThisTemplate" title="Delete"
                                       href="<?php echo admin_url(); ?>admin.php?page=estis_pn_templates-view&amp;id=<?php echo $maket['id']; ?>">
                                        <?php _e('Delete', 'estis_pn_translate'); ?></a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php
                            else: {
                                ?>
                                <tr>
                                    <td colspan="6" align="center"><?php _e('No records available', 'estis_pn_translate'); ?></td>
                                </tr>
                                <?php
                            }
                            endif;
                            ?>
                            </tbody>
                        </table>
                        <?php wp_nonce_field('estis_form_show'); ?>
                        <div class="table-nav">
                            <a href="<?php echo admin_url(); ?>admin.php?page=estis_pn_templates-add">
                                <input class="btn btn-success action" type="button"
                                       value="<?php _e('Create New Template', 'estis_pn_translate'); ?>">
                            </a>
                            <a href="<?php echo admin_url(); ?>admin.php?page=estis_pn_templates-view&amp;del=yes">
                                <input id="deleteAllTemplates" class="btn btn-danger action" type="button"
                                       value="<?php _e('Delete All Templates', 'estis_pn_translate'); ?>">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}