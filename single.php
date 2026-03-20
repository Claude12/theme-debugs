
<?php 
	get_header();
	if ( function_exists('yoast_breadcrumb') ) {
    yoast_breadcrumb( '<div class="yoast-breadcrumbs">','</div>' );
  }

	$pt = get_post_type();
	$labels = get_post_type_object(get_post_type())->labels;
?>

  <div class="km-post-type-item<?php if($pt === 'post' && is_active_sidebar($pt)) echo ' km-has-sidebar'; ?>">
    
		<article class="km-post-type-content km-wysiwyg">
			<?php // Image ?>
			<?php if(get_the_post_thumbnail_url()) : ?>
				<img class="km-pt-image" src="<?php the_post_thumbnail_url(); ?>" alt="<?php the_title(); ?>" />  
			<?php endif; ?>

			<?php // CATEGORIES ?>
			<?php /* if(!empty(get_the_category( get_the_ID() ))) : ?>
				<ul class="km-pt-categories">
					<?php foreach (get_the_category( get_the_ID() ) as $item) : ?>
						<?php
							$catLink = get_category_link($item);
							$catTitle = $item->name;
						?>
						<li>
							<a href="<?php echo $catLink; ?>">
								<span><?php echo $catTitle; ?></span>
							</a>
						</li>
					<?php endforeach;?>  
				</ul>
			<?php endif; */?>


			<?php // Title ?>
			<h2 class="km-ni-title"><?php the_title(); ?></h2>

			<?php // Content ?>
			<?php if(get_the_content()) : ?>
				<div class="km-ni-content km-wysiwyg"><?php the_content(); ?></div>
			<?php endif; ?>
		
		</article>

		<?php if($pt === 'post' && is_active_sidebar($pt)) echo get_template_part('sidebar'); ?>

  </div>

	<div class="km-pt-nav">
		<?php previous_post_link('%link', '<span class="km-pt-nav-item km-pt-prev" title="%title"><svg><use href="#single-post-prev" /></svg></span>'); ?>
		<a class="km-pt-all" href="<?php echo get_post_type_archive_link(get_post_type()); ?>"><span>View all <?php echo $labels->name; ?></span></a>
		<?php next_post_link('%link', '<span class="km-pt-nav-item km-pt-next" title="%title"><svg><use href="#single-post-next" /></svg></span>'); ?>
	</div>

</div>

<?php get_footer();