<?php
/*
  Template Name: Search Page
*/
?>
<?php get_header(); ?>

<div class="search-results-header">&nbsp;</div>

<div class="main-content-wrap">
  <div class="main-content" id="main-content">

   <article class="search-results">
      <h3 class="sr-title">Search results for '<?php echo get_search_query(); ?>' </h3>

      <div class="sr-list">

        <?php while ( have_posts() ) : the_post(); ?>

          <a class="sr-item" href="<?php echo the_permalink(); ?>">
            <h4 class="sr-item-title"><?php echo the_title(); ?></h4>
            <p class="sr-item-content"><?php the_excerpt(); ?></p>
            <span class="sr-item-more">Read more <svg><use href="#search-more-arrow"/></svg></span>
          </a>

        <?php endwhile; ?>

      </div>

      <?php if(!search_has_results()) : ?>
        <div class="search-no-results-message">
          <h4 class="sr-item-title">Sorry, but nothing matched your search criteria. Please try again with some different keywords.</h4>
        </div>
      <?php endif; ?>

      <?php the_posts_pagination( array(
          'screen_reader_text' => 'Search Navigation',
          'aria-label' => 'Search Navigation',
          'mid_size'  => 2,
          'prev_text' => 'Previous',
          'next_text' => 'Next',
      ) ); ?>
    </article>

  </div>
</div>

<?php get_footer();