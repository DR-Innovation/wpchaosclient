<?php
/**
 * @package WP Chaos Client
 * @version 1.0
 */
get_header(); ?>

<div class="fluid-container body-container">
  <div class="dark-search no-show">
    <div class="search row"><?php dynamic_sidebar('Top'); ?></div>
  </div>
</div>

<div class="container">
  <div class="row">
    <article class="single-material" id="<?php echo WPChaosClient::get_object()->GUID ?>">
      <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1">
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
          <?php dynamic_sidebar('wpchaos-obj-featured'); ?>
        </div>
        <div>
          <?php dynamic_sidebar('wpchaos-obj-main'); ?>
        </div>
      </div>
    </article>
  </div>
</div>

<?php get_footer(); ?>
