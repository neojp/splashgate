<?php
/*
Plugin Name: SplashGate
Plugin URI: http://zaen.co/wp/splashgate
Description: Allows wordpress page content to be used as a splash page or splash overlay. <em>Does not</em> provide any template for a Splash Page - template must be created separately.
Author: zaen, neojp
Version: 1.1
Author URI: http://zaen.co
*/


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
			add_action('admin_init',  array(&$this, 'splashgate_admin_init') );
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
				//wp_enqueue_script('fancybox', WP_PLUGIN_URL . '/' . str_replace(basename( __FILE__), "js/jquery.fancybox-1.3.4.pack.js", plugin_basename(__FILE__)), array('jquery'), '1.3.4');
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
						$window_width = 414;
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
					?>
					<script type="text/javascript">
					function splashgate_get_qs_param(name) {
  				  return decodeURIComponent((new RegExp('[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)').exec(location.search)||[,""])[1].replace(/\+/g, '%20'))||null;
  				}
  				<?php print $splashgate_fancybox_js; ?>
					(function($) {
								$(document).ready(function() {
									if(typeof SplashGate != 'undefined'){$('#splashgate-modal').fancybox(SplashGate.fancybox);}
									function splashCookie(){
										var splash_path = "<?php bloginfo('url'); ?>/?pagename=splashgate";
										var splash_hours = <?php print $duration; ?>;
										var key = 'splashgate';
										var value = 1;
										var expires = new Date();
										var keyValue = document.cookie.match('(^|;) ?' + key + '=([^;]*)(;|$)');
										var splasharg = splashgate_get_qs_param('splash');
										if ( document.referrer.indexOf("<?php bloginfo('url'); ?>") != 0 ) {
											if (!keyValue && splasharg!='no') {
												expires.setTime(expires.getTime() + splash_hours * 3600000);
												document.cookie = key + '=' + value + ';expires=' + expires.toUTCString();<?php print $do_splash_js; ?>} else if(splasharg=='yes'){<?php print $do_splash_js; ?>}
										}
									}
									splashCookie();
								});
							})(jQuery);
						</script>
						
						<?php
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
			register_setting(	SPLASHGATE_SETTINGS_GROUP, 'splashgate_options' ); // 'options' will be an array holding everything
		}

		function on_screen_layout_columns($columns, $screen) {
			if ($screen == $this->pagehook) {
				$columns[$this->pagehook] = 2;
			}
			return $columns;
		}		

		function on_admin_menu() {
			$this->pagehook = add_options_page('SplashGate Splash Options', "SplashGate", 'manage_options', SPLASHGATE_OPTIONS, array(&$this, 'on_show_page'));
			add_action('load-'.$this->pagehook, array(&$this, 'on_load_page'));
			
			// Add Metabox to all page admin
			 add_meta_box( 'splashgate-page-option-1', __( 'Splashability', 'splashgate_textdomain' ), array(&$this, 'on_splashgate_pages_admin'), 'page', 'side' );
                
		}
		
		
		function on_load_page() {
			wp_enqueue_script('common');
			wp_enqueue_script('wp-lists');
			wp_enqueue_script('postbox');	
			add_meta_box('splashgate-contentbox-1', 'Page to Splash', array(&$this, 'on_contentbox_1_content'), $this->pagehook, 'normal', 'core' );
			add_meta_box('splashgate-sidebox-1', 'Splash Status', array(&$this, 'on_sidebox_1_content'), $this->pagehook, 'side', 'high' );
			add_meta_box('splashgate-sidebox-2', 'Splash Recurrance', array(&$this, 'on_sidebox_2_content'), $this->pagehook, 'side', 'low' );
			add_meta_box('splashgate-sidebox-3', 'Splash Style', array(&$this, 'on_sidebox_3_content'), $this->pagehook, 'side', 'low' );
		}
		

		function on_show_page() {
			global $screen_layout_columns;
			if($screen_layout_columns === 0){
  			$screen_layout_columns = 2;
			}
			
			$splashgate_options = array();
			$splashgate_options = get_option('splashgate_options');
			?>
			<style>
			.splashgate-color1{
				color:#00ABDF;
			}
			.splashgate-status{
				padding:5px;
				border-radius: 5px;
				-moz-border-radius: 5px;
				-webkit-border-radius:5px;
				text-align:center;
				font:bold 12px/22px arial,helvetica,sans-serif;
			}
			.splashgate-active{
				background:#00ABDF;
				color:#FFF;
				text-shadow: 1px 1px 1px #249;
			}		
			.splashgate-inactive{
				background:#A66;
				color:#FFF;
				text-shadow: 1px 1px 1px #621;
			}
			.splashgate-active-desc{
				
			}
			.splashgate-active-title{
				font-style:italic;
			}
			</style>
			<div id="splashgate-general" class="wrap">
			<?php screen_icon('options-general'); ?>
			<h2><span class="splashgate-color1">SplashGate</span> Splash Management</h2>
			<form method="post" action="options.php">

				<?php wp_nonce_field('splashgate-general'); ?>
				<?php wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false ); ?>
				<?php wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false ); ?>
				<?php settings_fields(SPLASHGATE_SETTINGS_GROUP); ?>
				
				<div id="poststuff" class="metabox-holder<?php echo 2 == $screen_layout_columns ? ' has-right-sidebar' : ''; ?>">
					<div id="side-info-column" class="inner-sidebar">
						<?php do_meta_boxes($this->pagehook, 'side', $splashgate_options); ?>
					</div>
					<div id="post-body" class="has-sidebar">
						<div id="post-body-content" class="has-sidebar-content">
							<?php do_meta_boxes($this->pagehook, 'normal', $splashgate_options); ?>							
							<p>
								<input type="submit" value="Save Changes" class="button-primary" name="Submit"/>	
							</p>
						</div>
					</div>
					<br class="clear"/>
									
				</div>	
			</form>
			</div>
		<script type="text/javascript">
			//<![CDATA[
			jQuery(document).ready( function($) {
				// close postboxes that should be closed
				$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
				// postboxes setup
				postboxes.add_postbox_toggles('<?php echo $this->pagehook; ?>');
			});
			//]]>
		</script>
			
			<?php
		} // end on_show_page()	
		
		// SELECT SPLASH PAGE
		function on_contentbox_1_content($splashgate_options) { 
			
			// Can't do query_posts for meta_key, must use custom db query
			global $wpdb;
			
			$sql = "
		    SELECT wposts.* 
		    FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta
		    WHERE wposts.ID = wpostmeta.post_id 
		    AND wpostmeta.meta_key = '_splashgate_splashable' 
		    AND wpostmeta.meta_value = 'on' 
		    AND wposts.post_status = 'publish' 
		    AND wposts.post_type = 'page' 
		    ORDER BY wposts.post_date DESC;
		    ";
		
			$pageposts = $wpdb->get_results($sql, OBJECT);
 
			if ($pageposts){
				global $post;?>
				
				<p>Choose the page you'd like to splash from the list of splashable pages below:</p>
				<table cellpadding="0" cellspacing="0" class="widefat page splashable-list">
				<thead>
					<tr>
						<th scope="col" id="cb" class="manage-column column-cb check-column"></th>
						<th scope="col" id="title" class="manage-column column-title">Title</th>
						<th scope="col" id="author" class="manage-column column-author">Author</th>
						<th scope="col" id="date" class="manage-column column-date">Date</th>
					</tr>
				</thead><?php
				
				$zebra = '';
				foreach ($pageposts as $post){
					setup_postdata($post);
					
					$checked = "";
					if($post->ID == $splashgate_options['splashpage_id']){
						$checked = "checked=\"checked\"";
					}
					
					if($zebra == " alternate"){
						$zebra = "";
					} else {
						$zebra = " alternate";
					}
					?>
					
					<tr class="<?php echo $zebra;?>">
						<th scope="row" class="check-column"><input type="radio" name="splashgate_options[splashpage_id]" value="<?php echo $post->ID;?>" <?php echo $checked; ?>/></th>
						<td><a href="<?php the_permalink();?>"><?php the_title(); ?></a></td>
						<td><?php the_author(); ?></td>
						<td><?php the_time('F jS, Y') ?></td>
					</tr><?php
					
 				} // end foreach ?>
 				
 				</table>
 				<p style="color:#777;">Pages have to be made 'splashable' in order to use them.  To add a new page to this list, go <a href="<?php echo admin_url('/edit.php?post_type=page',__FILE__);?>">edit the page</a> you'd like to use and check the 'Splashable' checkbox available in the right sidebar.  Then come back here to finish the set up.</p><?php
 				
 			} else { ?>
    		<p style="display:block;color:#922;">There are no pages available for use.</p>
    		<p style="color:#777;">Pages have to be made 'splashable' in order to use them.  To make a page splashable, go <a href="<?php echo admin_url('/edit.php?post_type=page',__FILE__);?>">edit the page</a> you'd like to use and check the 'Splashable' checkbox available in the right sidebar.  Then come back here to finish the set up.</p><?php
 			}
 			
		}
		
		// STATUS
		function on_sidebox_1_content($splashgate_options) {
			
			$checked = "";
			$active_class = "splashgate-inactive";
			$active_msg = "Splash Page is OFF";
			$active_title = "";
			$active_display_style = "block";
			
			/** TO BE COMPLETED - active_display_style
			  *
			  * This needs to check and only allow Activation
			  * checkbox to be shown if at least on page
			  * is marked 'splashable'.
			  */
			
	 		if($splashgate_options['active']){
	 			$checked = "checked=\"checked\"";
	 			$active_class = "splashgate-active";
	 			$active_msg = "Splash Page is ON";
	 			$active_title = "<span class=\"splashgate-active-title\">" . get_the_title($splashgate_options['splashpage_id']) . "</span> is currently active.";
	 		} ?>
	 		
	 		<p class="splashgate-status <?php echo $active_class;?>"><?php echo $active_msg; ?></p>
			<div style="display:<?php echo $active_display_style; ?>">
		 		<p><input type="checkbox" name="splashgate_options[active]" <?php echo $checked; ?>/>
			  <label for="splashgate_options[active]">Active</label></p>
				<p class="splashgate-active-desc"><?php echo $active_title; ?></p>
			</div><?php
		}
		
		// RECURRANCE
		function on_sidebox_2_content($splashgate_options) { 
			
			?>
				<p><select name="splashgate_options[recurrance]">
					<optgroup label="Hours">
						<option value="1"<?php $this->splashgate_selected_opt($splashgate_options['recurrance'], '1'); ?>>1 hour</option>
						<option value="2"<?php $this->splashgate_selected_opt($splashgate_options['recurrance'], '2'); ?>>2 hours</option>
						<option value="3"<?php $this->splashgate_selected_opt($splashgate_options['recurrance'], '3'); ?>>3 hours</option>
						<option value="4"<?php $this->splashgate_selected_opt($splashgate_options['recurrance'], '4'); ?>>4 hours</option>
						<option value="8"<?php $this->splashgate_selected_opt($splashgate_options['recurrance'], '8'); ?>>8 hours</option>
					</optgroup>
					<optgroup label="Days">
						<option value="12"<?php $this->splashgate_selected_opt($splashgate_options['recurrance'], '12'); ?>>1/2 a day</option>
						<option value="24"<?php $this->splashgate_selected_opt($splashgate_options['recurrance'], '24'); ?>>1 day</option>
						<option value="48"<?php $this->splashgate_selected_opt($splashgate_options['recurrance'], '48'); ?>>2 days</option>
						<option value="72"<?php $this->splashgate_selected_opt($splashgate_options['recurrance'], '72'); ?>>3 days</option>
					</optgroup>
					<optgroup label="Weeks">
						<option value="84"<?php $this->splashgate_selected_opt($splashgate_options['recurrance'], '84'); ?>>1/2 week</option>
						<option value="168"<?php $this->splashgate_selected_opt($splashgate_options['recurrance'], '168'); ?>>1 week</option>
						<option value="336"<?php $this->splashgate_selected_opt($splashgate_options['recurrance'], '336'); ?>>2 weeks</option>
					</optgroup>
					<optgroup label="Months">
						<option value="672"<?php $this->splashgate_selected_opt($splashgate_options['recurrance'], '672'); ?>>1 month</option>
						<option value="1344"<?php $this->splashgate_selected_opt($splashgate_options['recurrance'], '1344'); ?>>2 months</option>
						<option value="2016"<?php $this->splashgate_selected_opt($splashgate_options['recurrance'], '2016'); ?>>3 months</option>
					</optgroup>
					<optgroup label="Testing">
						<option value="-1"<?php $this->splashgate_selected_opt($splashgate_options['recurrance'], '-1'); ?>>Every page load</option>
					</optgroup>
				</select></p>
				<p><em>After a visitor sees the splash page, it gets repressed for a length of time.  Set that repression duration here.</em></p>
			<?php
		}
		
		
		// SPLASH STYLE OPTS
		function on_sidebox_3_content($splashgate_options) { 
			?>
				<p><select id="splashgate_options_style" name="splashgate_options[style]">
					<option value="1"<?php $this->splashgate_selected_opt($splashgate_options['style'], '1'); ?>>Page Redirect</option>
					<option value="2"<?php $this->splashgate_selected_opt($splashgate_options['style'], '2'); ?>>Pop Over (fancybox)</option>
				</select></p>

				<div id="splashgate_options_fancybox" style="display:<?php echo ($splashgate_options['style'] == '2') ? 'block' : 'none'; ?>">
					<?php
						$w = isset($splashgate_options['window_width']) && !empty($splashgate_options['window_width']) ? $splashgate_options['window_width'] : 414;
						$h = isset($splashgate_options['window_height']) && !empty($splashgate_options['window_height']) ? $splashgate_options['window_height'] : 360;
					?>
					<p><label>Fancybox width <input type="number" name="splashgate_options[window_width]" value="<?php echo $w; ?>"></label></p>
					<p><label>Fancybox height <input type="number" name="splashgate_options[window_height]" value="<?php echo $h; ?>"></label></p>
				</div>

				<p><em>By default, user is redirected to full splash page.  Optionally select 'Pop Over' to engage a window over the front page instead.</em></p>
				
				<script>
					(function($){
						$('#splashgate_options_style').live('change', function(e){
							e.preventDefault();
							var $fancybox_options = $('#splashgate_options_fancybox');
							var bool = $(this).val() == '2';
							$fancybox_options.toggle( bool );
						});
					})(jQuery);
				</script>
			<?php
		}
		
		function splashgate_selected_opt($a, $b){
			if($a == $b){
				echo "selected=\"selected\"";
			}	
			return;
		}
		/*** END SPLASH MANAGER **/
		
		
		
		/**  SPLASHABLE *** 
		  *  This handles sidebar meta-box on 'Page' type admin pages 
		  */
		function on_splashgate_pages_admin(){
			global $post; ?>
	    <input type="hidden" name="splashgate_splashable_noncename" id="splashgate_splashable_noncename" value="<?php echo wp_create_nonce( plugin_basename(__FILE__) ); ?>" /><?php
	 
			$checked = "";
	    $splashgate_splashable = get_post_meta($post->ID, '_splashgate_splashable', true);
	 		if($splashgate_splashable){
	 			$checked = "checked=\"checked\"";
	 		} ?>
	 		
	 		<p><input type="checkbox" name="splashgate_splashable" <?php echo $checked; ?>/>
		  <label for="splashgate_splashable"><?php echo __( 'Splashable', 'splashgate_textdomain' ); ?></label></p>
  
			<p>This <strong>does not</strong> set the Splash Page.  This only <em>allows</em> this page to be set as the Splash Page. </p>
			<p><a href="<?php echo admin_url('/options-general.php?page=splashgate_options',__FILE__); ?>" title="Manage splash page">Manage splash page</a></p><?php
		
		} // end on_splashgate_pages_admin()
		
		function splashgate_splashable_save(){
			global $post;
			// verify this came from the our screen and with proper authorization,
	    // because save_post can be triggered at other times
	    if ( !wp_verify_nonce( $_POST['splashgate_splashable_noncename'], plugin_basename(__FILE__) )) {
	    	return $post->ID;
	    }
	 
	    // Is the user allowed to edit
	    if ( !current_user_can( 'edit_post', $post->ID )){
	    	return $post->ID;
	 		}
	    
	    // Set all options here
	    $splashgate_meta = array();
	    $splashgate_meta['_splashgate_splashable'] = filter_input(INPUT_POST, 'splashgate_splashable', FILTER_SANITIZE_STRING);
	 		
	    
	    foreach ($splashgate_meta as $key => $value) { // Cycle through the $events_meta array!
	        if( $post->post_type == 'revision' ) return; // Don't store custom data twice
	        $value = implode(',', (array)$value); // If $value is an array, make it a CSV (unlikely)
	        if(get_post_meta($post->ID, $key, FALSE)) { // If the custom field already has a value
	            update_post_meta($post->ID, $key, $value);
	        } else { // If the custom field doesn't have a value
	            add_post_meta($post->ID, $key, $value);
	        }
	        if(!$value) delete_post_meta($post->ID, $key); // Delete if blank
	    }
    
		} // end splashgate_splashable_save()
		
		
		/*** END SPLASHABLE PAGES ***/
		
			
	} // end class
} // end if class !exist

if (class_exists("SplashGate")) {
	$splashgate = new SplashGate();
}