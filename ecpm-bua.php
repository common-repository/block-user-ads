<?php
/*
Plugin Name: Block User Ads
Plugin URI: http://www.easycpmods.com
Description: Block User Ads is a lightweight plugin that will allow you to block users from posting ads. It requires Classipress theme to be installed.
Author: EasyCPMods
Version: 1.4.1
Text Domain: ecpm-bua
*/

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
} 

define('ECPM_BUA', 'ecpm-bua');
define('ECPM_BUA_NAME', 'Block User Ads');
define('ECPM_BUA_VERSION', '1.4.1');
define('ECPM_BUA_META_KEY', 'ecpm_bua_user_blocked');

include_once( plugin_dir_path(__FILE__) ."/ecpm-bua-functions.php" );

register_activation_hook( __FILE__, 'ecpm_bua_activate');
register_deactivation_hook( __FILE__, 'ecpm_bua_deactivate');
register_uninstall_hook( __FILE__, 'ecpm_bua_uninstall');

add_action('admin_enqueue_scripts', 'ecpm_bua_enqueuescripts');

add_action('plugins_loaded', 'ecpm_bua_plugins_loaded');
add_action('admin_init', 'ecpm_bua_requires_version');
add_action('admin_menu', 'ecpm_bua_create_menu_set', 11);
add_action( 'block_user_ads', 'ecpm_bua_cron');

add_filter( 'template_include', 'ecpm_bua_block_user', 99 );

function ecpm_bua_create_menu_set() {
  if ( is_plugin_active('easycpmods-toolbox/ecpm-toolbox.php') ) {
    $ecpm_etb_settings = get_option('ecpm_etb_settings');
    if ($ecpm_etb_settings['group_settings'] == 'on') {
      add_submenu_page( 'ecpm-menu', ECPM_BUA_NAME, ECPM_BUA_NAME, 'manage_options', 'ecpm_bua_settings_page', 'ecpm_bua_settings_page_callback' );
      return;
    }
  }
  add_options_page(ECPM_BUA_NAME, ECPM_BUA_NAME, 'manage_options', 'ecpm_bua_settings_page', 'ecpm_bua_settings_page_callback');
}    
  
function ecpm_bua_calc_expire_date($expire_num, $expire_time) {
  if ($expire_num == '')
    return false;

  switch ($expire_time) {
    case 'day':
      $days = $expire_num;
      break;
    case 'week':
      $days = $expire_num * 7;
      break;
    case 'month':
      $days = $expire_num * 30;
      break;
    case 'year':
      $days = $expire_num * 365;      
      break;
  }

  return $days;
}

function ecpm_bua_settings_page_callback() {
  $ecpm_bua_settings = get_option('ecpm_bua_settings');
    
  if ( current_user_can( 'manage_options' ) ) {
    
    if ( isset($_POST['bua_block']) ) {
      $block_user = $_POST['ecpm_bua_block_user'];
      $add_days = ecpm_bua_calc_expire_date($_POST['ecpm_bua_expire'], $_POST['ecpm_bua_expire_time']);

      update_user_meta( $block_user, ECPM_BUA_META_KEY, 'on' );
      update_user_meta( $block_user, ECPM_BUA_META_KEY.'_date', appthemes_mysql_date( current_time( 'mysql' ) ) );
      
      if ($add_days)
        update_user_meta( $block_user, ECPM_BUA_META_KEY.'_expire', appthemes_mysql_date(current_time( 'mysql' ), $add_days ) );
      
      update_user_meta( $block_user, ECPM_BUA_META_KEY.'_notes', sanitize_text_field($_POST['ecpm_bua_notes'] ) );
    }
    
    if ( isset($_GET['bua_unblock']) ) {
      $unblock_user = $_GET['bua_unblock'];
      ecpm_bua_clean_user_meta($unblock_user);
    }
    
    if( isset( $_POST['ecpm_bua_submit'] ) )
  	{
      $avail_sizes = array('xx-small', 'x-small', 'small', 'medium', 'large', 'x-large', 'xx-large', 'smaller', 'larger');
      $avail_weights = array('normal', 'lighter', 'bold', 'bolder');
      $avail_decoration = array('none', 'underline', 'overline', 'line-through');
      $avail_transform = array('none', 'capitalize', 'uppercase', 'lowercase');
      $avail_styles = array('normal', 'italic', 'oblique', 'initial');
      $avail_img_pos = array('top', 'middle', 'bottom', 'hide');
      
      if ( isset($_POST[ 'ecpm_bua_show_top_ad' ]) && $_POST[ 'ecpm_bua_show_top_ad' ] == 'on' )
        $ecpm_bua_settings['show_top_ad'] = sanitize_text_field( $_POST[ 'ecpm_bua_show_top_ad' ] );
      else
        $ecpm_bua_settings['show_top_ad'] = '';
        
      $ecpm_bua_settings['top_ad'] = appthemes_clean($_POST[ 'ecpm_bua_top_ad' ]);  
        
      if ( isset($_POST[ 'ecpm_bua_show_top' ]) && $_POST[ 'ecpm_bua_show_top' ] == 'on' )
        $ecpm_bua_settings['show_top'] = sanitize_text_field( $_POST[ 'ecpm_bua_show_top' ] );
      else
        $ecpm_bua_settings['show_top'] = '';
        
      $ecpm_bua_settings['top_text'] = wp_kses_post($_POST[ 'ecpm_bua_top_text' ]);
      
      if ( isset($_POST[ 'ecpm_bua_top_size' ]) && in_array($_POST[ 'ecpm_bua_top_size' ], $avail_sizes) )
        $ecpm_bua_settings['top_size'] = sanitize_text_field( $_POST[ 'ecpm_bua_top_size' ] );
      else
        $ecpm_bua_settings['top_size'] = '';
        
      if ( isset($_POST[ 'ecpm_bua_top_weight' ]) && in_array($_POST[ 'ecpm_bua_top_weight' ], $avail_weights) )
        $ecpm_bua_settings['top_weight'] = sanitize_text_field( $_POST[ 'ecpm_bua_top_weight' ] );
      else
        $ecpm_bua_settings['top_weight'] = '';  
        
      if ( isset($_POST[ 'ecpm_bua_top_style' ]) && in_array($_POST[ 'ecpm_bua_top_style' ], $avail_styles) )
        $ecpm_bua_settings['top_style'] = sanitize_text_field( $_POST[ 'ecpm_bua_top_style' ] );
      else
        $ecpm_bua_settings['top_style'] = '';  
        
      if ( isset($_POST[ 'ecpm_bua_top_decoration' ]) && in_array($_POST[ 'ecpm_bua_top_decoration' ], $avail_decoration) )
        $ecpm_bua_settings['top_decoration'] = sanitize_text_field( $_POST[ 'ecpm_bua_top_decoration' ] );
      else
        $ecpm_bua_settings['top_decoration'] = '';
        
      if ( isset($_POST[ 'ecpm_bua_top_transform' ]) && in_array($_POST[ 'ecpm_bua_top_transform' ], $avail_transform) )
        $ecpm_bua_settings['top_transform'] = sanitize_text_field( $_POST[ 'ecpm_bua_top_transform' ] );
      else
        $ecpm_bua_settings['top_transform'] = '';  
        
      $ecpm_bua_settings['top_color'] = sanitize_text_field($_POST[ 'ecpm_bua_top_color' ]);  
      
      
      if ( isset($_POST[ 'ecpm_bua_show_bottom_ad' ]) && $_POST[ 'ecpm_bua_show_bottom_ad' ] == 'on' )
        $ecpm_bua_settings['show_bottom_ad'] = sanitize_text_field( $_POST[ 'ecpm_bua_show_bottom_ad' ] );
      else
        $ecpm_bua_settings['show_bottom_ad'] = '';
      
      $ecpm_bua_settings['bottom_ad'] = appthemes_clean($_POST[ 'ecpm_bua_bottom_ad' ]);  
        
      if ( isset($_POST[ 'ecpm_bua_show_bottom' ]) && $_POST[ 'ecpm_bua_show_bottom' ] == 'on' )
        $ecpm_bua_settings['show_bottom'] = sanitize_text_field( $_POST[ 'ecpm_bua_show_bottom' ] );
      else
        $ecpm_bua_settings['show_bottom'] = '';
        
      $ecpm_bua_settings['bottom_text'] = wp_kses_post($_POST[ 'ecpm_bua_bottom_text' ]);
      
      if ( isset($_POST[ 'ecpm_bua_bottom_size' ]) && in_array($_POST[ 'ecpm_bua_bottom_size' ], $avail_sizes) )
        $ecpm_bua_settings['bottom_size'] = sanitize_text_field( $_POST[ 'ecpm_bua_bottom_size' ] );
      else
        $ecpm_bua_settings['bottom_size'] = '';
        
      if ( isset($_POST[ 'ecpm_bua_bottom_weight' ]) && in_array($_POST[ 'ecpm_bua_bottom_weight' ], $avail_weights) )
        $ecpm_bua_settings['bottom_weight'] = sanitize_text_field( $_POST[ 'ecpm_bua_bottom_weight' ] );
      else
        $ecpm_bua_settings['bottom_weight'] = '';
        
      if ( isset($_POST[ 'ecpm_bua_bottom_style' ]) && in_array($_POST[ 'ecpm_bua_bottom_style' ], $avail_styles) )
        $ecpm_bua_settings['bottom_style'] = sanitize_text_field( $_POST[ 'ecpm_bua_bottom_style' ] );
      else
        $ecpm_bua_settings['bottom_style'] = '';    
        
      if ( isset($_POST[ 'ecpm_bua_bottom_decoration' ]) && in_array($_POST[ 'ecpm_bua_bottom_decoration' ], $avail_decoration) )
        $ecpm_bua_settings['bottom_decoration'] = sanitize_text_field( $_POST[ 'ecpm_bua_bottom_decoration' ] );
      else
        $ecpm_bua_settings['bottom_decoration'] = '';
        
      if ( isset($_POST[ 'ecpm_bua_bottom_transform' ]) && in_array($_POST[ 'ecpm_bua_bottom_transform' ], $avail_transform) )
        $ecpm_bua_settings['bottom_transform'] = sanitize_text_field( $_POST[ 'ecpm_bua_bottom_transform' ] );
      else
        $ecpm_bua_settings['bottom_transform'] = '';  
        
      $ecpm_bua_settings['bottom_color'] = sanitize_text_field($_POST[ 'ecpm_bua_bottom_color' ]);  
      
      
      if ( isset($_POST[ 'ecpm_bua_image'] ) )          
        $ecpm_bua_settings['image'] = appthemes_clean($_POST[ 'ecpm_bua_image' ]);
      
      if ( isset($_POST[ 'ecpm_bua_image_position' ]) && in_array($_POST[ 'ecpm_bua_image_position' ], $avail_img_pos) )
        $ecpm_bua_settings['image_position'] = sanitize_text_field( $_POST[ 'ecpm_bua_image_position' ] );
      else
        $ecpm_bua_settings['image_position'] = 'middle';   

      if ( isset($_POST[ 'ecpm_bua_remove_userdata' ]) && $_POST[ 'ecpm_bua_remove_userdata' ] == 'on' )
        $ecpm_bua_settings['remove_userdata'] = sanitize_text_field( $_POST[ 'ecpm_bua_remove_userdata' ] );
      else
        $ecpm_bua_settings['remove_userdata'] = '';  
        
      update_option( 'ecpm_bua_settings', $ecpm_bua_settings );
      
      echo scb_admin_notice( __( 'Settings saved.', APP_TD ), 'updated' );
      
      ecpm_bua_clean_user_meta();
      //ecpm_bua_cron();
  	}
  }
  
  $form_url = remove_query_arg(array('bua_unblock', 'bua_block')); 
  
  ?>
  
  <script type="text/javascript">
  function confirmBuaDelete(text) {
  	return confirm(text);
  }
  </script>
  
		<div id="buasetting">
			<div class="wrap">
      <h1><?php echo ECPM_BUA_NAME; ?></h1>
        <?php
        echo "<i>Plugin version: <u>".ECPM_BUA_VERSION."</u>";
        echo "<br>Plugin language file: <u>ecpm-bua-".get_locale().".mo</u></i>";
        ?>
        <hr>
        <div id='bua-container-left' style='float: left; margin-right: 285px;'>      
        <?php
          $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'bua_settings';
          ?>
          <form id='buasettingform' method="post" action="<?php echo $form_url;?>">
          <h2 class="bua-nav-tab-wrapper">
            <a id="link-tab" class="bua-nav-tab bua-nav-tab-active" href="<?php echo admin_url();?>index.php?page=bua-blocked"><?php echo _e('Blocked users', ECPM_BUA). " (".ecpm_bua_get_blocked_users(true).")"; ?></a>  
            <a id="link-tab" class="bua-nav-tab" href="<?php echo admin_url();?>index.php?page=bua-image"><?php echo _e('Image settings', ECPM_BUA);?></a>
            <a id="link-tab" class="bua-nav-tab" href="<?php echo admin_url();?>index.php?page=bua-top"><?php echo _e('Top notice', ECPM_BUA);?></a>
            <a id="link-tab" class="bua-nav-tab" href="<?php echo admin_url();?>index.php?page=bua-bottom"><?php echo _e('Bottom notice', ECPM_BUA);?></a>
            <a id="link-tab" class="bua-nav-tab" href="<?php echo admin_url();?>index.php?page=bua-ad"><?php echo _e('Ad settings', ECPM_BUA);?></a>
            <a id="link-tab" class="bua-nav-tab" href="<?php echo admin_url();?>index.php?page=bua-settings"><?php echo _e('Settings', ECPM_BUA); ?></a>
          </h2> 
      
          <?php require_once( plugin_dir_path(__FILE__). '/ecpm-bua-settings.php' );?>
          
          <hr>
          <p>
            <input type="submit" id="ecpm_bua_submit" name="ecpm_bua_submit" class="button-primary" value="<?php _e('Save settings', ECPM_BUA); ?>" />
          </p>
          
        </form>
 
        
      </div>
        
      <div id='bua-container-right' class='nocloud' style='border: 1px solid #e5e5e5; float: right; margin-left: -275px; padding: 0em 1.5em 1em; background-color: #fff; box-shadow:10px 10px 5px #888888; display: inline-block; width: 234px;'>
        <h3>Thank you for using</h3>
        <h2><?php echo ECPM_BUA_NAME;?></h2>
        <hr>
        <?php include_once( plugin_dir_path(__FILE__)."/image_sidebar.php" );?>
      </div>
    
	  </div>
  </div>
<?php
}
?>