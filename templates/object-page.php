<?php
/**
 * @package WP Chaos Client
 * @version 1.0
 */
get_header(); ?>

<article class="row single-material" id="<?php echo WPChaosClient::get_object()->GUID ?>">
		<div class="col-lg-9 col-12">
			<?php if (current_user_can(WPDKA::PUBLISH_STATE_CAPABILITY)): ?>
				<div class="publishinfo">
					<?php if (!WPChaosClient::get_object()->isPublished): ?>
						<div class="alert alert-danger">
							<?php _e('This object is <strong>not</strong> visible for any other viewers.', 'wpchaosclient'); ?>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
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
</article>

<?php get_footer(); ?>