<?php
/**
 * Talk-to-Build Theme Functions
 *
 * Minimal, future-proof. No external dependencies.
 * Built for AI-native build workflows + Figma Decode pipeline.
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Theme setup — runs after WordPress loads
 */
function t2b_setup() {
    // Let WordPress manage the <title> tag
    add_theme_support( 'title-tag' );

    // Featured images on posts/pages
    add_theme_support( 'post-thumbnails' );

    // RSS feed links in <head>
    add_theme_support( 'automatic-feed-links' );

    // Block editor support
    add_theme_support( 'wp-block-styles' );
    add_theme_support( 'align-wide' );
    add_theme_support( 'responsive-embeds' );

    // HTML5 markup
    add_theme_support( 'html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ) );

    // Custom logo (clients can upload their own via Customizer)
    add_theme_support( 'custom-logo', array(
        'height'      => 80,
        'width'       => 300,
        'flex-height' => true,
        'flex-width'  => true,
    ) );

    // Register navigation menu
    register_nav_menus( array(
        'primary' => esc_html__( 'Primary Menu', 'talk-to-build' ),
        'footer'  => esc_html__( 'Footer Menu', 'talk-to-build' ),
    ) );
}
add_action( 'after_setup_theme', 't2b_setup' );

/**
 * Enqueue styles — only on standard pages (NOT canvas)
 */
function t2b_scripts() {
    // Don't load theme CSS on canvas template pages
    if ( is_page_template( 'canvas.php' ) ) {
        return;
    }

    wp_enqueue_style(
        't2b-style',
        get_stylesheet_uri(),
        array(),
        wp_get_theme()->get( 'Version' )
    );
}
add_action( 'wp_enqueue_scripts', 't2b_scripts' );

/**
 * Remove unnecessary WordPress head clutter
 */
function t2b_cleanup_head() {
    remove_action( 'wp_head', 'wp_generator' );
    remove_action( 'wp_head', 'wlwmanifest_link' );
    remove_action( 'wp_head', 'rsd_link' );
    remove_action( 'wp_head', 'wp_shortlink_wp_head' );
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
}
add_action( 'init', 't2b_cleanup_head' );


/**
 * Widget area (optional — for standard pages)
 */
function t2b_widgets_init() {
    register_sidebar( array(
        'name'          => esc_html__( 'Footer Widgets', 'talk-to-build' ),
        'id'            => 'footer-widgets',
        'before_widget' => '<div class="footer-widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="widget-title">',
        'after_title'   => '</h4>',
    ) );
}
add_action( 'widgets_init', 't2b_widgets_init' );

/**
 * Allow full HTML in page content (for Canvas template).
 * WordPress strips <style> tags for non-admin users by default.
 * This preserves CSS pushed by the t2b pipeline via WP-CLI or the DB.
 */
function t2b_allow_style_tags( $allowedposttags, $context ) {
    if ( 'post' === $context ) {
        $allowedposttags['style'] = array(
            'type' => true,
            'id'   => true,
        );
    }
    return $allowedposttags;
}
add_filter( 'wp_kses_allowed_html', 't2b_allow_style_tags', 10, 2 );
