<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e( 'Skip to content', 'talk-to-build' ); ?></a>

<div class="site">
  <header class="site-header">
    <div class="container">
      <?php if ( has_custom_logo() ) : ?>
        <?php the_custom_logo(); ?>
      <?php else : ?>
        <h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
      <?php endif; ?>

      <?php if ( has_nav_menu( 'primary' ) ) : ?>
        <nav class="main-navigation" aria-label="<?php esc_attr_e( 'Primary menu', 'talk-to-build' ); ?>">
          <?php wp_nav_menu( array( 'theme_location' => 'primary', 'depth' => 2, 'fallback_cb' => false ) ); ?>
        </nav>
      <?php endif; ?>
    </div>
  </header>

  <main id="main" class="site-content">
