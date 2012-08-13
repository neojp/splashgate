<?php
/*
Plugin Name: SplashGate
Plugin URI: http://zaen.co/wp/splashgate
Description: Allows wordpress page content to be used as a splash page or splash overlay. <em>Does not</em> provide any template for a Splash Page - template must be created separately.
Author: zaen, neojp
Version: 1.1
Author URI: http://zaen.co
*/

// Do not directly access this file
if (!function_exists ('add_action')) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}

define('SPLASHGATE_OPTIONS', 'splashgate_options');
define('SPLASHGATE_SETTINGS_GROUP', 'splashgate-settings-group');


if (!class_exists("SplashGate")){
	class SplashGate {

		function SplashGate() { // Constructor
			add_action('init', array(&$this, 'splashgate_init_fancybox') );
			add_filter('screen_layout_columns', array(&$this, 'on_screen_layout_columns'), 10, 2);
			add_action('admin_menu', array(&$this, 'on_admin_menu')); 
			add_action('admin_init', array(&$this, 'splashgate_admin_init') );
			add_action('save_post', array(&$this, 'splashgate_splashable_save'), 1, 2);
			add_action('wp_head', array(&$this, 'splashgate_prime') );
			add_action('wp_footer', array(&$this, 'splashgate_modal_markup') );
			add_action('template_redirect', array(&$this, 'intercept_request') );
		}

		function __construct() {
			$this->SplashGate();
		}
		
		function splashgate_init_fancybox() {
			$splashgate_options = array();
			$splashgate_options = get_option('splashgate_options');
			$need_fancy = false;
				
			// enqueue jquery if needed
			if (!is_admin() && !empty($splashgate_options['active'])) {
				if ($splashgate_options['active']) {
					if (!empty($splashgate_options['style']) && $splashgate_options['style'] == 2) {
						$need_fancy = true;
					}
				}
			}
			
			if ($need_fancy) {
				wp_deregister_script('jquery');
				wp_register_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.4.3/jquery.min.js');
				wp_enqueue_script('jquery');
				wp_enqueue_script('fancybox', WP_PLUGIN_URL . '/' . str_replace(basename( __FILE__), "js/jquery.fancybox-1.3.4.pack.js", plugin_basename(__FILE__)), array('jquery'), '1.3.4');
				wp_enqueue_style('splashgate-fancybox', WP_PLUGIN_URL . '/' . str_replace(basename( __FILE__), "css/fancybox.css", plugin_basename(__FILE__)) );
			}
		} // End splashgate_init_fancybox
		
		function splashgate_modal_markup(){
			
			$splashgate_options = array();
			$splashgate_options = get_option('splashgate_options');
			$active = false;
			$modal_markup = "";
			$splash_path = "#";
			
			if(!empty($splashgate_options['active'])){
				if($splashgate_options['active']){
					$active = true;
				}
			}
			
			if($active){
				if( is_front_page() || is_home() ){
					if(!empty($splashgate_options['style'])){
						if($splashgate_options['style'] == 2){ // fancybox style - mark up needed
							if(!empty($splashgate_options['splashpage_id'])){
								$splash_path = get_permalink( $splashgate_options['splashpage_id']);
							} 
							$modal_markup = '<a id="splashgate-modal" style="display:none;" href="' . $splash_path . '"></a>';
						}
					}
				}
			}
		
			echo $modal_markup;
			
		}
 
		/**  SPLASH PAGE FRONT END *** 
		  *  Add JS to homepage when splash is active
		  */
		function splashgate_prime(){
			$splashgate_options = array();
			$splashgate_options = get_option('splashgate_options');
			$active = false;
			$do_splash_js = "";
			$splashgate_fancybox_js = "";
			
			if(!empty($splashgate_options['active'])){
				if($splashgate_options['active']){
					$active = true;
				}
			}
			
			if ($active) {
				if( is_front_page() || is_home() ){
				
					$splashgate_style = 'page';
					$duration = 24; //hours
					
					if (!empty($splashgate_options['recurrance'])) {
						if ($splashgate_options['recurrance'] == -1) {
							$duration = 0;
						} else {
							$duration = $splashgate_options['recurrance'];
						}
					}
					
					if (!empty($splashgate_options['style']) && $splashgate_options['style'] == 2) {
						$splashgate_style = 'fancybox';
					}
					
					if ($splashgate_style == 'fancybox') {
						$window_width  = 414;
						$window_height = 360;

						if (!empty($splashgate_options['window_width'])) {
							$window_width = $splashgate_options['window_width'];
						}

						if (!empty($splashgate_options['window_height'])) {
							$window_height = $splashgate_options['window_height'];
						}
					
						// INLINE SCRIPT for fancybox dynamic vars 
						$do_splash_js = "$('#splashgate-modal').trigger('click');";
						$splashgate_fancybox_js = "SplashGate = {
								fancybox: {
									width: {$window_width},
									height: {$window_height},
									autoScale: false,
									transitionIn: 'none',
									transitionOut: 'none',
									type: 'iframe'
								}
							};";
					
					} else { // page (full redirect) style
						$do_splash_js = "document.location = splash_path;";
					} // end if fancybox/page style
					
					include dirname(__FILE__) . '/tpl/prime.php';
				} // end if front_page
			} // end if active
			
		} // end splashgate_prime()
		

		/**  SPLASH PAGE URL ENDPOINT *** 
		  *  Reserve and pick off a URL for redirecting to dynamic splash page
		  */
		function intercept_request(){
			global $wp_query;
			$rqt = isset($wp_query->query_vars['pagename']) ? $wp_query->query_vars['pagename'] : '';

			if ($rqt === 'splashgate') {
				$splashgate_options = array();
				$splashgate_options = get_option('splashgate_options');
				
				if (!empty($splashgate_options['splashpage_id'])) {
					$splash_link = get_permalink( $splashgate_options['splashpage_id'] );
					if ($splash_link) {
						wp_redirect( $splash_link );
						exit;
					}
				}
			}
		} // end intercept_request()
		
		
		/**  SPLASH PAGE MANAGER *** 
		  *  Handle admin menu and main settings
		  */
		function splashgate_admin_init(){
			// 'splashgate_options' will be an array holding everything
			register_setting(SPLASHGATE_SETTINGS_GROUP, 'splashgate_options');
		}


		function on_screen_layout_columns($columns, $screen) {
			if ($screen == $this->pagehook) {
				$columns[$this->pagehook] = 2;
			}

			return $columns;
		}


		function on_admin_menu() {
			$this->pagehook = add_options_page('SplashGate Splash Options', "SplashGate", 'manage_options', SPLASHGATE_OPTIONS, array(&$this, 'on_show_page'));
			add_action('load-' . $this->pagehook, array(&$this, 'on_load_page'));
			
			// Add Metabox to all page admin
			add_meta_box(
				'splashgate-page-option-1',
				__( 'Splashability', 'splashgate_textdomain' ),
				array(&$this, 'on_splashgate_pages_admin'),
				'page',
				'side'
			);
		}
		
		
		function on_load_page() {
			wp_enqueue_script('common');
			wp_enqueue_script('wp-lists');
			wp_enqueue_script('postbox');

			add_meta_box(
				'splashgate-contentbox-1',
				'Page to Splash',
				array(&$this, 'on_contentbox_1_content'),
				$this->pagehook,
				'normal',
				'core'
			);

			add_meta_box(
				'splashgate-sidebox-1',
				'Splash Status',
				array(&$this, 'on_sidebox_1_content'),
				$this->pagehook,
				'side',
				'high'
			);

			add_meta_box(
				'splashgate-sidebox-2',
				'Splash Recurrance', array(&$this, 'on_sidebox_2_content'),
				$this->pagehook,
				'side',
				'low'
			);

			add_meta_box(
				'splashgate-sidebox-3',
				'Splash Style',
				array(&$this, 'on_sidebox_3_content'),
				$this->pagehook,
				'side',
				'low'
			);
		} // end on_load_page()
		

		function on_show_page() {
			global $screen_layout_columns;

			if ($screen_layout_columns === 0) {
				$screen_layout_columns = 2;
			}
			
			$splashgate_options = get_option('splashgate_options');

			include dirname(__FILE__) . '/tpl/showpage.php';

		} // end on_show_page()
		

		// SELECT SPLASH PAGE
		function on_contentbox_1_content($splashgate_options) {
			global $post;
			query_posts('post_type=page&meta_key=_splashgate_splashable&meta_value=on&order=desc&orderby=date');
			$admin_url = admin_url('/edit.php?post_type=page',__FILE__);
			include dirname(__FILE__) . '/tpl/contentbox1.php';
			wp_reset_query();
		}
		
		// STATUS
		function on_sidebox_1_content($options) {
			include dirname(__FILE__) . '/tpl/sidebox1.php';
		}
		
		// RECURRANCE
		function on_sidebox_2_content($options) {
			include dirname(__FILE__) . '/tpl/sidebox2.php';
		}
		
		// SPLASH STYLE OPTS
		function on_sidebox_3_content($options) {
			include dirname(__FILE__) . '/tpl/sidebox3.php';
		}

		static function selected_opt($a, $b){
			if($a == $b){
				echo "selected=\"selected\"";
			}	
			return;
		}
		/*** END SPLASH MANAGER **/
		
		
		
		/**  SPLASHABLE *** 
		  *  This handles sidebar meta-box on 'Page' type admin pages 
		  */
		function on_splashgate_pages_admin() {
			global $post;
			$nonce = wp_create_nonce(plugin_basename(__FILE__));
			include dirname(__FILE__) . '/tpl/pageadmin.php';
		} // end on_splashgate_pages_admin()
		
		function splashgate_splashable_save(){
			global $post;

			// verify this came from the our screen and with proper authorization,
			// because save_post can be triggered at other times
			if ( !wp_verify_nonce( $_POST['splashgate_splashable_noncename'], plugin_basename(__FILE__) )) {
				return $post->ID;
			}

			// Is the user allowed to edit
			if ( !current_user_can('edit_post', $post->ID)) {
				return $post->ID;
			}

			// Set all options here
			$splashgate_meta = array();
			$splashgate_meta['_splashgate_splashable'] = filter_input(INPUT_POST, 'splashgate_splashable', FILTER_SANITIZE_STRING);

			// Cycle through the $events_meta array!
			foreach ($splashgate_meta as $key => $value) {
				
				// Don't store custom data twice
				if ($post->post_type == 'revision') {
					return;
				}
				
				// If $value is an array, make it a CSV (unlikely)
				$value = implode(',', (array)$value);
				
				// If the custom field already has a value
				if(get_post_meta($post->ID, $key, FALSE)) {
					update_post_meta($post->ID, $key, $value);
				
				// If the custom field doesn't have a value
				} else {
					add_post_meta($post->ID, $key, $value);
				}
				
				// Delete if blank
				if (!$value) {
					delete_post_meta($post->ID, $key);
				}
			}

		} // end splashgate_splashable_save()
		
		
		/*** END SPLASHABLE PAGES ***/
		
	} // end class
} // end if class !exist


$splashgate = new SplashGate();