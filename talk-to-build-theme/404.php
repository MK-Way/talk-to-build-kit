<?php
/**
 * 404 template.
 *
 * @package Talk-to-Build
 */

get_header(); ?>

<article class="error-404">
  <h1 class="entry-title">Page Not Found</h1>
  <p>The page you're looking for doesn't exist. <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Return home</a>.</p>
</article>

<?php get_footer(); ?>
