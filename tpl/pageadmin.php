<?php
	$splashgate_splashable = get_post_meta($post->ID, '_splashgate_splashable', true);
	$checked = ($splashgate_splashable) ? 'checked' : '';
?>

<input type="hidden" name="splashgate_splashable_noncename" id="splashgate_splashable_noncename" value="<?php echo $nonce; ?>" />

<p>
	<input type="checkbox" id="splashgate_splashable" name="splashgate_splashable" <?php echo $checked; ?>/>
	<label for="splashgate_splashable"><?php echo __( 'Splashable', 'splashgate_textdomain' ); ?></label>
</p>

<p>This <strong>does not</strong> set the Splash Page.  This only <em>allows</em> this page to be set as the Splash Page.</p>
<p><a href="<?php echo admin_url('/options-general.php?page=splashgate_options',__FILE__); ?>" title="Manage splash page">Manage splash page</a></p>