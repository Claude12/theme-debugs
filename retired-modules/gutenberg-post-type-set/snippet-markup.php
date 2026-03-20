<?php 

  $pageId = get_the_ID();
  $postType = get_field('km_post_type_select');
  $step = get_field('step');
  $maxPages =  $step === '-1' ? 1 : ceil(wp_count_posts( $postType )->publish / $step);
  $persistProgress = get_field('persist_item_progress') ? 'true' : 'false';
  $orderDirection = get_field('order_direction') === true ? 'DESC' : 'ASC';
  $orderBy = get_field('order_by') ?: 'date';
  $hideLoadMore = get_field('hide_load_more');
  $displayCategories = get_field('display_categories'); // '', filter, links

  // data attributes for load more button
  $loadMoreAttributes = [
    'total-pages' => esc_attr($maxPages),
    'page-id' => get_the_ID(),
    'persist-progress' => $persistProgress,
    'post-type' => $postType,
    'step' => $step,
    'order-by' => $orderBy,
    'order-direction' => $orderDirection,
    'target-url' => get_admin_url() . 'admin-ajax.php',
    'single-markup' => $postType, // this should match templates-> card markup. defaults to post
    'current' => $pageId // used to exlude relevant post from Query
  ];

  // Extra content
  $title = get_field('title');
  $titleClasses = createClasses(['km-pi-title'], 'title_colour');
  $separatorClasses = createClasses(['km-pi-separator-line'], 'separator_line_colour', 'background-colour');
  $ctas = get_field('buttons') ? get_field('buttons')['links'] ?: [] : [];

?>
  <section class="km-post-type-feed km-post-type-block <?php if($postType) echo 'km-' . $postType . '-feed'; ?>">

  <?php // LINE  ?>
  <?php if(get_field('display_line')) : ?>
    <span class="<?php echo $separatorClasses; ?>">&nbsp;</span>
  <?php endif; ?>

  <?php // TITLE ?>
  <?php if($title) : ?>
    <h2 class="<?php echo $titleClasses; ?>"><?php echo $title; ?></h2>
  <?php endif; ?>

  <?php if($displayCategories === 'filter') : ?>
    <?php hm_get_template_part( get_template_directory() . '/template-parts/gutenberg-post-type-set/category-sorter.php', [
      'post_type' =>  $postType
      ] );
    ?>   
  <?php endif; ?>

  <?php if($displayCategories === 'links') : ?>
    <?php hm_get_template_part( get_template_directory() . '/template-parts/gutenberg-post-type-set/category-links.php', [
      'post_type' =>  $postType
      ] );
    ?>   
  <?php endif; ?>

  <div class="km-ptf-wrap km-filter<?php if($displayCategories !== 'filter') echo '-not'; ?>-in-use"><!-- Data injected via AJAX --></div>
  
  <button class="km-items-load-more 
  <?php if($hideLoadMore) echo 'km-items-load-hidden'; ?>" 
  <?php foreach ($loadMoreAttributes as $key => &$attr) echo 'data-' . $key .'="'. $attr.'"'; unset($attr) ?>>Load More</button>


  <?php // CTAS ?>
  <?php if(count($ctas) > 0) : ?>
    <?php hm_get_template_part( get_template_directory() . '/template-parts/common/buttons/snippet-markup.php', [
        'links' => $ctas ?: [],
        'classPrefix' => 'post-type', // optional
        'extraButtonClass' => 'cta-hover-x' // optional
      ] );
    ?>   
  <?php endif; ?>


</section>

