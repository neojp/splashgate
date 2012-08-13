<p>
	<select name="splashgate_options[recurrance]">
		<optgroup label="Hours">
			<option value="1"   <?php SplashGate::selected_opt($options['recurrance'], '1'); ?>>1 hour</option>
			<option value="2"   <?php SplashGate::selected_opt($options['recurrance'], '2'); ?>>2 hours</option>
			<option value="3"   <?php SplashGate::selected_opt($options['recurrance'], '3'); ?>>3 hours</option>
			<option value="4"   <?php SplashGate::selected_opt($options['recurrance'], '4'); ?>>4 hours</option>
			<option value="8"   <?php SplashGate::selected_opt($options['recurrance'], '8'); ?>>8 hours</option>
		</optgroup>
		<optgroup label="Days">
			<option value="12"  <?php SplashGate::selected_opt($options['recurrance'], '12'); ?>>1/2 a day</option>
			<option value="24"  <?php SplashGate::selected_opt($options['recurrance'], '24'); ?>>1 day</option>
			<option value="48"  <?php SplashGate::selected_opt($options['recurrance'], '48'); ?>>2 days</option>
			<option value="72"  <?php SplashGate::selected_opt($options['recurrance'], '72'); ?>>3 days</option>
		</optgroup>
		<optgroup label="Weeks">
			<option value="84"  <?php SplashGate::selected_opt($options['recurrance'], '84'); ?>>1/2 week</option>
			<option value="168" <?php SplashGate::selected_opt($options['recurrance'], '168'); ?>>1 week</option>
			<option value="336" <?php SplashGate::selected_opt($options['recurrance'], '336'); ?>>2 weeks</option>
		</optgroup>
		<optgroup label="Months">
			<option value="672" <?php SplashGate::selected_opt($options['recurrance'], '672'); ?>>1 month</option>
			<option value="1344"<?php SplashGate::selected_opt($options['recurrance'], '1344'); ?>>2 months</option>
			<option value="2016"<?php SplashGate::selected_opt($options['recurrance'], '2016'); ?>>3 months</option>
		</optgroup>
		<optgroup label="Testing">
			<option value="-1"  <?php SplashGate::selected_opt($options['recurrance'], '-1'); ?>>Every page load</option>
		</optgroup>
	</select>
</p>

<p><em>After a visitor sees the splash page, it gets repressed for a length of time.  Set that repression duration here.</em></p>