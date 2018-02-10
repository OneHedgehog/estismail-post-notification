<?php
defined('ABSPATH') or exit;

function estis_pn_remote_get($url, $key)
{
	$response = wp_remote_get($url,
		array(
			'timeout' => 3,
			'httpversion' => '1.1',
			'sslverify' => true,
			'headers' => array('X-Estis-Auth' => $key))
	);

	if (wp_remote_retrieve_response_code($response) !== 200) {
		update_option(ESTIS_API_PREFIX . '_error', $response['body']);
		return false;
	} else {
		return json_decode($response['body'], true);
	}
}

function estis_pn_remote_post($url, $params, $api_key)
{

	$response = wp_remote_post($url, array(
			'method' => 'POST',
			'timeout' => 3,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking' => true,
			'headers' => array('X-Estis-Auth' => $api_key),
			'body' => $params,
			'cookies' => array()
		)
	);

	if (wp_remote_retrieve_response_code($response) !== 201) {
		update_option('estis_pn_remote_post_err', $response['body']);
		return false;
	} else {
		return ($response['body']);
	}
}

function estis_pn_send_err_checker($err)
{
	switch ($err) {
		case (20540):
			$send_err_mes = __('Invalid sender_email_id. Specified sender_email_id did not found', 'estis_pn_translate');
			break;
		case (20541):
			$send_err_mes = __('You do not have default email. Please add new sender_email', 'estis_pn_translate');
			break;
		case(20542):
			$send_err_mes = __('Invalid maket_id. Specified maket_id did not found', 'estis_pn_translate');
			break;
		case(20544):
			$send_err_mes = __('Empty included lists', 'estis_pn_translate');
			break;
		case(20545):
			$send_err_mes = __('No active emails in selected lists', 'estis_pn_translate');
			break;
		case(20546):
			$send_err_mes = __('Processing error. Please contact with support service of Estismail support@estismail.com', 'estis_pn_translate');
			break;
		case(20547):
			$send_err_mes = __('Some error with saving letter. Please try again', 'estis_pn_translate');
			break;
		case(20548):
			$send_err_mes = __('Can not save send. Please try again', 'estis_pn_translate');
			break;
	}
	return $send_err_mes;
}

function estis_pn_template_render($body, $post)
{
	str_replace("{AUTHOR}", 'estismail_bot', $body);
	$body = str_replace("{EMAIL}", 'useremail@email.com', $body);
	$body = str_replace("{DATE}", $post->post_modified, $body);
	$body = str_replace("{POST_DESC}", $post->post_content, $body);
	$body = str_replace("{DATE_GMT}", $post->post_date_gmt, $body);
	$body = str_replace("{POST_STATUS}", $post->post_status, $body);
	$body = str_replace("{COMMENT_STATUS}", $post->comment_status, $body);
	$body = str_replace("{EDIT_DATE}", $post->post_modified, $body);
	$body = str_replace("{EDIT_DATE_GMT}", $post->post_modified_gmt, $body);
	$body = str_replace("{POST_URL}", $post->guid, $body);
	$body = str_replace("{POST_TITLE}", '<a href=' . $post->guid  .'>' . $post->post_title . '</a>', $body) ;
	return do_shortcode($body);
}