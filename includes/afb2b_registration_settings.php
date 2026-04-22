<?php
//Tab 1
add_settings_section(  
	'page_1_section',         // ID used to identify this section and with which to register options  
	'',   // Title to be displayed on the administration page  
	'afreg_page_1_section_callback', // Callback used to render the description of the section  
	'addify-afreg-1'                           // Page on which to add this section of options  
);

add_settings_field(   
	'afreg_additional_fields_section_title',                      // ID used to identify the field throughout the theme  
	esc_html__('Additional Fields Section Title', 'addify_b2b'),    // The label to the left of the option interface element  
	'afreg_additional_fields_section_title_callback',   // The name of the function responsible for rendering the option interface  
	'addify-afreg-1',                          // The page on which this option will be displayed  
	'page_1_section',         // The name of the section to which this field belongs  
	array(                              // The array of arguments to pass to the callback. In this case, just a description.  
		esc_html__('This is the title for the section where additional fields are displayed on front end registration form.', 'addify_b2b'),
	)  
);  
register_setting(  
	'afreg_setting-group-1',  
	'afreg_additional_fields_section_title'  
);

add_settings_section(  
	'page_2_section',         // ID used to identify this section and with which to register options  
	'',   // Title to be displayed on the administration page  
	'afreg_page_2_section_callback', // Callback used to render the description of the section  
	'addify-afreg-1'                           // Page on which to add this section of options  
);

add_settings_field(   
	'afreg_site_key',                      // ID used to identify the field throughout the theme  
	esc_html__('Site Key', 'addify_b2b'),    // The label to the left of the option interface element  
	'afreg_site_key_callback',   // The name of the function responsible for rendering the option interface  
	'addify-afreg-1',                          // The page on which this option will be displayed  
	'page_2_section',         // The name of the section to which this field belongs  
	array(                              // The array of arguments to pass to the callback. In this case, just a description.  
		esc_html__('This is Google reCaptcha site key, you can get this from Google. Without this key Google reCaptcha will not work.', 'addify_b2b'),
	)  
);  
register_setting(  
	'afreg_setting-group-1',  
	'afreg_site_key'  
);

add_settings_field(   
	'afreg_secret_key',                      // ID used to identify the field throughout the theme  
	esc_html__('Secret Key', 'addify_b2b'),    // The label to the left of the option interface element  
	'afreg_secret_key_callback',   // The name of the function responsible for rendering the option interface  
	'addify-afreg-1',                          // The page on which this option will be displayed  
	'page_2_section',         // The name of the section to which this field belongs  
	array(                              // The array of arguments to pass to the callback. In this case, just a description.  
		esc_html__('This is Google reCaptcha secret key, you can get this from Google. Without this key Google reCaptcha will not work.', 'addify_b2b'),
	)  
);  
register_setting(  
	'afreg_setting-group-1',  
	'afreg_secret_key'  
);


//Tab 2
add_settings_section(  
	'page_1_section',         // ID used to identify this section and with which to register options  
	'',   // Title to be displayed on the administration page  
	'afreg_page_22_section_callback', // Callback used to render the description of the section  
	'addify-afreg-2'                           // Page on which to add this section of options  
);

add_settings_field(   
	'afreg_enable_user_role',                      // ID used to identify the field throughout the theme  
	esc_html__('Enable User Role Selection', 'addify_b2b'),    // The label to the left of the option interface element  
	'afreg_enable_user_role_callback',   // The name of the function responsible for rendering the option interface  
	'addify-afreg-2',                          // The page on which this option will be displayed  
	'page_1_section',         // The name of the section to which this field belongs  
	array(                              // The array of arguments to pass to the callback. In this case, just a description.  
		esc_html__('Enable/Disable User Role selection on registration page. If this is enable then a user role dropdown will be shown on registration page.', 'addify_b2b'),
	)  
);  
register_setting(  
	'afreg_setting-group-2',  
	'afreg_enable_user_role'  
);

add_settings_field(   
	'afreg_user_role_field_text',                      // ID used to identify the field throughout the theme  
	esc_html__('User Role Field Label', 'addify_b2b'),    // The label to the left of the option interface element  
	'afreg_user_role_field_text_callback',   // The name of the function responsible for rendering the option interface  
	'addify-afreg-2',                          // The page on which this option will be displayed  
	'page_1_section',         // The name of the section to which this field belongs  
	array(                              // The array of arguments to pass to the callback. In this case, just a description.  
		esc_html__('Field label for user role selection select box.', 'addify_b2b'),
	)  
);  
register_setting(  
	'afreg_setting-group-2',  
	'afreg_user_role_field_text'  
);



add_settings_field(   
	'afreg_allow_update_myaccount',                      // ID used to identify the field throughout the theme  
	esc_html__('Allow User to Edit Role in My Account', 'addify_b2b'),    // The label to the left of the option interface element  
	'afreg_allow_update_myaccount_callback',   // The name of the function responsible for rendering the option interface  
	'addify-afreg-2',                          // The page on which this option will be displayed  
	'page_1_section',         // The name of the section to which this field belongs  
	array(                              // The array of arguments to pass to the callback. In this case, just a description.  
		esc_html__('If this option is enabled then user can update user role from my account page.', 'addify_b2b'),
	)  
);  
register_setting(  
	'afreg_setting-group-2',  
	'afreg_allow_update_myaccount'  
);


add_settings_field(   
	'afreg_user_roles',                      // ID used to identify the field throughout the theme  
	esc_html__('Select User Roles', 'addify_b2b'),    // The label to the left of the option interface element  
	'afreg_user_roles_callback',   // The name of the function responsible for rendering the option interface  
	'addify-afreg-2',                          // The page on which this option will be displayed  
	'page_1_section',         // The name of the section to which this field belongs  
	array(                              // The array of arguments to pass to the callback. In this case, just a description.  
		esc_html__('Select which user roles you want to show in dropdown on registration page. Note: Administrator role is not available for show in dropdown.', 'addify_b2b'),
	)  
);  
register_setting(  
	'afreg_setting-group-2',  
	'afreg_user_roles'  
);


//Tab 3
add_settings_section(  
	'page_1_section',         // ID used to identify this section and with which to register options  
	'',   // Title to be displayed on the administration page  
	'afreg_page_3_section_callback', // Callback used to render the description of the section  
	'addify-afreg-3'                           // Page on which to add this section of options  
);

add_settings_field(   
	'afreg_enable_approve_user',                      // ID used to identify the field throughout the theme  
	esc_html__('Enable Approve New User', 'addify_b2b'),    // The label to the left of the option interface element  
	'afreg_enable_approve_user_callback',   // The name of the function responsible for rendering the option interface  
	'addify-afreg-3',                          // The page on which this option will be displayed  
	'page_1_section',         // The name of the section to which this field belongs  
	array(                              // The array of arguments to pass to the callback. In this case, just a description.  
		esc_html__('Enable/Disable Approve new user. When this option is enabled all new registered users will be set to Pending until admin approves', 'addify_b2b'),
	)  
);  
register_setting(  
	'afreg_setting-group-3',  
	'afreg_enable_approve_user'  
);

add_settings_field(   
	'afreg_enable_approve_user_checkout',                      // ID used to identify the field throughout the theme  
	esc_html__('Enable Approve New User at Checkout Page', 'addify_b2b'),    // The label to the left of the option interface element  
	'afreg_enable_approve_user_checkout_callback',   // The name of the function responsible for rendering the option interface  
	'addify-afreg-3',                          // The page on which this option will be displayed  
	'page_1_section',         // The name of the section to which this field belongs  
	array(                              // The array of arguments to pass to the callback. In this case, just a description.  
		esc_html__('Enable/Disable Approve new user at the Checkout page. If you enable it, the order of the customer with registration will be placed and pending status is assigned to the user. Once the user logout from the site, he will not able to log in again until the administrator approves the user. If you disable it, the user will be approved automatically when registered from the checkout page.', 'addify_b2b'),
	)  
);  
register_setting(  
	'afreg_setting-group-3',  
	'afreg_enable_approve_user_checkout'  
);

add_settings_field(   
	'afreg_exclude_user_roles_approve_new_user',                      // ID used to identify the field throughout the theme  
	esc_html__('Exclude User Roles', 'addify_b2b'),    // The label to the left of the option interface element  
	'afreg_exclude_user_roles_approve_new_user_callback',   // The name of the function responsible for rendering the option interface  
	'addify-afreg-3',                          // The page on which this option will be displayed  
	'page_1_section',         // The name of the section to which this field belongs  
	array(                              // The array of arguments to pass to the callback. In this case, just a description.  
		esc_html__('Select which user roles users you want to exclude from manual approval. These user roles users will be automatically approved.', 'addify_b2b'),
	)  
);  
register_setting(  
	'afreg_setting-group-3',  
	'afreg_exclude_user_roles_approve_new_user'  
);

add_settings_section(  
	'page_2_section',         // ID used to identify this section and with which to register options  
	'',   // Title to be displayed on the administration page  
	'afreg_page_33_section_callback', // Callback used to render the description of the section  
	'addify-afreg-3'                           // Page on which to add this section of options  
);

add_settings_field(   
	'afreg_user_pending_approval_message',                      // ID used to identify the field throughout the theme  
	esc_html__('Message for Users when Account is Created', 'addify_b2b'),    // The label to the left of the option interface element  
	'afreg_user_pending_approval_message_callback',   // The name of the function responsible for rendering the option interface  
	'addify-afreg-3',                          // The page on which this option will be displayed  
	'page_2_section',         // The name of the section to which this field belongs  
	array(                              // The array of arguments to pass to the callback. In this case, just a description.  
		esc_html__('First message that will be displayed to user when he/she completes the registration process, this message will be displayed only when manual approval is required. ', 'addify_b2b'),
	)  
);  
register_setting(  
	'afreg_setting-group-3',  
	'afreg_user_pending_approval_message'  
);

add_settings_field(   
	'afreg_user_approval_message',                      // ID used to identify the field throughout the theme  
	esc_html__('Message for Users when Account is pending for approval', 'addify_b2b'),    // The label to the left of the option interface element  
	'afreg_user_approval_message_callback',   // The name of the function responsible for rendering the option interface  
	'addify-afreg-3',                          // The page on which this option will be displayed  
	'page_2_section',         // The name of the section to which this field belongs  
	array(                              // The array of arguments to pass to the callback. In this case, just a description.  
		esc_html__('This will be displayed when user will attempt to login after registration and his/her account is still pending for admin approval. ', 'addify_b2b'),
	)  
);  
register_setting(  
	'afreg_setting-group-3',  
	'afreg_user_approval_message'  
);

add_settings_field(   
	'afreg_user_disapproved_message',                      // ID used to identify the field throughout the theme  
	esc_html__('Message for Users when Account is disapproved', 'addify_b2b'),    // The label to the left of the option interface element  
	'afreg_user_disapproved_message_callback',   // The name of the function responsible for rendering the option interface  
	'addify-afreg-3',                          // The page on which this option will be displayed  
	'page_2_section',         // The name of the section to which this field belongs  
	array(                              // The array of arguments to pass to the callback. In this case, just a description.  
		esc_html__('Message for Users when Account is Disapproved By Admin.', 'addify_b2b'),
	)  
);  
register_setting(  
	'afreg_setting-group-3',  
	'afreg_user_disapproved_message'  
);



//Tab 4

add_settings_section(  
	'page_1_section',         // ID used to identify this section and with which to register options  
	'',   // Title to be displayed on the administration page  
	'afreg_page_4_section_callback', // Callback used to render the description of the section  
	'addify-afreg-4'                           // Page on which to add this section of options  
);


//enable admin email notification


add_settings_field(   
	'afreg_admin_email_text',                      // ID used to identify the field throughout the theme  
	esc_html__('Admin Email Text (New User)', 'addify_b2b'),    // The label to the left of the option interface element  
	'afreg_admin_email_text_callback',   // The name of the function responsible for rendering the option interface  
	'addify-afreg-4',                          // The page on which this option will be displayed  
	'page_1_section',         // The name of the section to which this field belongs  
	array(                              // The array of arguments to pass to the callback. In this case, just a description.  
		esc_html__('This email text will be used when new user notification is sent to admin. You can use {approve_link}, {disapprove_link}, {customer_details} variables. {approve_link}, {disapprove_link} variables will work only when manual user approval is active. ', 'addify_b2b'),
	)  
);  
register_setting(  
	'afreg_setting-group-4',  
	'afreg_admin_email_text'  
);


add_settings_field(   
	'afreg_update_user_admin_email_text',                      // ID used to identify the field throughout the theme  
	esc_html__('Admin Email Text (My Account Update)', 'addify_b2b'),    // The label to the left of the option interface element  
	'afreg_update_user_admin_email_text_callback',   // The name of the function responsible for rendering the option interface  
	'addify-afreg-4',                          // The page on which this option will be displayed  
	'page_1_section',         // The name of the section to which this field belongs  
	array(                              // The array of arguments to pass to the callback. In this case, just a description.  
		esc_html__('This email text will be used when update user notification is sent to admin. You can use {customer_details} variable to include user data. Only Custom fields data will be sent. ', 'addify_b2b'),
	)  
);  
register_setting(  
	'afreg_setting-group-4',  
	'afreg_update_user_admin_email_text'  
);



add_settings_field(   
	'afreg_user_email_text',                      // ID used to identify the field throughout the theme  
	esc_html__('User Welcome Email Text', 'addify_b2b'),    // The label to the left of the option interface element  
	'afreg_user_email_text_callback',   // The name of the function responsible for rendering the option interface  
	'addify-afreg-4',                          // The page on which this option will be displayed  
	'page_1_section',         // The name of the section to which this field belongs  
	array(                              // The array of arguments to pass to the callback. In this case, just a description.  
		esc_html__('This email text will be used when new user notification is sent to customer. You can use {customer_details} variable to include customer details. This email text will not work when new user pending approval is active.', 'addify_b2b'),
	)  
);  
register_setting(  
	'afreg_setting-group-4',  
	'afreg_user_email_text'  
);






add_settings_field(   
	'afreg_pending_approval_email_text',                      // ID used to identify the field throughout the theme  
	esc_html__('Pending Email Body Text', 'addify_b2b'),    // The label to the left of the option interface element  
	'afreg_pending_approval_email_text_callback',   // The name of the function responsible for rendering the option interface  
	'addify-afreg-4',                          // The page on which this option will be displayed  
	'page_1_section',         // The name of the section to which this field belongs  
	array(                              // The array of arguments to pass to the callback. In this case, just a description.  
		esc_html__('This email body text will be used when account is pending for approval. You can use {customer_details} variable to include customer details.', 'addify_b2b'),
	)  
);  
register_setting(  
	'afreg_setting-group-4',  
	'afreg_pending_approval_email_text'  
);




add_settings_field(   
	'afreg_approved_email_text',                      // ID used to identify the field throughout the theme  
	esc_html__('Approved Email Text', 'addify_b2b'),    // The label to the left of the option interface element  
	'afreg_approved_email_text_callback',   // The name of the function responsible for rendering the option interface  
	'addify-afreg-4',                          // The page on which this option will be displayed  
	'page_1_section',         // The name of the section to which this field belongs  
	array(                              // The array of arguments to pass to the callback. In this case, just a description.  
		esc_html__('This is the approved email message, this message is used when account is approved by administrator. You can use {customer_details} variable to include customer details.  ', 'addify_b2b'),
	)  
);  
register_setting(  
	'afreg_setting-group-4',  
	'afreg_approved_email_text'  
);




add_settings_field(   
	'afreg_disapproved_email_text',                      // ID used to identify the field throughout the theme  
	esc_html__('Disapproved Email Text', 'addify_b2b'),    // The label to the left of the option interface element  
	'afreg_disapproved_email_text_callback',   // The name of the function responsible for rendering the option interface  
	'addify-afreg-4',                          // The page on which this option will be displayed  
	'page_1_section',         // The name of the section to which this field belongs  
	array(                              // The array of arguments to pass to the callback. In this case, just a description.  
		esc_html__('This is the disapproved email message, this message is used when account is disapproved by administrator. You can use {customer_details} variable to include customer details.', 'addify_b2b'),
	)  
);  
register_setting(  
	'afreg_setting-group-4',  
	'afreg_disapproved_email_text'  
);






//Tab 1
function afreg_page_1_section_callback() {
	?>
	<div class="afb2b_setting_div">
			<p><?php echo esc_html__('Manage registration module general settings from here.', 'addify_b2b'); ?></p>
	</div>

	<?php 
} // function afreg_page_1_section_callback

function afreg_additional_fields_section_title_callback( $args ) {
	?>
	<input type="text" id="afreg_additional_fields_section_title" class="setting_fields" name="afreg_additional_fields_section_title" value="<?php echo esc_attr(get_option('afreg_additional_fields_section_title')); ?>">
	<p class="description afreg_additional_fields_section_title"> <?php echo esc_attr($args[0]); ?> </p>
	<?php      
} // end afreg_additional_fields_section_title_callback 

function afreg_page_2_section_callback() {
	?>

	<h3><?php echo esc_html__('Google reCaptcha Settings', 'addify_b2b'); ?></h3>

	<?php 
} // function afreg_page_2_section_callback

function afreg_site_key_callback( $args ) {
	?>
	<input type="text" id="afreg_site_key" class="setting_fields" name="afreg_site_key" value="<?php echo esc_attr(get_option('afreg_site_key')); ?>">
	<p class="description afreg_site_key"> <?php echo esc_attr($args[0]); ?> </p>
	<?php      
} // end afreg_site_key_callback 

function afreg_secret_key_callback( $args ) {
	?>
	<input type="text" id="afreg_secret_key" class="setting_fields" name="afreg_secret_key" value="<?php echo esc_attr(get_option('afreg_secret_key')); ?>">
	<p class="description afreg_secret_key"> <?php echo esc_attr($args[0]); ?> </p>
	<?php      
} // end afreg_secret_key_callback


//Tab 2
function afreg_page_22_section_callback() {
	?>
	<div class="afb2b_setting_div">
			<p><?php echo esc_html__('Manage user role settings from here. Choose whether you want to show user role dropdown on registration page or not and choose which user roles you want to show in dropdown on registration page.', 'addify_b2b'); ?></p>
	</div>

	<?php 
} // function afreg_page_22_section_callback

function afreg_user_role_field_text_callback( $args ) {
	?>
	<input type="text" id="afreg_user_role_field_text" class="setting_fields" name="afreg_user_role_field_text" value="<?php echo esc_attr(get_option('afreg_user_role_field_text')); ?>">
	<p class="description afreg_user_role_field_text"> <?php echo esc_attr($args[0]); ?> </p>
	<?php      
} // end afreg_user_role_field_text_callback



function afreg_allow_update_myaccount_callback( $args ) {  
	?>
			<input type="checkbox" id="afreg_allow_update_myaccount" class="setting_fields" name="afreg_allow_update_myaccount" value="yes" <?php checked('yes', esc_attr( get_option('afreg_allow_update_myaccount'))); ?> >
			<p class="description afreg_allow_update_myaccount"> <?php echo esc_attr($args[0]); ?> </p>
			<?php      
} // end afreg_allow_update_myaccount_callback


function afreg_enable_user_role_callback( $args ) {
	?>
	<input type="checkbox" id="afreg_enable_user_role" class="setting_fields" name="afreg_enable_user_role" value="yes" <?php echo checked('yes', esc_attr(get_option('afreg_enable_user_role'))); ?> >
	<p class="description afreg_enable_user_role"> <?php echo esc_attr($args[0]); ?> </p>
	<?php      
} // end afreg_enable_user_role_callback

function afreg_user_roles_callback( $args ) {
	?>
	
	<div class="all_cats">
		<ul>
	<?php

	global $wp_roles;
	$roles = $wp_roles->get_names();

	if (!empty($roles)) {

		foreach ($roles as $key => $value) {
			if ('administrator' != $key) {
				?>
					<li class="par_cat">
						
						<input type="checkbox" class="parent" name="afreg_user_roles[]" id="afreg_user_roles" value="<?php echo esc_attr($key); ?>"
				<?php
				if (!empty(get_option('afreg_user_roles'))) {
					if (in_array($key, get_option('afreg_user_roles'))) {
						echo 'checked';
					}
				}
				?>
						/>
				<?php echo esc_attr($value); ?>

					</li>
				<?php
			} 
		}
	}
	?>
		</ul>
	</div>

	<p class="description afreg_enable_user_role"> <?php echo esc_attr($args[0]); ?> </p>
	<?php      
} // end afreg_user_roles_callback


//Tab 3

function afreg_page_3_section_callback() {
	?>
	<div class="afb2b_setting_div">
			<p><?php echo esc_html__('Manage Approve new user settings from here.', 'addify_b2b'); ?></p>
			<h3>
			<?php 
			echo esc_html__('Approve New User Settings', 'addify_b2b'
				); 
			?>
				</h3>
	</div>

	<?php 
} // function afreg_page_3_section_callback


function afreg_enable_approve_user_callback( $args ) {
	?>
	<input type="checkbox" id="afreg_enable_approve_user" class="setting_fields" name="afreg_enable_approve_user" value="yes" <?php echo checked('yes', esc_attr(get_option('afreg_enable_approve_user'))); ?> >
	<p class="description afreg_enable_approve_user"> <?php echo esc_attr($args[0]); ?> </p>
	<?php      
} // end afreg_enable_approve_user_callback

function afreg_enable_approve_user_checkout_callback( $args ) {
	?>
	<input type="checkbox" id="afreg_enable_approve_user_checkout" class="setting_fields" name="afreg_enable_approve_user_checkout" value="yes" <?php echo checked('yes', esc_attr(get_option('afreg_enable_approve_user_checkout'))); ?> >
	<p class="description afreg_enable_approve_user"> <?php echo esc_attr($args[0]); ?> </p>
	<?php      
} 


function afreg_exclude_user_roles_approve_new_user_callback( $args ) {
	?>
	
	<div class="all_cats">
		<ul>
	<?php

	global $wp_roles;
	$roles = $wp_roles->get_names();

	if (!empty($roles)) {

		foreach ($roles as $key => $value) {
			if ('administrator' != $key) {
				?>
					<li class="par_cat">
						
						<input type="checkbox" class="parent" name="afreg_exclude_user_roles_approve_new_user[]" id="afreg_exclude_user_roles_approve_new_user" value="<?php echo esc_attr($key); ?>"
				<?php
				if (!empty(get_option('afreg_exclude_user_roles_approve_new_user'))) {
					if (in_array($key, get_option('afreg_exclude_user_roles_approve_new_user'))) {
						echo 'checked';
					}
				}
				?>
						/>
				<?php echo esc_attr($value); ?>

					</li>
				<?php
			} 
		}
	}
	?>
		</ul>
	</div>

	<p class="description afreg_exclude_user_roles_approve_new_user"> <?php echo esc_attr($args[0]); ?> </p>
	<?php      
} // end afreg_user_roles_callback

function afreg_page_33_section_callback() {
	?>

	<h3><?php echo esc_html__('Approve New User Messages Settings', 'addify_b2b'); ?></h3>

	<?php 
} // function afreg_page_33_section_callback


function afreg_user_pending_approval_message_callback( $args ) {
	?>
	<textarea name="afreg_user_pending_approval_message" id="afreg_user_pending_approval_message" rows="10" cols="70"><?php echo esc_textarea(get_option('afreg_user_pending_approval_message')); ?></textarea>
	<p class="description afreg_user_pending_approval_message"> <?php echo esc_attr($args[0]); ?> </p>
	<?php      
} // end afreg_user_pending_approval_message_callback

function afreg_user_approval_message_callback( $args ) {
	?>
	<textarea name="afreg_user_approval_message" id="afreg_user_approval_message" rows="10" cols="70"><?php echo esc_textarea(get_option('afreg_user_approval_message')); ?></textarea>
	<p class="description afreg_user_approval_message"> <?php echo esc_attr($args[0]); ?> </p>
	<?php      
} // end afreg_user_approval_message_callback

function afreg_user_disapproved_message_callback( $args ) {
	?>
	<textarea name="afreg_user_disapproved_message" id="afreg_user_disapproved_message" rows="10" cols="70"><?php echo esc_textarea(get_option('afreg_user_disapproved_message')); ?></textarea>
	<p class="description afreg_user_disapproved_message"> <?php echo esc_attr($args[0]); ?> </p>
	<?php      
} // end afreg_user_disapproved_message_callback



//Tab 4
function afreg_page_4_section_callback() {
	?>
	<div class="afb2b_setting_div">
			<p><?php echo esc_html__('Manage Email Settings from here.', 'addify_b2b'); ?></p>
	</div>

	<?php 
} // function afreg_page_4_section_callback



function afreg_admin_email_text_callback( $args ) {
	?>
	
	<?php

	$content   = get_option('afreg_admin_email_text');
	$editor_id = 'afreg_admin_email_text';
	$settings  = array(
		'wpautop'       => false,
		'tinymce'       => true,
		'textarea_rows' => 10,
		'quicktags'     => array( 'buttons' => 'em,strong,link' ),
	);

	wp_editor($content, $editor_id, $settings);

	?>
	<p class="description afreg_admin_email_text"> <?php echo esc_attr($args[0]); ?> </p>
	<?php      
} // end afreg_admin_email_text_callback

function afreg_update_user_admin_email_text_callback( $args ) {  
	?>
			
			<?php

			$content   = get_option('afreg_update_user_admin_email_text');
			$editor_id = 'afreg_update_user_admin_email_text';
			$settings  = array(
				'wpautop'       => false,
				'tinymce'       => true,
				'textarea_rows' => 10,
				'quicktags'     => array( 'buttons' => 'em,strong,link' ),
				
			);

			wp_editor( $content, $editor_id, $settings );

			?>
			<p class="description afreg_update_user_admin_email_text"> <?php echo esc_attr($args[0]); ?> </p>
			<?php      
} // end afreg_update_user_admin_email_text_callback

function afreg_user_email_text_callback( $args ) {  
	?>
			
	<?php

	$content   = get_option('afreg_user_email_text');
	$editor_id = 'afreg_user_email_text';
	$settings  = array(
		'wpautop'       => false,
		'tinymce'       => true,
		'textarea_rows' => 10,
		'quicktags'     => array( 'buttons' => 'em,strong,link' ),
		
	);

	wp_editor( $content, $editor_id, $settings );

	?>
	<p class="description afreg_user_email_text"> <?php echo esc_attr($args[0]); ?> </p>
	<?php      
} // end afreg_user_email_text_callback






function afreg_pending_approval_email_text_callback( $args ) {
	?>
	
	<?php

	$content   = get_option('afreg_pending_approval_email_text');
	$editor_id = 'afreg_pending_approval_email_text';
	$settings  = array(
		'wpautop'       => false,
		'tinymce'       => true,
		'textarea_rows' => 10,
		'quicktags'     => array( 'buttons' => 'em,strong,link' ),
		
	);

	wp_editor($content, $editor_id, $settings);

	?>
	<p class="description afreg_pending_approval_email_text"> <?php echo esc_attr($args[0]); ?> </p>
	<?php      
} // end afreg_pending_approval_email_text_callback



function afreg_approved_email_text_callback( $args ) {
	?>
	
	<?php

	$content   = get_option('afreg_approved_email_text');
	$editor_id = 'afreg_approved_email_text';
	$settings  = array(
		'wpautop'       => false,
		'tinymce'       => true,
		'textarea_rows' => 10,
		'quicktags'     => array( 'buttons' => 'em,strong,link' ),
		
	);

	wp_editor($content, $editor_id, $settings);

	?>
	<p class="description afreg_approved_email_text"> <?php echo esc_attr($args[0]); ?> </p>
	<?php      
} // end afreg_approved_email_text_callback




function afreg_disapproved_email_text_callback( $args ) {
	?>
	
	<?php

	$content   = get_option('afreg_disapproved_email_text');
	$editor_id = 'afreg_disapproved_email_text';
	$settings  = array(
		'wpautop'       => false,
		'tinymce'       => true,
		'textarea_rows' => 10,
		'quicktags'     => array( 'buttons' => 'em,strong,link' ),
		
	);

	wp_editor($content, $editor_id, $settings);

	?>
	<p class="description afreg_disapproved_email_text"> <?php echo esc_attr($args[0]); ?> </p>
	<?php      
} // end afreg_disapproved_email_text_callback
