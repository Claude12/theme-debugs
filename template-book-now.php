 <?php
/*
Template Name: Book Now
*/
?>

 <?php get_header(); 

  // Ketchup Modules
	while (have_posts()) {
		the_post();
		get_template_part('parse','blocks',[
			'data' => get_the_content(),
			'render-html' => true
		]);
	}
?>

 <div class="main-content-wrap">
   <div class="main-content" id="main-content">
     <section class="content index-content wedding-planner" id="wedding-planner">
       <div class="global-container clearfix" style="min-height: 1000px;">

         <div class="booking-form-mobile responsive-only">
           <h2>Contact us to discuss your Wedding Day</h2>
           <p>Email: <a
               href="mailto:<?php the_field('contact_email', 15); ?>"><?php the_field('contact_email', 15); ?></a></p>
           <p>Call: <?php the_field('contact_telephone', 15); ?></p>
         </div>

         <div class="booking-form">
           <?php gravity_form(1, false, false, false, '', false); ?> <?php // originally form 4 ?>
         </div>

         <div class="visuallyhidden">
          <!-- <div class=""> -->

           <!-- Calendar Limit -->

           <span class="calandar_limit">
             <?php $date = DateTime::createFromFormat('Ymd', get_field('calandar_limit', 'options')); ?>
             <span class="year"><?php echo $date->format('Y'); ?></span>
             <span class="month"><?php echo $date->format('m'); ?></span>
             <span class="day"><?php echo $date->format('d'); ?></span>
           </span>

           <!-- Plan -->			 
           <h3 class="title plan-title"><?php the_field('planner_plan_title', 'options'); ?></h3>
           <p class="direction plan-direction"><?php the_field('planner_plan_direction', 'options'); ?></p>
           <?php if( have_rows('planner_plan_repeater', 'options') ): ?>
           <?php $i = 1; ?>
           <?php while ( have_rows('planner_plan_repeater', 'options') ) : the_row(); ?>
           <p class="plan-<?php echo $i; ?>-item"><?php the_sub_field('planner_plan_item'); ?></p>
           <?php $i++; ?>
           <?php endwhile; ?>
           <?php endif; ?>
           <?php if( have_rows('planner_price_unavail', 'options') ): ?>
           <?php while ( have_rows('planner_price_unavail', 'options') ) : the_row(); ?>
           <p class="unavailable-date"><?php the_sub_field('planner_price_unavail_date'); ?></p>
           <?php endwhile; ?>
           <?php endif; ?>

           <!-- Daytime (number of guests) -->

           <h3 class="title daytime-title"><?php the_field('planner_daytime_title', 'options'); ?></h3>
           <p class="direction daytime-direction"><?php the_field('planner_daytime_direction', 'options'); ?></p>
           <label class="label daytime-label"><?php the_field('planner_daytime_label', 'options'); ?></label>

           <!-- Ceremony type  -->
           <h3 class="title ceremony-type-title"><?php the_field('planner_ceremony_headline', 'options'); ?></h3>
           
           <!-- Dressing Rooms -->
           <h3 class="title dressingrooms-title"><?php the_field('dressing_rooms_section_headline', 'options'); ?></h3>
           <?php if( have_rows('dressing_rooms_options', 'options') ): ?>
           <?php $i = 1; ?>
           <?php while ( have_rows('dressing_rooms_options', 'options') ) : the_row(); ?>
           <h4 class="name dressing-<?php echo $i; ?>-name"><?php the_sub_field('title'); ?></h4>
            <div class="price dressing-<?php echo $i; ?>-price">
              <p class="dressing-<?php echo $i; ?>-price-placeholder"></p>
              <p class="dressing-price-2025">&pound;<?php echo number_format(get_sub_field('price_2025'), 2); ?></p>
              <p class="dressing-price-2026">&pound;<?php echo number_format(get_sub_field('price_2026'), 2); ?></p>
              <p class="dressing-price-2027">&pound;<?php echo number_format(get_sub_field('price_2027'), 2); ?></p>
            </div>
           
           <span class="dressing-<?php echo $i; ?>-description"><?php the_sub_field('description'); ?></span>
           <?php $i++; ?>
           <?php endwhile; ?>
           <?php endif; ?>

           <!-- PRE WEDDING BREAKFAST -->
           <!-- <h3 class="title prewedding-breakfast-title">PRE WEDDING BREAKFAST</h3> -->

           <!-- Canapés -->

           <h3 class="title canapes-title"><?php the_field('planner_canapes_title', 'options'); ?></h3>
           <p class="direction canapes-direction"><?php the_field('planner_canapes_direction', 'options'); ?></p>

           <?php if( have_rows('planner_canapes_repeater', 'options') ): ?>
           <?php $i = 1; ?>
           <?php while ( have_rows('planner_canapes_repeater', 'options') ) : the_row(); ?>
           <h4 class="name canapes-<?php echo $i; ?>-name"><?php the_sub_field('planner_canapes_name'); ?></h4>
           <p class="price canapes-<?php echo $i; ?>-price">
             &pound;<?php echo number_format(get_sub_field('planner_canapes_price'), 2); ?> per head</p>
           <p class="fancy canapes-<?php echo $i; ?>-fancy">
             <a href="#canapes-<?php echo $i; ?>-description" class="fancybox" data-fancybox-group="canapes">View
               Menu</a>
           </p>
           <?php $i++; ?>
           <?php endwhile; ?>
           <?php endif; ?>

           <!-- Breakfast -->
           <h2 class="title breakfast-packages-title">Wedding Breakfast Packages</h2>
           <h3 class="title breakfast-title"><?php the_field('planner_breakfast_title', 'options'); ?></h3>
           <p class="direction breakfast-direction"><?php the_field('planner_breakfast_direction', 'options'); ?></p>

            <a href="#breakfast-1-menu" class="fancybox breakfast-menu-link-1" data-fancybox-group="breakfast" style="font-weight: 600;text-decoration: underline;">
              View Menu
            </a>
            <a href="#breakfast-2-menu" class="fancybox breakfast-menu-link-2" data-fancybox-group="breakfast" style="font-weight: 600;text-decoration: underline;">
              View Menu
            </a>
            <a href="#breakfast-3-menu" class="fancybox breakfast-menu-link-3" data-fancybox-group="breakfast" style="font-weight: 600;text-decoration: underline;">
              View Menu
            </a>
            <a href="#breakfast-4-menu" class="fancybox breakfast-menu-link-4" data-fancybox-group="breakfast" style="font-weight: 600;text-decoration: underline;">
              View Menu
            </a>

           <?php if( have_rows('planner_breakfast_menu', 'options') ): ?>
           <?php $i = 1; ?>
           <div class="information breakfast-information">
             <?php the_field('planner_breakfast_menu_direction', 'options'); ?>
             <hr>
             <ul>
               <?php while( have_rows('planner_breakfast_menu', 'options') ): the_row(); ?>
               <li><a href="#breakfast-<?php echo $i; ?>-menu" class="fancybox"
                   data-fancybox-group="breakfast"><?php the_sub_field('title'); ?></a></li>
               <?php $i++; ?>
               <?php endwhile; ?>
             </ul>
           </div>
           <?php endif; ?>

           <?php if( have_rows('planner_breakfast_repeater', 'options') ): ?>
           <?php $i = 1; ?>
           <?php while ( have_rows('planner_breakfast_repeater', 'options') ) : the_row(); ?>
           <h4 class="name breakfast-<?php echo $i; ?>-name"><?php the_sub_field('planner_breakfast_name'); ?></h4>
           <p class="price breakfast-<?php echo $i; ?>-price">
             &pound;<?php echo number_format(get_sub_field('planner_breakfast_price'), 2); ?> per head</p>
           <span
             class="description breakfast-<?php echo $i; ?>-description"><?php the_sub_field('planner_breakfast_descrip'); ?></span>
           <?php $i++; ?>
           <?php endwhile; ?>
           <?php endif; ?>

           <!-- Crew food -->
            <h3 class="title crew-title">CREW FOOD</h3>
            <p class="direction crew-direction">Available for your suppliers on the day</p>


           <!-- Relaxed Dinner Options -->
            <h3 class="title relaxed-dining-title"><?php the_field('planner_relaxed_dining_headline', 'options'); ?></h3>

             <?php if( have_rows('planner_relaxed_dining_repeater', 'options') ): ?>
             <?php $i = 1; ?>
             <?php while ( have_rows('planner_relaxed_dining_repeater', 'options') ) : the_row(); ?>
             <h4 class="name relaxed-dining-<?php echo $i; ?>-name"><?php the_sub_field('planner_relaxed_dining_title'); ?></h4>
             <p class="price relaxed-dining-<?php echo $i; ?>-price">
               &pound;<?php echo number_format(get_sub_field('planner_relaxed_dining_price'), 2); ?></p>
             <p class="fancy relaxed-dining-<?php echo $i; ?>-fancy">
               <a href="#relaxed-dining-<?php echo $i; ?>-description" class="fancybox" data-fancybox-group="relaxed-dining">View Menu</a>
             </p>
             <?php $i++; ?>
             <?php endwhile; ?>
             <?php endif; ?>

            <!-- Asian catering -->
            <h3 class="title asian-catering-title">Asian Catering</h3>
            <p class="direction asian-catering-direction">Please note we only accept the external caterers detailed in our Asian Brochure. For Carribean, Nigerian or any other cultural cuisines, please get in touch with the Events Team</p>
            <h4 class="name asian-catering-name">Asian Catering</h4>
            <p class="price asian-catering-price">&pound;<span>66.00</span>
             service charge per head</p>

           <!-- Children's menu -->
           <a href="#breakfast-6-menu" class="fancybox children-2-course-popup-link" data-fancybox-group="breakfast" style="text-decoration: underline;">View Menu</a>
           <a href="#breakfast-6-menu" class="fancybox children-3-course-popup-link" data-fancybox-group="breakfast" style="text-decoration: underline;">View Menu</a>

           <!-- Drinks -->
           <h3 class="title drinks-title"><?php the_field('planner_drinks_title', 'options'); ?></h3>
           <p class="direction drinks-direction"><?php the_field('planner_drinks_direction', 'options'); ?></p>

           <?php if( have_rows('planner_drinks_repeater', 'options') ): ?>
           <?php $i = 1; ?>
           <?php while ( have_rows('planner_drinks_repeater', 'options') ) : the_row(); ?>
           <h4 class="name drinks-<?php echo $i; ?>-name"><?php the_sub_field('planner_drinks_name'); ?></h4>
           <p class="price drinks-<?php echo $i; ?>-price">
             &pound;<?php echo number_format(get_sub_field('planner_drinks_price'), 2); ?> per head</p>
           <span
             class="description drinks-<?php echo $i; ?>-description"><?php the_sub_field('planner_drinks_description'); ?></span>
           <?php $i++; ?>
           <?php endwhile; ?>
           <?php endif; ?>



           <!-- Non-alcoholic Drinks -->
           <h3 class="title non-alco-drinks-title"><?php the_field('planner_non_alco_drinks_title', 'options'); ?></h3>

           <?php if( have_rows('planner_non_alco_drinks_repeater', 'options') ): ?>
           <?php $i = 1; ?>
           <?php while ( have_rows('planner_non_alco_drinks_repeater', 'options') ) : the_row(); ?>
           <h4 class="name non-alco-drinks-<?php echo $i; ?>-name"><?php the_sub_field('planner_drinks_name'); ?></h4>
           <p class="price non-alco-drinks-<?php echo $i; ?>-price">
             &pound;<?php echo number_format(get_sub_field('planner_drinks_price'), 2); ?> per head</p>
           <span
             class="description non-alco-drinks-<?php echo $i; ?>-description"><?php the_sub_field('planner_drinks_description'); ?></span>
           <?php $i++; ?>
           <?php endwhile; ?>
           <?php endif; ?>


           <!-- Children soft drinks -->
           <h4 class="name chidren-soft-name">Children Soft Drinks</h4>
           <p class="price chidren-soft-price">&pound;<span>15.00</span></p>
           <span class="description chidren-soft-description">A selection of four soft drinks throughout your event</span>

           <!-- Extended Bar -->
           <h4 class="name bar-name"><?php the_field('planner_bar_name', 'options'); ?></h4>
           <p class="price bar-price">&pound;<?php echo number_format(get_field('planner_bar_price', 'options'), 2); ?>
             total</p>
           <span class="description bar-description"><?php the_field('planner_bar_description', 'options'); ?></span>

           <!-- Gin Bar -->
           <h4 class="name ginbar-name"><?php the_field('planner_ginbar_name', 'options'); ?></h4>
           <p class="price ginbar-price">&pound;<?php echo number_format(get_field('planner_ginbar_price', 'options'), 2); ?>
             total</p>
           <span class="description ginbar-description"><?php the_field('planner_ginbar_description', 'options'); ?></span>
          
          <!-- Gin Bar Selection -->
            <h3 class="title ginselection-title"><?php the_field('planner_ginselection_title', 'options'); ?></h3>
           <p class="direction ginselection-direction"><?php the_field('planner_ginselection_direction', 'options'); ?></p>

           <?php if( have_rows('planner_ginselection_repeater', 'options') ): ?>
           <?php $i = 1; ?>
           <?php while ( have_rows('planner_ginselection_repeater', 'options') ) : the_row(); ?>
           <h4 class="name ginselection-<?php echo $i; ?>-name"><?php the_sub_field('planner_ginselection_name'); ?></h4>
           <p class="price ginselection-<?php echo $i; ?>-price">
             &pound;<?php echo number_format(get_sub_field('planner_ginselection_price'), 2); ?></p>
           <span
             class="description ginselection-<?php echo $i; ?>-description"><?php the_sub_field('planner_ginselection_description'); ?></span>
           <?php $i++; ?>
           <?php endwhile; ?>
           <?php endif; ?>

          <!-- Champagne tower -->
          <h3 class="title champagnetower-title"><?php the_field('planner_champagnetower_headline', 'options'); ?></h3>
           

           <?php if( have_rows('planner_champagnetower_repeater', 'options') ): ?>
           <?php $i = 1; ?>
           <?php while ( have_rows('planner_champagnetower_repeater', 'options') ) : the_row(); ?>
           <h4 class="name champagnetower-<?php echo $i; ?>-name"><?php the_sub_field('planner_champagnetower_name'); ?></h4>
           <p class="price champagnetower-<?php echo $i; ?>-price">
             &pound;<?php echo number_format(get_sub_field('planner_champagnetower_price'), 2); ?></p>
           
           <?php $i++; ?>
           <?php endwhile; ?>
           <?php endif; ?>



           <!-- Afterhours -->
           <!-- Charcuterie -->
           <span class="description charcuterie-description">
            &pound;<?php echo number_format(get_field('planner_charcuterie_price', 'options'), 2); ?> per board 
            <hr style="margin: 10px auto;" />
            <?php the_field('planner_charcuterie_description', 'options'); ?></span>

            <!-- Cheeseboard -->
           <span class="description cheeseboard-description">
            &pound;<?php echo number_format(get_field('planner_cheeseboard_price', 'options'), 2); ?> per board 
            <hr style="margin: 10px auto;" />
            <?php the_field('planner_cheeseboard_description', 'options'); ?></span>

          
           
           <!-- Evening (number of guests) -->

           <h3 class="title evening-title"><?php the_field('planner_evening_title', 'options'); ?></h3>
           <p class="direction evening-direction"><?php the_field('planner_evening_direction', 'options'); ?></p>
           <label class="label evening-label"><?php the_field('planner_evening_label', 'options'); ?></label>
           <label class="label evening-label-2"><?php the_field('planner_evening_label_2', 'options'); ?></label>
           <p class="evening-label-3"><?php the_field('planner_evening_label_3', 'options'); ?></p>

           <!-- Dinner (Evening Meal) -->

           <h3 class="title dinner-title"><?php the_field('planner_dinner_title', 'options'); ?></h3>
           <p class="direction dinner-direction"><?php the_field('planner_dinner_direction', 'options'); ?></p>

           <?php if( have_rows('planner_dinner_repeater', 'options') ): ?>
           <?php $i = 1; ?>
           <?php while ( have_rows('planner_dinner_repeater', 'options') ) : the_row(); ?>
           <h4 class="name dinner-<?php echo $i; ?>-name"><?php the_sub_field('planner_dinner_name'); ?></h4>
           <p class="price dinner-<?php echo $i; ?>-price">
              &pound;<span class="price"><?php the_sub_field('planner_dinner_price') ?></span>
           </p>
           <p class="fancy dinner-<?php echo $i; ?>-fancy">
             <a href="#dinner-<?php echo $i; ?>-description" class="fancybox" data-fancybox-group="dinner">View Menu</a>
           </p>
           <?php $i++; ?>
           <?php endwhile; ?>
           <?php endif; ?>

           <!-- Dinner Simple (Evening Meal) -->

           <p class="direction dinner-s-direction"><?php the_field('planner_dinner_simple_direction', 'options'); ?></p>

           <?php if( have_rows('planner_dinner_s_repeater', 'options') ): ?>
           <?php $i = 1; ?>
           <?php while ( have_rows('planner_dinner_s_repeater', 'options') ) : the_row(); ?>
            <h4 class="name dinner-s-<?php echo $i; ?>-name"><?php the_sub_field('title'); ?></h4>
           <p class="description dinner-s-<?php echo $i; ?>-description">
             <?php the_sub_field('planner_dinner_s_description'); ?></p>
           <p class="price dinner-s-<?php echo $i; ?>-price">
             &pound;<?php echo number_format(get_sub_field('planner_dinner_s_price'), 2); ?> per head</p>
           <?php $i++; ?>
           <?php endwhile; ?>
           <?php endif; ?>

           <style>
           #field_1_24 {
             display: none;
           }
           </style>

           <!-- Something Extra -->
           <h3 class="title something-extra-title"><?php the_field('planner_something_extra_headline', 'options'); ?></h3>

           <?php if( have_rows('planner_something_extra_repeater', 'options') ): ?>
           <?php $i = 1; ?>
           <?php while ( have_rows('planner_something_extra_repeater', 'options') ) : the_row(); ?>
           <h4 class="name something-extra-<?php echo $i; ?>-name"><?php the_sub_field('planner_relaxed_dining_title'); ?></h4>
           <p class="description something-extra-<?php echo $i; ?>-description">
             <?php the_sub_field('description'); ?> 
            </p>
           <p class="price something-extra-<?php echo $i; ?>-price">
              <?php if ($i == 3): ?>
                <span class="from">from</span>
              <?php endif; ?>
              &pound;<span class="price"><?php the_sub_field('planner_relaxed_dining_price') ?></span>
           </p>
           <?php $i++; ?>
           <?php endwhile; ?>
           <?php endif; ?>


           <!-- Bedrooms -->

           <h3 class="title bedrooms-title"><?php the_field('planner_bedrooms_title', 'options'); ?></h3>
           <p class="direction bedrooms-direction"><?php the_field('planner_bedrooms_direction', 'options'); ?></p>
           <div class="information bedrooms-information">
             <?php the_field('planner_bedrooms_information', 'options'); ?>
           </div>

           <?php if( have_rows('planner_bedrooms_repeater', 'options') ): ?>
           <?php $i = 1; ?>
           <?php while ( have_rows('planner_bedrooms_repeater', 'options') ) : the_row(); ?>
           <h4 class="name bedrooms-<?php echo $i; ?>-name"><?php the_sub_field('planner_bedrooms_name'); ?></h4>
           <p class="price bedrooms-<?php echo $i; ?>-price">
             <?php if ($i == 7) { ?>
             &pound;<?php echo number_format(get_sub_field('planner_bedrooms_price'), 2); ?> each</p>
           <?php } else { ?>
           &pound;<?php echo number_format(get_sub_field('planner_bedrooms_price'), 2); ?></p>
           <?php } ?>
           <?php $i++; ?>
           <?php endwhile; ?>
           <?php endif; ?>

           <!-- Personal -->

           <h3 class="title personal-title"><?php the_field('planner_personal_title', 'options'); ?></h3>
           <p class="direction personal-direction"><?php the_field('planner_personal_direction', 'options'); ?></p>

         </div>

       </div>
     </section>
   </div>
 </div>

 <style>
    #field_1_56,
    #field_1_69 {
      display: flex;
      justify-content: center;
      align-items: center;
      flex-wrap: wrap;
      font-weight: 700;
    }

    #field_1_56 .ginput_container_number,
    #field_1_69 .ginput_container_number {
      margin-left: 20px;
    }

    @media (max-width: 500px) {
      #field_1_56 .ginput_container_number,
      #field_1_69 .ginput_container_number {
        margin: 20px 0 0 0;
        width: 100%;
      }

      #field_1_56 .ginput_container_number input,
      #field_1_69 .ginput_container_number input {
        width: 100%;
        text-align: center;
      }
    }

    .gform_page_fields .dressing-rooms .gfield_label {
      display: none;
    }

    .gform_page_fields .dressing-rooms {
      background: 0;
      color: #606060;
      padding: 10px 0 30px 0;
    }

    .gform_page_fields .dressing-rooms .gfield_radio {
      grid-template-columns: repeat(2, 1fr);
    }

    .gform_page_fields .dressing-rooms .gchoice {
      color: #fff;
    }

    .pre-wed-breakfast {
        width: calc(33% - 17px) !important;
        float: left;
        display: flex !important;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
    }

    .pre-wed-breakfast .ginput_container {
      margin: 20px 0;
    }

    #field_1_58,
    #field_1_59 {
      margin-right: 30px;
    }

    #field_1_61 {
      background: 0;
      text-align: center;
      color: #606060;
      padding: 0;
    }

    .crew-food {
      width: calc(50% - 15px) !important;
      float: left;
      display: flex !important;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      font-weight: 600;
    }

    .crew-food#field_1_62 {
      margin-right: 30px;
    }

    .crew-food label {
      text-transform: uppercase;
      margin-bottom: 20px;
    }

    .gform_page_fields .children-menu {
      background: 0;
      color: #606060;
      padding: 10px 0 30px 0;
    }

    .gform_page_fields .children-menu .gfield_label {
      display: none;
    }

    .gform_page_fields .children-menu .gfield_radio {
      grid-template-columns: repeat(2, 1fr);
    }

    .gform_page_fields .children-menu .gchoice {
      color: #fff;
    }

    #field_1_47.drinks .gfield_radio {
      grid-template-columns: repeat(4, 1fr);
    }

    .champagne-tower,
    .relaxed-dining {
        background: none !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    .champagne-tower .gfield_label,
    .relaxed-dining .gfield_label {
        display: none;
    }

    .champagne-tower .gfield_radio,
    .relaxed-dining .gfield_radio {
        grid-template-columns: repeat(3, 1fr);
    }

    .champagne-tower .gchoice,
    .relaxed-dining .gchoice {
        background: #7c1833;
        text-align: center;
    }

    .relaxed-dining-popup em {
      font-style: italic;
    }

    .gfield.asian-catering {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
    }

    .gfield.asian-catering label {
      display: none;
    }

    .gfield.asian-catering hr {
      margin: 10px auto !important;
    }

    .non-alcoholic-drinks,
    .simple-evening-food,
    .something-extra {
        background: none !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    .non-alcoholic-drinks .gfield_label,
    .simple-evening-food .gfield_label {
        display: none;
    }

    .non-alcoholic-drinks .gfield_radio,
    .simple-evening-food .gfield_radio,
    .something-extra .gfield_radio {
        grid-template-columns: repeat(3, 1fr);
    }

    .non-alcoholic-drinks .gchoice,
    .simple-evening-food .gchoice,
    .something-extra .gchoice {
        background: #7c1833;
        text-align: center;
    }

    .non-alcoholic-drinks .description p {
        font-size: 1.5rem;
        font-weight: 400;
    }

    .gfield.children-soft-drinks {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
    }

    .gfield.children-soft-drinks label,
    .gfield.something-extra label {
      display: none;
    }

    .gfield.children-soft-drinks hr {
      margin: 10px auto !important;
    } 

    .bedrooms-title {
      clear: both;
    }

    .dinner .gfield_radio {
      grid-template-columns: repeat(3,1fr);
    }

    .dinner-s-direction {
      color: #606060;
      margin-top: 0 !important;
      grid-column: span 3;
    }

    .simple-dinner-title {
      grid-column: span 3;
      margin-bottom: 0;
      margin-top: 0;
      color: #606060;
    }

    .simple-evening-food p.description,
    .something-extra p.description {
      font-weight: 400 !important;
    }

    .something-extra-title {
      color: #606060;
      margin-bottom: 20px;
    }

    .something-extra .price .from {
      margin-right: 8px;
      font-weight: 400;
    }

    .something-extra .gfield_checkbox {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 30px;
    }

    .something-extra .gfield_checkbox .gchoice {
      display: flex;
      box-sizing: border-box;
      padding: 20px;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      background: #7c1833;
      text-align: center;
    }

    .something-extra .gfield_checkbox .gchoice p {
      font-size: 1.8rem;
    }

    .something-extra .gfield_checkbox .gchoice p.price {
      font-weight: 700;
    }

    .ceremony-type-title {
      margin-bottom: 20px;
    }

    #field_1_51 {
      padding: 0;
      margin: 0;
    }

    .gform_page_fields .gfield.afterhours-cheeseboard {
      margin-bottom: 50px;
    }

    .gform_page_fields .gfield.bedrooms-main {
      padding-top: 0;
    }

    .wedding-planner .direction.bedrooms-direction {
      margin-bottom: 0;
    }

    .bedrooms-zbeds label,
    .double-badrooms-number label {
      display: inline-block;
      margin-bottom: 10px;
    }

    .bedrooms-zbeds select,
    .double-badrooms-number select,
    .extra-breakfast select {
      box-sizing: border-box;
      padding: 5px;
      width: 150px;
      height: 40px;
    }

    .double-badrooms-number,
    .extra-breakfast {
      text-align: center;
    } 

    .extra-breakfast .gfield_description {
      margin-bottom: 15px;
    }

    .dressing-rooms .gfield_checkbox {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 30px;
    }

    .dressing-rooms .gfield_checkbox .gchoice {
      display: flex;
      box-sizing: border-box;
      padding: 20px;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      background: #7c1833;
      text-align: center;
    }

    .dressing-rooms .gfield_checkbox .gchoice p {
      display: flex;
      align-items: center;
      text-align: center;
      justify-content: center;
      font-weight: 700;
      font-size: 1.8rem;
      height: auto !important;
    }

    @media (max-width: 760px) {
      .gform_page_fields .dressing-rooms .gfield_radio,
      .gform_page_fields .breakfast .gfield_radio, 
      .gform_page_fields .canapes .gfield_radio,
      .champagne-tower .gfield_radio, 
      .relaxed-dining .gfield_radio,
      .gform_page_fields .children-menu .gfield_radio,
      .drinks .gfield_radio,
      .non-alcoholic-drinks .gfield_radio, 
      .simple-evening-food .gfield_radio, 
      .something-extra .gfield_radio,
      #field_1_47.drinks .gfield_radio,
      .champagne-tower .gfield_radio, 
      .relaxed-dining .gfield_radio,
      .dinner .gfield_radio,
      .something-extra .gfield_checkbox,
      .bedrooms-main .gfield_checkbox, 
      #field_1_25 .dinner-s-wrap {
        grid-template-columns: repeat(1, 1fr);
      }

      .pre-wed-breakfast,
      .crew-food,
      .afterhours-charcutierie, 
      .afterhours-cheeseboard {
        width: 100% !important;
        float: none;
      } 

      #field_1_58, 
      #field_1_59,
      .crew-food#field_1_62,
      .afterhours-charcutierie {
        margin-right: 0;
      }
    }

 </style>

 <?php get_footer(); ?>

 <div id="km-wp-popups" class="km-wp-popups">
   <?php // canapes ?>
   <?php if( have_rows('planner_canapes_repeater', 'options') ): ?>
   <?php $i = 1; ?>
   <?php while ( have_rows('planner_canapes_repeater', 'options') ) : the_row(); ?>
   <div class="km-wp-popup" id="canapes-<?php echo $i; ?>-description">
     <button class="button-reset km-popup-close">
       <span class="km-icon km-icon-push-menu-cross">Close</span>
     </button>
     <h3><?php the_sub_field('planner_canapes_name'); ?></h3>
     <?php the_sub_field('planner_canapes_description'); ?>
   </div>
   <?php $i++; ?>
   <?php endwhile; ?>
   <?php endif; ?>

   <!-- relaxed dining options -->
   <?php // relaxed dining options ?>
   <?php if( have_rows('planner_relaxed_dining_repeater', 'options') ): ?>
   <?php $i = 1; ?>
   <?php while ( have_rows('planner_relaxed_dining_repeater', 'options') ) : the_row(); ?>
   <div class="km-wp-popup relaxed-dining-popup" id="relaxed-dining-<?php echo $i; ?>-description">
     <button class="button-reset km-popup-close">
       <span class="km-icon km-icon-push-menu-cross">Close</span>
     </button>
     <h3><?php the_sub_field('planner_relaxed_dining_title'); ?></h3>
     <?php the_sub_field('description'); ?>
   </div>
   <?php $i++; ?>
   <?php endwhile; ?>
   <?php endif; ?>


   <?php // Breakfast ?>
   <?php if( have_rows('planner_breakfast_menu', 'options') ): ?>
   <?php $i = 1; ?>
   <?php while( have_rows('planner_breakfast_menu', 'options') ): the_row(); ?>
   <div class="km-wp-popup" id="breakfast-<?php echo $i; ?>-menu">
     <button class="button-reset km-popup-close">
       <span class="km-icon km-icon-push-menu-cross">Close</span>
     </button>
     <h3><?php the_sub_field('title'); ?></h3>
     <?php the_sub_field('items'); ?>
   </div>
   <?php $i++; ?>
   <?php endwhile; ?>
   <?php endif; ?>


   <?php // Evening menu / Dinner ?>
   <?php if( have_rows('planner_dinner_repeater', 'options') ): ?>
   <?php $i = 1; ?>
   <?php while ( have_rows('planner_dinner_repeater', 'options') ) : the_row(); ?>
   <div class="km-wp-popup" id="dinner-<?php echo $i; ?>-description">
     <button class="button-reset km-popup-close">
       <span class="km-icon km-icon-push-menu-cross">Close</span>
     </button>
     <h3><?php the_sub_field('planner_dinner_name'); ?></h3>
     <?php the_sub_field('planner_dinner_description'); ?>
   </div>
   <?php $i++; ?>
   <?php endwhile; ?>
   <?php endif; ?>

 </div>