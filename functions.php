<?php 
function kiyoh_getOption()
{
	$kiyoh_options					= array();
	$kiyoh_options['enable'] 		= get_option('kiyoh_option_enable');
	$kiyoh_options['link'] 			= get_option('kiyoh_option_link');
	$kiyoh_options['email'] 		= get_option('kiyoh_option_email');
	$kiyoh_options['delay'] 		= (int)get_option('kiyoh_option_delay');
	$kiyoh_options['event'] 		= (get_option('kiyoh_option_event') == 'Orderstatus') ? get_option('kiyoh_option_order_status') : get_option('kiyoh_option_event');
	$kiyoh_options['server'] 		= get_option('kiyoh_option_server');
	$kiyoh_options['excule_groups'] = kiyoh_getValExculeGroups();
	$kiyoh_options['tmpl_en'] 		= get_option('kiyoh_option_tmpl_en');
	$kiyoh_options['tmpl_en']		= str_replace("\n", '<br />', $kiyoh_options['tmpl_en']);
	$kiyoh_options['tmpl_du'] 		= get_option('kiyoh_option_tmpl_du');
	$kiyoh_options['tmpl_du']		= str_replace("\n", '<br />', $kiyoh_options['tmpl_du']);
	$kiyoh_options['company_name'] 	= get_option('kiyoh_option_company_name');

	if ($kiyoh_options['event'] == 'Shipping') {
		$kiyoh_options['event'] = 'processing';
	}
	return $kiyoh_options;
}
function kiyoh_getValExculeGroups()
{
	$result = array();
	if (is_plugin_active('groups/groups.php')) {
		$options = get_option( 'kiyoh_option_excule');
		if (kiyoh_checkExistsTable('groups_group')) {
			global $table_prefix;
			global $wpdb;
			$groups = $wpdb->get_results("SELECT group_id, name FROM `{$table_prefix}groups_group`");
			if (count($groups) > 0) {
				$arr_group = array();
				foreach ($groups as $group) {
					$arr_group[$group->group_id] = $group->group_id;
				}
			}
			ksort($arr_group);
			foreach ($arr_group as $key => $group) {
				if ($options[ $key] == 1) {
					$result[$key] = 'on';
				}			
			}
		}
	}
	return $result;
}
function kiyoh_set_html_content_type() {
	return 'text/html';
}
function kiyoh_sendMail($options)
{
	add_filter( 'wp_mail_content_type', 'kiyoh_set_html_content_type' );
	$kiyoh_options 	= $options['option'];
	$email 			= $options['email'];
	$send_mail		= $kiyoh_options['email'];
	$content_email 	= ($kiyoh_options['server'] == 'kiyoh.com') ? $kiyoh_options['tmpl_en'] : $kiyoh_options['tmpl_du'];
	$link 			= $kiyoh_options['link'];
	$subject 		= ($kiyoh_options['server'] == 'kiyoh.com') ? 'Review ' : 'Beoordeel ';
	$subject		.= $kiyoh_options['company_name'];
	$content_email 	= str_replace('[COMPANY_NAME]', $kiyoh_options['company_name'], $content_email);
	$link 			= '<a href="' . $link . '">' . $link . '</a>';
	$content_email 	= str_replace('[LINK]', $link, $content_email);
	$message 		=  $content_email;
	$headers 		= 'From: ' . $send_mail;
	$attachments 	= '';
	wp_mail( $email, $subject, $message, $headers, $attachments );
	
	//echo $headers;die();
	remove_filter( 'wp_mail_content_type', 'kiyoh_set_html_content_type' );
}

function kiyoh_checkExculeGroups($excule_groups, $user_id)
{
	//return true or false
	$flag = true;
	if (count($excule_groups) > 0 && kiyoh_checkExistsTable('groups_user_group') && kiyoh_checkExistsTable('groups_group')) {
		if ($user_id > 0) {
			global $table_prefix;
			global $wpdb;
			$groups = $wpdb->get_results("SELECT group_id FROM `{$table_prefix}groups_user_group` WHERE user_id=" . $user_id);
			if (count($groups) > 0) {
				if (count($groups) == 1) {
					$groups = current($groups);
					$group_id = $groups->group_id;
					if (array_key_exists($group_id, $excule_groups)) {
						$flag = false;
					}
				}else{
					$arr_group = array();
					foreach ($groups as $key => $group) {
						$arr_group[$group->group_id] = 0;
					}
					foreach ($excule_groups as $id => $group) {
						if (array_key_exists($id, $arr_group)) {
							$flag = false;
							break;
						}
					}
				}
			}
		}
	}
	return $flag;
}
function kiyoh_checkExistsTable($table_name)
{
	$flag = true;
	global $table_prefix;
	global $wpdb;
	$table_name = $table_prefix . $table_name;
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
	    $flag = false;
	}
	return $flag;
}
function kiyoh_selectExculeGroups()
{
	if (kiyoh_checkExistsTable('groups_group')) {
		global $table_prefix;
		global $wpdb;
		$groups = $wpdb->get_results("SELECT group_id, name FROM `{$table_prefix}groups_group`");
		if (count($groups) > 0) {
			$arr_group = array();
			foreach ($groups as $group) {
				$arr_group[$group->group_id] = $group->name;
			}
		}
		ksort($arr_group);
		//$arr_group[1] = 'General';
		$options = get_option( 'kiyoh_option_excule' );
		foreach ($arr_group as $key => $group) {
			echo '<fieldset>';
				echo '<label for="kiyoh_option_excule' . $key . '">';
					echo '<input name="kiyoh_option_excule[' . $key . ']" type="checkbox" value="1" ';
					checked($options[$key], 1, true );
					echo ' />' . $group;
				echo '</label>';
			echo '</fieldset>';
		}
	}
}
function kiyoh_createTableKiyoh($table_name = 'kiyoh')
{
	if (!kiyoh_checkExistsTable($table_name)) {
		global $wpdb;
		$table_name = $wpdb->prefix . $table_name;

		$sql = "CREATE TABLE $table_name (
			id int(11) NOT NULL AUTO_INCREMENT,
			order_id int(11) DEFAULT NULL,
			status varchar(255) NULL,
			UNIQUE KEY id (id)
		);";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
		return true;
	}else{
		return false;
	}

}
function kiyoh_checkSendedMail($table_name, $order_id, $status)
{
	global $wpdb;
	$row = $wpdb->get_row('SELECT * FROM ' . $table_name . ' WHERE order_id=' . $order_id . ' AND status="' . $status . '"');
	if ($row) {
		return true;
	}else{
		return false;
	}
}
function kiyoh_insertRow($table_name, $order_id, $status)
{
	global $wpdb;

	$row = $wpdb->get_row('SELECT * FROM ' . $table_name . ' WHERE order_id=' . $order_id);
	if ($row) {
		$wpdb->update( 
			$table_name, 
			array('status' => $status),
			array('order_id' => $order_id), 
			array('%s') 
		);
	}else{
		$wpdb->insert( 
			$table_name, 
			array( 
				'order_id' => $order_id, 
				'status' => $status 
			),
			array( 
				'%d', 
				'%s' 
			) 
		);
	}
}