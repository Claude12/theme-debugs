<?php
wp_head();
get_header();

if ( is_user_logged_in() ) {
	$user      = wp_get_current_user();
	$roles     = ( array ) $user->roles;
	$curr_role = current( $roles );
} else {

	$curr_role = 'guest';
}

if (!empty(get_option('afpvu_user_role_visibility'))) {

	$role_selected_data = get_option('afpvu_user_role_visibility');

} else {

	$role_selected_data = array();

}

if (!empty($role_selected_data[ esc_attr( $curr_role ) ]['afpvu_enable_role'])) {

	$role_data = 'yes';

} else {

	$role_data = 'no';
}

?>
<div class="pro_visib_msg">
<?php
if ('yes' == $role_data) {

	if (!empty($role_selected_data[ esc_attr( $curr_role ) ]['afpvu_custom_message_role'])) {

		echo wp_kses_post( $role_selected_data[ esc_attr( $curr_role ) ]['afpvu_custom_message_role'] ); 
	}

} elseif ('no' == $role_data) {

	echo wp_kses_post( get_option( 'afpvu_global_custom_msg' ) ); 
}
	
?>
</div>


<?php get_footer(); ?>
