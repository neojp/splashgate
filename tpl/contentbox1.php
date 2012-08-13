<?php if (have_posts()) : ?>

	<p>Choose the page you'd like to splash from the list of splashable pages below:</p>

	<table cellpadding="0" cellspacing="0" class="widefat page splashable-list">
		<thead>
			<tr>
				<th scope="col" id="cb" class="manage-column column-cb check-column"></th>
				<th scope="col" id="title" class="manage-column column-title">Title</th>
				<th scope="col" id="author" class="manage-column column-author">Author</th>
				<th scope="col" id="date" class="manage-column column-date">Date</th>
			</tr>
		</thead>
		<?php
	
			$zebra = '';
			while (have_posts()) : the_post();
				$checked = ($post->ID == $splashgate_options['splashpage_id']) ? 'checked="checked"' : '';
				$zebra = ($zebra == ' alternate') ? '' : ' alternate';

				?>
				
				<tr class="<?php echo $zebra;?>">
					<th scope="row" class="check-column"><input type="radio" name="splashgate_options[splashpage_id]" value="<?php echo $post->ID;?>" <?php echo $checked; ?>/></th>
					<td><a href="<?php the_permalink();?>"><?php the_title(); ?></a></td>
					<td><?php the_author(); ?></td>
					<td><?php the_time('F jS, Y') ?></td>
				</tr>
				
			<?php endwhile; ?>
	</table>

	<p style="color:#777;">
		Pages have to be made 'splashable' in order to use them.
		To add a new page to this list, go <a href="<?php echo $admin_url;?>">edit the page</a> you'd like to use and check the 'Splashable' checkbox available in the right sidebar.
		Then come back here to finish the set up.
	</p>

<?php else : ?>

	<p style="display:block;color:#922;">There are no pages available for use.</p>
	<p style="color:#777;">
		Pages have to be made 'splashable' in order to use them.
		To make a page splashable, go <a href="<?php echo $admin_url; ?>">edit the page</a> you'd like to use and check the 'Splashable' checkbox available in the right sidebar.
		Then come back here to finish the set up.
	</p>

<?php endif; ?>