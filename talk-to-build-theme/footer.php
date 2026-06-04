  </main>

  <footer class="site-footer">
    <div class="container">
      <?php if ( has_nav_menu( 'footer' ) ) : ?>
        <nav class="footer-navigation">
          <?php wp_nav_menu( array( 'theme_location' => 'footer', 'depth' => 1 ) ); ?>
        </nav>
      <?php endif; ?>
      <p>&copy; <?php echo absint( gmdate( 'Y' ) ); ?> <?php echo esc_html( get_bloginfo( 'name' ) ); ?>. All rights reserved.</p>
    </div>
  </footer>
</div>

<?php wp_footer(); ?>
</body>
</html>
