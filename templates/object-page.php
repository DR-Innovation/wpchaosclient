<?php
/**
 * @package WP Chaos Client
 * @version 1.0
 */
get_header(); ?>

<article class="container single-material" id="<? echo WPChaosClient::get_object()->GUID ?>">
	<div>
		<div class="col-lg-9 col-12">
			<div>
				<?php dynamic_sidebar( 'wpchaos-obj-featured' ); ?>
			</div>
			<div>
				<?php dynamic_sidebar( 'wpchaos-obj-main' ); ?>
			</div>
		</div>
		<div class="col-lg-3 col-12">
			<?php if(is_active_sidebar('wpchaos-obj-sidebar')) : ?>
				<ul class="nav info">
					<?php dynamic_sidebar( 'wpchaos-obj-sidebar' ); ?>
				</ul>
			<?php endif;?>
		</div>
	</div>
</article>

<?php get_footer(); ?>