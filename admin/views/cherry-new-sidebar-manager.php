<?php
// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
	die;
}
/**
 * Custom sidebar dom.
 *
 * @since 1.0.0
 */

if( !function_exists( 'cherry_register_sidebar' ) ) {
	function cherry_register_sidebar( $args ) {

		// Set up some default sidebar arguments.
		$defaults = array(
			'id'            => '',
			'name'          => '',
			'description'   => '',
			'before_widget' => '<aside id="%1$s" class="widget %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		);

		/**
		 * Filteras the default sidebar arguments
		 *
		 * @since 4.0.0
		 * @param array $defaults
		 */
		$defaults = apply_filters( 'cherry_sidebar_defaults', $defaults );

		// Parse the arguments.
		$args = wp_parse_args( $args, $defaults );

		/**
		 * Filters the sidebar arguments.
		 *
		 * @since 4.0.0
		 * @param array $args
		 */
		$args = apply_filters( 'cherry_sidebar_args', $args );

		/**
		 * Fires before execute WordPress `register_sidebar` function.
		 *
		 * @since 4.0.0
		 * @param array $args
		 */
		do_action( 'cherry_register_sidebar', $args );

		/**
		 * Register the sidebar.
		 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
		 */
		return register_sidebar( $args );
	}
}

add_action('wp_ajax_add_new_custom_sidebar', 'add_custom_sidebar' );

function add_custom_sidebar() {
	check_ajax_referer( 'new_custom_sidebar', 'security' );

	$nonce = isset($_GET['security']) ? $_GET['security'] : $security ;
	if ( !wp_verify_nonce( $nonce, 'new_custom_sidebar' ) ){
		exit;
	}
	global $wp_registered_sidebars;

	$Cherry_Custom_Sidebars_Methods = new Cherry_Custom_Sidebars_Methods();
	$cusotm_sidebar_array = $Cherry_Custom_Sidebars_Methods -> get_custom_sidebar_array();

	$form_data = isset($_GET['formdata']) ? $_GET['formdata'] : $formdata ;

	if(!array_key_exists('cherry-sidebar-manager-counter', $cusotm_sidebar_array)){
		$cusotm_sidebar_array['cherry-sidebar-manager-counter'] = 0;
	}else{
		$cusotm_sidebar_array['cherry-sidebar-manager-counter'] +=1;
	}
	$id = $cusotm_sidebar_array['cherry-sidebar-manager-counter'];
	$args = array(
		'name' => $form_data[0]['value'],
		'id' => 'cherry-sidebar-manager-'.$id,
		'description' => $form_data[1]['value']
		);
	$registrate_custom_sidebar = cherry_register_sidebar ($args);
	$cusotm_sidebar_array['cherry-sidebar-manager-'.$id] = $wp_registered_sidebars[ $registrate_custom_sidebar ];
?>
	<div class="widgets-holder-wrap closed cherry-widgets-holder-wrap">
		<div class='cherry-delete-sidebar-manager'>
			<div class="cherry-spinner-wordpress spinner-wordpress-type-1"><span class="cherry-inner-circle"></span></div>
			<span class="dashicons dashicons-trash"></span>
		</div>
		<div id="<?php echo esc_attr ('cherry-sidebar-manager-' . $id) ?>" class="widgets-sortables ui-sortable cherry-sidebar-manager">
			<div class="sidebar-name">
				<div class="sidebar-name-arrow"><br></div>
				<h3><?php echo esc_html($form_data[0]['value']) ?><span class="spinner"></span></h3>
			</div>
			<div class="sidebar-description">
				<p class="description"><?php echo esc_html($form_data[1]['value']) ?></p>
			</div>
		</div>
	</div>
<?php
	$Cherry_Custom_Sidebars_Methods -> set_custom_sidebar_array($cusotm_sidebar_array);
	wp_die();
}
add_action('wp_ajax_remove_custom_sidebar', 'remove_custom_sidebar' );

function remove_custom_sidebar() {
	check_ajax_referer( 'remove_custom_sidebar', 'security' );

	$nonce = isset($_GET['security']) ? $_GET['security'] : $security ;
	if ( !wp_verify_nonce( $nonce, 'remove_custom_sidebar' ) ){
		exit;
	}

	$id = isset($_GET['id']) ? $_GET['id'] : $id ;

	$Cherry_Custom_Sidebars_Methods = new Cherry_Custom_Sidebars_Methods();
	$cusotm_sidebar_array = $Cherry_Custom_Sidebars_Methods -> get_custom_sidebar_array();

	unset($cusotm_sidebar_array[$id]);

	$Cherry_Custom_Sidebars_Methods -> set_custom_sidebar_array($cusotm_sidebar_array);
}
?>