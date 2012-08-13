<script type="text/javascript">
	function splashgate_get_qs_param(name) {
		return decodeURIComponent((new RegExp('[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)').exec(location.search) || [,""])[1].replace(/\+/g, '%20')) || null;
	}
	
	<?php print $splashgate_fancybox_js; ?>
	
	(function($) {
		$(document).ready(function() {
			if (typeof SplashGate != 'undefined') {
				$('#splashgate-modal').fancybox(SplashGate.fancybox);
			}

			function splashCookie(){
				var splash_path = "<?php bloginfo('url'); ?>/?pagename=splashgate",
				    splash_hours = <?php print $duration; ?>,
				    key = 'splashgate',
				    value = 1,
				    expires = new Date(),
				    keyValue = document.cookie.match('(^|;) ?' + key + '=([^;]*)(;|$)'),
				    splasharg = splashgate_get_qs_param('splash');
				
				if (document.referrer.indexOf("<?php bloginfo('url'); ?>") != 0) {
					if (!keyValue && splasharg !== 'no') {
						expires.setTime(expires.getTime() + splash_hours * 3600000);
						document.cookie = key + '=' + value + ';expires=' + expires.toUTCString();
						<?php print $do_splash_js; ?>
					} else if (splasharg == 'yes') {
						<?php print $do_splash_js; ?>
					}
				}
			}
			splashCookie();
		});
	})(jQuery);
</script>