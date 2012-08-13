<?php
	$width = isset($options['window_width'])  && !empty($options['window_width'])  ? $options['window_width']  : 414;
	$height = isset($options['window_height']) && !empty($options['window_height']) ? $options['window_height'] : 360;
?>

<p>
	<select id="splashgate_options_style" name="splashgate_options[style]">
		<option value="1"<?php SplashGate::selected_opt($options['style'], '1'); ?>>Page Redirect</option>
		<option value="2"<?php SplashGate::selected_opt($options['style'], '2'); ?>>Pop Over (fancybox)</option>
	</select>
</p>

<div id="splashgate_options_fancybox" style="display: <?php echo ($options['style'] == '2') ? 'block' : 'none'; ?>">
	<p><label>Fancybox width <input type="number" name="splashgate_options[window_width]" value="<?php echo $width; ?>"></label></p>
	<p><label>Fancybox height <input type="number" name="splashgate_options[window_height]" value="<?php echo $height; ?>"></label></p>
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