<?php
function ecpm_bua_requires_version() {
  $allowed_apps = array('classipress');
  
  if ( defined(APP_TD) && !in_array(APP_TD, $allowed_apps ) ) { 
	  $plugin = plugin_basename( __FILE__ );
    $plugin_data = get_plugin_data( __FILE__, false );
		
    if( is_plugin_active($plugin) ) {
			deactivate_plugins( $plugin );
			wp_die( "<strong>".$plugin_data['Name']."</strong> requires a AppThemes theme to be installed. Your WordPress installation does not appear to have that installed. The plugin has been deactivated!<br />If this is a mistake, please contact plugin developer!<br /><br />Back to the WordPress <a href='".get_admin_url(null, 'plugins.php')."'>Plugins page</a>." );
		}
	}
}

function ecpm_bua_activate() {
  $ecpm_bua_settings = get_option('ecpm_bua_settings');
  if ( empty($ecpm_bua_settings) ) { 
    $ecpm_bua_settings = array(
      'show_top_ad' => '',
      'top_ad' => '',
      'show_top' => 'on',
      'top_text' => 'You are not allowed to post any ads!',
      'top_size' => 'x-large',
      'top_weight' => 'bold',
      'top_style' => '',
      'top_decoration' => 'none',
      'top_transform' => 'none',
      'top_color' => '#000000',
      'image' => '',
      'image_position' => 'hide',
      'show_bottom' => 'on',
      'bottom_text' => '<a href="[home]">Back to page</a>',
      'bottom_size' => 'small',
      'bottom_weight' => 'bold',
      'bottom_style' => '',
      'bottom_decoration' => 'none',
      'bottom_transform' => 'none',
      'bottom_color' => '#000000',
      'show_bottom_ad' => '',
      'bottom_ad' => '',
      'remove_userdata' => '',      
      );
    update_option( 'ecpm_bua_settings', $ecpm_bua_settings );
  }

  wp_schedule_event( current_time( 'timestamp' ), 'daily', 'block_user_ads');
}

function ecpm_bua_deactivate() {
  wp_clear_scheduled_hook('block_user_ads');
}

function ecpm_bua_uninstall() {
  global $wpdb;
  $ecpm_bua_settings = get_option('ecpm_bua_settings');
  
  if ($ecpm_bua_settings['remove_userdata'] == 'on') {
    $like_field = $wpdb->esc_like( ECPM_BUA_META_KEY ). "%";
    $wpdb->query(	$wpdb->prepare("DELETE FROM $wpdb->usermeta WHERE meta_key LIKE %s", $like_field ) );
  }

  delete_option( 'ecpm_bua_settings' );
}

function ecpm_bua_plugins_loaded() {
	$dir = dirname(plugin_basename(__FILE__)).DIRECTORY_SEPARATOR.'languages'.DIRECTORY_SEPARATOR;
	load_plugin_textdomain(ECPM_BUA, false, $dir);
}

function ecpm_bua_clean_user_meta($user_id = '') {
  global $wpdb;

  if ($user_id == '')
    $sql = "SELECT user_id FROM $wpdb->usermeta WHERE meta_key = '".ECPM_BUA_META_KEY."' AND meta_value = ''";
  else  
    $sql = "SELECT user_id FROM $wpdb->usermeta WHERE meta_key LIKE '".ECPM_BUA_META_KEY."%' AND user_id = $user_id";

  $results = $wpdb->get_results( $sql );
  
  foreach ($results as $user_meta) {
    delete_user_meta($user_meta->user_id, ECPM_BUA_META_KEY);    
    delete_user_meta($user_meta->user_id, ECPM_BUA_META_KEY.'_date');    
    delete_user_meta($user_meta->user_id, ECPM_BUA_META_KEY.'_expire'); 
    delete_user_meta($user_meta->user_id, ECPM_BUA_META_KEY.'_notes'); 
  } 
}

function ecpm_bua_cron() {
  global $wpdb;
  $sql = "SELECT user_id FROM $wpdb->usermeta WHERE meta_key = '".ECPM_BUA_META_KEY."_expire' AND meta_value <= '".current_time('mysql')."'";

  $results = $wpdb->get_results( $sql );

  foreach ($results as $user_meta) {
    ecpm_bua_clean_user_meta($user_meta->user_id);
  } 
}

function ecpm_bua_enqueuescripts()	{
  wp_enqueue_style('ecpm_bua_style', plugins_url('css/ecpm-bua-admin.css', __FILE__));

  wp_enqueue_style( 'wp-color-picker' );
  wp_enqueue_script( 'ecpm-bua-color-script', plugins_url( 'js/ecpm-bua-adm.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
  wp_enqueue_media(); // upload engine
} 

function ecpm_bua_get_blocked_users($count = false) {
  
  $users = get_users( array('meta_key' => ECPM_BUA_META_KEY, 'meta_value' => 'on') );
  
  if ($count)
    return count($users);
  else
   return $users;   
} 

function ecpm_bua_block_user( $template ) {
  global $current_user;
  
  $user_blocked = get_user_meta( $current_user->ID, ECPM_BUA_META_KEY, true );
  if ($user_blocked == 'on') {
    if ( strposa($template, array('create-listing', 'renew-listing' )) ) {
      if ( ecpm_bua_is_cp4() )
        $template = plugin_dir_path( __FILE__ ).'templates/ecpm-bua-blocked-template-4.php';
      else   
        $template = plugin_dir_path( __FILE__ ).'templates/ecpm-bua-blocked-template.php';
    }  
  }    
  
  return $template;
}

function ecpm_bua_is_cp4() {
  if ( defined("CP_VERSION") )
    $cp_version = CP_VERSION;
  else   
    $cp_version = get_option('cp_version');
    
  if (version_compare($cp_version, '4.0.0') >= 0) {
    return true;
  } 
  
  return false;
}

function strposa($haystack, $needles=array(), $offset=0) {
  $chr = array();
  foreach($needles as $needle) {
          $res = strpos($haystack, $needle, $offset);
          if ($res !== false) $chr[$needle] = $res;
  }
  if(empty($chr)) return false;
  return min($chr);
}

function ecpm_bua_show_text($position = 'top'){
  $ecpm_bua_settings = get_option('ecpm_bua_settings');
              
  if ($position == 'top') {
    $show_top = $ecpm_bua_settings['show_top'];
    if ($show_top == 'on') {
      $top_style = '';
      
      if ($ecpm_bua_settings['top_size'] != '')
        $top_style .= '; font-size:'.$ecpm_bua_settings['top_size'];
        
      if (isset($ecpm_bua_settings['top_weight']) && $ecpm_bua_settings['top_weight'] != 'normal')
        $top_style .= '; font-weight-'.$ecpm_bua_settings['top_weight'];
        
      if (isset($ecpm_bua_settings['top_style']) && $ecpm_bua_settings['top_style'] != 'normal')
        $top_style .= '; font-style:'.$ecpm_bua_settings['top_style'];  
        
      if (isset($ecpm_bua_settings['top_decoration']) && $ecpm_bua_settings['top_decoration'] != 'none')
        $top_style .= '; text-decoration-'.$ecpm_bua_settings['top_decoration'];
      
      if (isset($ecpm_bua_settings['top_transform']) && $ecpm_bua_settings['top_transform'] != 'none')
        $top_style .= '; transform-'.$ecpm_bua_settings['top_transform'];
        
      if ($ecpm_bua_settings['top_color'] != '')
        $top_style .= '; color:'.$ecpm_bua_settings['top_color'].';';
        
      $top_html = $ecpm_bua_settings['top_text'];
      echo '<p align="center" style="'.$top_style.';">'.$top_html.'</p>';  
    }
  } else {
  
    $show_bottom = $ecpm_bua_settings['show_bottom'];
    if ($show_bottom == 'on') {
      $bottom_style = '';
      
      if ($ecpm_bua_settings['bottom_size'] != '')
        $bottom_style .= '; font-size:'.$ecpm_bua_settings['bottom_size'];
        
      if (isset($ecpm_bua_settings['bottom_weight']) && $ecpm_bua_settings['bottom_weight'] != 'normal')
        $bottom_style .= '; font-weight:'.$ecpm_bua_settings['bottom_weight'];
        
      if (isset($ecpm_bua_settings['bottom_style']) && $ecpm_bua_settings['bottom_style'] != 'normal')
        $bottom_style .= '; font-style:'.$ecpm_bua_settings['bottom_style'];  
        
      if (isset($ecpm_bua_settings['bottom_decoration']) && $ecpm_bua_settings['bottom_decoration'] != 'none')
        $bottom_style .= '; text-decoration:'.$ecpm_bua_settings['bottom_decoration'];
      
      if (isset($ecpm_bua_settings['bottom_transform']) && $ecpm_bua_settings['bottom_transform'] != 'none')
        $bottom_style .= '; transform:'.$ecpm_bua_settings['bottom_transform'];
        
      if ($ecpm_bua_settings['bottom_color'] != '')
        $bottom_style .= '; color:'.$ecpm_bua_settings['bottom_color'].';';
        
      $bottom_html = $ecpm_bua_settings['bottom_text'];
      $bottom_html = str_replace('[dashboard]', CP_DASHBOARD_URL, $bottom_html);
      $bottom_html = str_replace('[home]', esc_url(home_url('/')), $bottom_html);
      
      echo '<p align="center" style="'.$bottom_style.';">'.$bottom_html.'</p>';
    }
  }  
}

function ecpm_bua_show_ad($position = 'top') {
  $ecpm_bua_settings = get_option('ecpm_bua_settings');
  
  if ($position == 'top') {
    if ( $ecpm_bua_settings['show_top_ad'] == 'on' ) {
      echo stripslashes( $ecpm_bua_settings['top_ad'] );
    }
  } else {  
    if ( $ecpm_bua_settings['show_bottom_ad'] == 'on' ) {
      echo stripslashes( $ecpm_bua_settings['bottom_ad'] );
    }
  }  
}

function ecpm_bua_show_image($image_pos) {
  $ecpm_bua_settings = get_option('ecpm_bua_settings');
  
  if ($ecpm_bua_settings['image_position'] != $image_pos)
    return;
    
  $access_denied_icon = $ecpm_bua_settings['image'];    
  ?>
  <p align="center">
  <img src="<?php echo $access_denied_icon;?>">
  </p>
  <?php
}

function ecpm_bua_get_avail_users() {
  $blocked_arr = array();
  $blocked = ecpm_bua_get_blocked_users();
  foreach ($blocked as $user) {
    $blocked_arr[] = $user->ID;
  }      

  $args = array(
  	'exclude'      => $blocked_arr,
    'orderby'      => 'login',
  	'order'        => 'ASC',
  	'fields'       => array('ID', 'user_login', 'user_email'),
   ); 
  
  return get_users( $args );
  
}

function ecpm_bua_count_ads($user_id) {
	global $wpdb;

	$result = $wpdb->get_row( "SELECT COUNT(*) FROM $wpdb->posts WHERE post_type = '" . APP_POST_TYPE . "' AND post_author = $user_id", ARRAY_N );
	return $result[0];
}
 

?>