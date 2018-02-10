<?php
function estis_pn_admin_menu()
{
    $plug = (get_plugin_data(ESTIS_PN_ABS_PATH));

    $hook_suffix = add_menu_page(
        'Estismail PN Settings',
        $plug['Name'],
        'manage_options',
        'estismail-post-notification',
        ESTIS_API_PREFIX . '_admin_menu_view'
    );
    $template_main_page = add_submenu_page(
        'estismail-post-notification',
        'Estismail PN Templates',
        'Templates',
        'manage_options',
        'estis_pn_templates-view',
        ESTIS_API_PREFIX . '_templates_view'
    );
    $send_mail_js = add_submenu_page(
        'estismail-post-notification',
        'Estismail PN Send Email',
        'Send Email',
        'manage_options',
        'estis_pn_sand-mail',
        ESTIS_API_PREFIX . '_list_view'
    );
    $add_template = add_submenu_page(
        '',
        'Estismail PN add new template',
        'Add new template',
        'manage_options',
        'estis_pn_templates-add',
        ESTIS_API_PREFIX . '_templates_add'
    );
    $edit_template = add_submenu_page(
        '',
        'Estismail PN edit template',
        'Edit template',
        'manage_options',
        'estis_pn_templates-edit',
        ESTIS_API_PREFIX . '_templates_edit'
    );
    $preview_page = add_submenu_page(
        '',
        'Estismail preview template',
        'Preview template',
        'manage_options',
        'estis_pn_templates-preview',
        ESTIS_API_PREFIX . '_templates_preview'
    );

    add_action("load-{$hook_suffix}", 'estis_pn_style_and_js');
    add_action("load-{$send_mail_js}", 'estis_pn_sendmail_js_and_css');
    add_action("load-{$template_main_page}", 'estis_pn_template_js');
	add_action("load-{$add_template}", 'estis_pn_add_css');
	add_action("load-{$edit_template}", 'estis_pn_edit_css');
	add_action("load-{$preview_page}", 'estis_pn_preview_css');

}