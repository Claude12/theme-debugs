<?php

defined('ABSPATH') || exit;

add_settings_section(  
	'page_1_section',         // ID used to identify this section and with which to register options  
	'',   // Title to be displayed on the administration page  
	'afpvu_page_3_section_callback', // Callback used to render the description of the section  
	'addify-products-visibility-3'                           // Page on which to add this section of options  
);


add_settings_field (   
	'afpvu_allow_seo',                      // ID used to identify the field throughout the theme  
	esc_html__('Allow Search Engines to Index', 'addify_b2b'),    // The label to the left of the option interface element  
	'afpvu_allow_seo_callback',   // The name of the function responsible for rendering the option interface  
	'addify-products-visibility-3',                          // The page on which this option will be displayed  
	'page_1_section',         // The name of the section to which this field belongs  
	array(                              // The array of arguments to pass to the callback. In this case, just a description.  
		esc_html__('Allow search engines to crawl and index hidden products, categories and other pages. While using global option when you hide products from guest users they will stay hidden for search engines as well i.e. Google won’t be able to rank those pages in search results. Please check this box if you want Google to crawl and rank hidden pages.', 'addify_b2b'),
	)  
);  
register_setting(  
	'setting-group-3',  
	'afpvu_allow_seo'  
);

function afpvu_page_3_section_callback() { 
	?>
	<h2><?php echo esc_html__('General Settings', 'addify_b2b'); ?></h2>
	

	<?php 
} // function afreg_page_1_section_callback


function afpvu_allow_seo_callback( $args ) {  
	?>
	<input type="checkbox" id="afpvu_allow_seo" name="afpvu_allow_seo" value="yes" <?php checked('yes', esc_attr( get_option('afpvu_allow_seo'))); ?> >
	<p class="description afpvu_allow_seo"> <?php echo esc_attr($args[0]); ?> </p>
	<?php      
} // end afpvu_allow_seo_callback 
