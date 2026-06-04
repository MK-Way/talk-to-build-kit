<?php
/**
 * Template Name: Canvas (Blank)
 * Template Post Type: page
 *
 * WP-compliant blank template for Talk-to-Build landing pages.
 * No theme chrome — no header, no nav, no footer widget areas.
 * WordPress hooks are fully preserved so all plugins work correctly:
 *   - wp_head()   → Yoast SEO meta tags, analytics scripts, security headers
 *   - wp_body_open() → Admin bar, tracking pixels, Google Tag Manager noscript
 *   - wp_footer() → Google Analytics, chat plugins, deferred scripts
 *
 * Content stored in WordPress must be body HTML only.
 * The t2b pipeline extracts <body> content and moves <head> styles into
 * <style> blocks before database insertion — never a full HTML document.
 *
 * @package Talk-to-Build
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php wp_head(); ?>
</head>
<body <?php body_class( 'canvas-page' ); ?>>
<?php wp_body_open(); ?>

<?php
if ( have_posts() ) {
	the_post();
	global $wpdb;
	// Raw DB read — bypasses wpautop and content filters that corrupt HTML/CSS
	// from the Figma/AI pipeline. Content is trusted: inserted by the t2b pipeline.
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery
	echo $wpdb->get_var(
		$wpdb->prepare( "SELECT post_content FROM {$wpdb->posts} WHERE ID = %d", get_the_ID() )
	);
}
?>

<?php wp_footer(); ?>
</body>
</html>
