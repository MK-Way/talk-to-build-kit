<?php
/**
 * Standard page template — used for client-editable pages.
 * Includes theme header/footer. Content comes from WordPress editor.
 *
 * @package Talk-to-Build
 */

get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>
  <article id="page-<?php the_ID(); ?>" <?php post_class(); ?>>
    <h1 class="entry-title"><?php the_title(); ?></h1>
    <div class="entry-content">
      <?php the_content(); ?>
    </div>
  </article>
<?php endwhile; ?>

<?php get_footer(); ?>
