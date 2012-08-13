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