<?php
	$checked = "";
	$active_class = "splashgate-inactive";
	$active_msg = "Splash Page is OFF";
	$active_display_style = "block";

	/** TO BE COMPLETED - active_display_style
	  *
	  * This needs to check and only allow Activation
	  * checkbox to be shown if at least on page
	  * is marked 'splashable'.
	  */

	$active = $options['active'];
	if ($active) {
		$checked = 'checked="checked"';
		$active_class = "splashgate-active";
		$active_msg = "Splash Page is ON";
	}
?>

<p class="splashgate-status <?php echo $active_class;?>"><?php echo $active_msg; ?></p>
<div style="display:<?php echo $active_display_style; ?>">
	<p>
		<input type="checkbox" id="splashgate_options_active" name="splashgate_options[active]" <?php echo $checked; ?>/>
		<label for="splashgate_options_active">Active</label>
	</p>
	<p class="splashgate-active-desc">
		<?php if ($active) : ?>
			<span class="splashgate-active-title"><?php echo get_the_title($options['splashpage_id']); ?></span> is currently active.
		<?php endif; ?>
	</p>
</div>