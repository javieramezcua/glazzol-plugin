<?php
	setlocale(LC_ALL,"es_ES");

	//require 'php/app.php';
	
	//	set_locale(LC_ALL,"es_ES@euro","es_ES","esp");
	/**
	 * Storefront engine room
	 *
	 * @package storefront
	 */

	/**
	 * Assign the Storefront version to a var
	 */
	$theme              = wp_get_theme( 'storefront' );
	$storefront_version = $theme['Version'];

	/**
	 * Set the content width based on the theme's design and stylesheet.
	 */
	if ( ! isset( $content_width ) ) {
		$content_width = 980; /* pixels */
	}

	$storefront = (object) array(
		'version' => $storefront_version,

		/**
		 * Initialize all the things.
		 */
		'main'       => require 'inc/class-storefront.php',
		'customizer' => require 'inc/customizer/class-storefront-customizer.php',
	);

	require 'inc/storefront-functions.php';
	require 'inc/storefront-template-hooks.php';
	require 'inc/storefront-template-functions.php';

	if ( class_exists( 'Jetpack' ) ) {
		$storefront->jetpack = require 'inc/jetpack/class-storefront-jetpack.php';
	}
	// archivos para la api-app
	require 'php/wp.php';
	require 'php/app.php';
	require 'php/glazzolCustomFnct.php';
	if ( storefront_is_woocommerce_activated() ) {
		$storefront->woocommerce = require 'inc/woocommerce/class-storefront-woocommerce.php';

		require 'inc/woocommerce/storefront-woocommerce-template-hooks.php';
		require 'inc/woocommerce/storefront-woocommerce-template-functions.php';
	}

	if ( is_admin() ) {
		$storefront->admin = require 'inc/admin/class-storefront-admin.php';

		require 'inc/admin/class-storefront-plugin-install.php';
	}
	if ( ! is_admin() ) {
		/* enqueue's de estilo y demas cosas Glazzol */
		wp_enqueue_style( 'font-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css' );
		//wp_enqueue_style( 'bootstrap', 'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css' );
		wp_enqueue_style( 'bootstrap', 'https://bootswatch.com/3/yeti/bootstrap.min.css' );
		wp_enqueue_style( 'lightslider',  get_template_directory_uri() . '/assets/lightslider/css/lightslider.css' );
		wp_enqueue_style( 'flexslider-css', get_template_directory_uri() . '/assets/flexslider/flexslider.css');//owl.complementary.css
		wp_enqueue_script( 'flexslider-js', get_template_directory_uri() . '/assets/flexslider/jquery.flexslider-min.js', array( 'jquery') );
		wp_enqueue_script( 'bootstrap-js', 'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js', array( 'jquery') );
		wp_enqueue_script( 'maskedinput-js', get_template_directory_uri() . '/js/jquery.maskedinput.min.js', array( 'jquery') );
		wp_enqueue_script( 'lightslider-js', get_template_directory_uri() . '/assets/lightslider/js/lightslider.js', array( 'jquery') );
		/* fin de enqueues glazzol  de estilos y js*/
		wp_enqueue_style('owl.carousel', get_template_directory_uri() . '/assets/owl/owl.carousel.min.css' );
		wp_enqueue_style('owl.theme', get_template_directory_uri() . '/assets/owl/owl.theme.default.min.css', array(), '2.2.4' );
		wp_enqueue_script('owl.carousel', get_template_directory_uri() . '/assets/owl/owl.carousel.min.js' );
	}
	function my_theme_remove_storefront_standard_functionality() {
		set_theme_mod('storefront_styles', '');
		set_theme_mod('storefront_woocommerce_styles', '');  
	}
	/**
	 * NUX
	 * Only load if wp version is 4.7.3 or above because of this issue;
	 * https://core.trac.wordpress.org/ticket/39610?cversion=1&cnum_hist=2
	 */
	if ( version_compare( get_bloginfo( 'version' ), '4.7.3', '>=' ) && ( is_admin() || is_customize_preview() ) ) {
		require 'inc/nux/class-storefront-nux-admin.php';
		require 'inc/nux/class-storefront-nux-guided-tour.php';

		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '3.0.0', '>=' ) ) {
			require 'inc/nux/class-storefront-nux-starter-content.php';
		}
	}

	/**
	 * Note: Do not add any custom code here. Please use a custom plugin so that your customizations aren't lost during updates.
	 * https://github.com/woocommerce/theme-customisations
	 */
	function init_js_scripts() {
		wp_enqueue_script('my_functions', get_template_directory_uri() . '/js/functions.js', array('jquery'), '', true);
	}
	add_action('init', 'init_js_scripts');

	if( is_page('home') ){
		echo '<style>.entry-header{display: none;}</style>';
	}


	add_action( 'woocommerce_archive_description', 'woocommerce_category_image', 2 );

	function woocommerce_category_image() {
		if ( is_product_category() ){
			global $wp_query;
			$cat = $wp_query->get_queried_object();
			$thumbnail_id = get_woocommerce_term_meta( $cat->term_id, 'thumbnail_id', true );
			$image = wp_get_attachment_url( $thumbnail_id );
			if ( $image ) {
				echo '<div class="row">';
				echo '	<div class="col-xs-12 hideOnMobil">';
				echo '		<img src="' . $image . '" alt="' . $cat->name . '" style="margin: 0 auto; max-width: 300px;" />';
				echo '		<style>.page-title{display: none;} .woocommerce-products-header{padding-bottom: 0 !important;}</style>';
				echo '	</div>';
				echo '</div>';
			}
		}
		if ( is_product_category() || is_product_tag() ){
			echo '<ul id="sliderMarcas" class="cs-hidden">';
			
			
			$taxonomy     = 'product_cat';
			$orderby      = 'name';  
			$show_count   = 1;      // 1 for yes, 0 for no
			$pad_counts   = 1;      // 1 for yes, 0 for no
			$hierarchical = 1;      // 1 for yes, 0 for no  
			$title        = '';  
			$empty        = 1;

			$args = array(
				'taxonomy'     => $taxonomy,
				'orderby'      => $orderby,
				'show_count'   => $show_count,
				'pad_counts'   => $pad_counts,
				'hierarchical' => $hierarchical,
				'title_li'     => $title,
				'hide_empty'   => $empty
			);
			
			$all_categories = get_categories( $args );
			foreach ($all_categories as $cat) {
				if($cat->category_parent == 0) {
					$category_id = $cat->term_id;       
					
					$thumbnail_id = get_woocommerce_term_meta( $cat->term_id, 'thumbnail_id', true );
					$image = wp_get_attachment_url( $thumbnail_id );
		
					if ( $image ) {
						echo '<li><a href="'. get_term_link($cat->slug, 'product_cat') .'" class=""><img src="' . $image . '" alt="' . $cat->name . '" style="margin: 0 auto; max-width: 100%;" /></a></li>';
					}
					// echo '<li><a href="'. get_term_link($cat->slug, 'product_cat') .'" class="itemthird">'. $cat->name .' neme</a></li>';
				}       
			}
			echo '</ul>';
		}
	}

	if( empty($_GET)){
		$url = '';
	} else {
		$url = '?';
	}
			
	foreach($_GET as $query_string_variable => $value) {
		$url .= $query_string_variable .'='. $value . '&';
		if( $query_string_variable != 'pa_genero-lente'){
			$urlC .= $query_string_variable .'='. $value . '&';
		}
		if( $query_string_variable != 'stock'){
			$urlC .= $query_string_variable .'='. $value . '&';
		}
	}

	add_filter( 'loop_shop_per_page', 'new_loop_shop_per_page', 21 );

	function new_loop_shop_per_page( $cols ) {
		// $cols contains the current number of products per page based on the value stored on Options -> Reading
		// Return the number of products you wanna show per page.
		$cols = 21;
		return $cols;
	}

	add_action( 'get_header', 'remove_storefront_sidebar' );
	function remove_storefront_sidebar() {
		if ( is_shop() ) {
			remove_action( 'storefront_sidebar', 'storefront_get_sidebar', 10 );
		}
	}
	get_locale(); 
	
	/**
	 * Add checkbox field to the checkout
	 **/
	add_action('woocommerce_after_order_notes', 'my_custom_checkout_field');
	
	function my_custom_checkout_field( $checkout ) { 
		
		woocommerce_form_field( 'factura', array(
			'type'          => 'checkbox',
			'class'         => array('input-checkbox', 'checkbox-facturacion', 'show'),
			'label'         => __('Solicitar factura <span data-toggle="tooltip" title="Si deseas facturar tu pedido, envía un correo a hola@glazzol.com con tus datos de facturación completos.,"><i class="fa fa-question-circle" aria-hidden="true"></i>
	</span>'),
			'required'  => false,
		), $checkout->get_value( 'factura' ));
		
		woocommerce_form_field( 'envolver', array(
			'type'          => 'checkbox',
			'class'         => array('input-checkbox', 'checkbox-envolverRegalo', 'show'),
			'label'         => __('¿Deseas Envolver para Regalo? <span data-toggle="tooltip" title="¡Tu paquete envuelto para regalo!"><i class="fa fa-question-circle" aria-hidden="true"></i>
</span>'),
			'required'  => false,
		), $checkout->get_value( 'envolver' ));
	}
	 
	// Update the order meta with field value

	add_action('woocommerce_checkout_update_order_meta', 'my_custom_checkout_field_update_order_meta');
	 
	function my_custom_checkout_field_update_order_meta( $order_id ) {
		if ($_POST['factura']) {update_post_meta( $order_id, 'factura', esc_attr("Desea Factura del pedido"));}//esc_attr($_POST['factura'])
		if ($_POST['envolver']) {update_post_meta( $order_id, 'envolver', esc_attr("Desea pedido sea envuelto"));}//esc_attr($_POST['envolver']
	}
	 

	global $wp_rewrite;
	$wp_rewrite->flush_rules();

	add_action('init', 'customRSS');
	function customRSS(){
	    add_feed('feedname', 'customRSSFunc');
	}

	function customRSSFunc(){
	    get_template_part('rss', 'feedname');
	}
?>