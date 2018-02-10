<?php

defined('ABSPATH') or exit;

function estis_pn_style_and_js()
{

    wp_register_style('boost_css', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css', array(), null);
    wp_register_style('estis_css', plugin_dir_url(dirname(__FILE__)) . 'css/estis_pn_style.css', array(), '1.0', 'all');

    wp_enqueue_style('boost_css', false);
    wp_enqueue_style('estis_css');
}


function estis_pn_sendmail_js_and_css()
{
    //vendor styles
    wp_register_style('multi-select_css', plugin_dir_url(dirname(__FILE__)) . 'css/multi-select.dist.css', array(), '1.0', 'all');
    wp_enqueue_style('multi-select_css');

    wp_register_style('calendar_css', plugin_dir_url(dirname(__FILE__)) . 'css/jquery.datetimepicker.min.css', array(), '1.0', 'all');
    wp_enqueue_style('calendar_css');

	wp_register_style('boost_css', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css', array(), null);
	wp_enqueue_style('boost_css', false);

    //custom styles
    wp_register_style('list-settings', plugin_dir_url(dirname(__FILE__)) . 'css/estis-pn-list-settings.css', array(), '1.0', 'all');
    wp_enqueue_style('list-settings');


    //vendor scripts
    wp_register_script('JQuery', 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js', true);
    wp_enqueue_script('JQuery');

    wp_register_script('JQueryMulti', plugin_dir_url(dirname(__FILE__)) . 'js/jquery.multi-select.js', true);
    wp_enqueue_script('JQueryMulti');

    wp_register_script('JQueryDateTimePicker', plugin_dir_url(dirname(__FILE__)) . 'js/jquery.datetimepicker.full.min.js', true);
    wp_enqueue_script('JQueryDateTimePicker');


    //custom scripts
    wp_register_script('custom-script', plugin_dir_url(dirname(__FILE__)) . 'js/estismail-np-send-mail.page.js', true);
    wp_enqueue_script('custom-script');

}

function estis_pn_template_js()
{
	wp_register_style('boost_css', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css', array(), null);
	wp_enqueue_style('boost_css', false);

    wp_register_style('template_custom.css', plugin_dir_url(dirname(__FILE__)) . 'css/template_custom.css', array(), '1.0', 'all');
    wp_enqueue_style('template_custom.css');

    wp_register_script('template_custom_script', plugin_dir_url(dirname(__FILE__)) . 'js/estis_pn_template.js', true);
    wp_enqueue_script('template_custom_script');
}

function estis_pn_add_css(){
	wp_register_style('boost_css', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css', array(), null);
	wp_enqueue_style('boost_css', false);

	wp_register_style('add.css', plugin_dir_url(dirname(__FILE__)) . 'css/add.css', array(), '1.0', 'all');
	wp_enqueue_style('add.css');
}


function estis_pn_edit_css()
{
	wp_register_style('boost_css', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css', array(), null);
	wp_enqueue_style('boost_css', false);

	wp_register_style('edit.css', plugin_dir_url(dirname(__FILE__)) . 'css/edit.css', array(), '1.0', 'all');
	wp_enqueue_style('edit.css');

}

function estis_pn_preview_css(){
	wp_register_style('boost_css', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css', array(), null);
	wp_enqueue_style('boost_css', false);

    wp_register_style('template_custom.css', plugin_dir_url(dirname(__FILE__)) . 'css/template_custom.css', array(), '1.0', 'all');
    wp_enqueue_style('template_custom.css');
}
?>