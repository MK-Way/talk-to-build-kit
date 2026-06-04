<?php
/**
 * Front page template.
 *
 * If the static front page has the Canvas template assigned, route through canvas.php
 * so the front page renders raw HTML. Otherwise fall through to page.php (standard).
 *
 * Without this file, WordPress's template hierarchy bypasses _wp_page_template for the
 * front page and renders page.php directly, ignoring the Canvas assignment.
 *
 * @package Talk-to-Build
 */

$front_id = (int) get_option( 'page_on_front' );
if ( $front_id ) {
    $template = get_post_meta( $front_id, '_wp_page_template', true );
    if ( $template === 'canvas.php' ) {
        require get_template_directory() . '/canvas.php';
        return;
    }
}

require get_template_directory() . '/page.php';
