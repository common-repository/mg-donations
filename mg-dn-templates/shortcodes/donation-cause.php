<div class="mg-dn-shortcode-donation-cause">
	<?php if ($show_title): ?>
		<h2 class="mg-dn-title">
			<?php if ($show_permalink): ?>
				<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
			<?php else: ?>
				<?php the_title(); ?>
			<?php endif; ?>
		</h2>
	<?php endif; ?>
			
	<?php if (has_post_thumbnail() && $show_featured_image): ?>
		<?php if ($show_permalink): ?>
			<a href="<?php the_permalink(); ?>">
				<?php the_post_thumbnail('mg_dn_dc'); ?>
			</a>
		<?php else: ?>
			<?php the_post_thumbnail('mg_dn_dc'); ?>
		<?php endif; ?>
	<?php endif; ?>


	<?php if ($show_excerpt): ?>
		<div class="mg-dn-abstract">
			<?php the_excerpt(); ?>
		</div>
	<?php endif; ?>

	<?php
		echo mg_dn_donate_button($button_template, array('button_text' => $button_text));
	?>
</div> <!-- .mg-dn-shortcode-donation-cause -->