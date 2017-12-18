<?php
	
	add_action( 'rest_api_init', 'glazzol_rest_api_init' );

	function glazzol_rest_api_init() {
		$namespace = 'glazzol-api';
		// -->/filters
		register_rest_route( $namespace,
			'/filters/',
			array(
				array(
					'methods' => WP_REST_Server::READABLE,
					'callback' => 'glazzol_rest_get_filters',
				),
			)
		);
		// --> /catalog/
		register_rest_route($namespace,
			'/catalog/',
			array(
				array(
					'methods' => WP_REST_Server::READABLE,
					'callback' => 'glazzol_rest_get_catalog',
				),
			)
		);
		// --> /catalog/<id>
		register_rest_route($namespace,
			'/catalog/(?P<idproduct>\d+)',
			array(
				array(
					'methods' => WP_REST_Server::READABLE,
					'callback' => 'glazzol_rest_get_single',
				),
			)
		);
		// --> /shopping/
		register_rest_route($namespace,
			'/shopping/',
			array(
				array(
					'methods' => WP_REST_Server::ALLMETHODS,
					'callback' => 'glazzol_rest_get_shopping',
				),
			)
		);
		// --> /coupons/<codigo>
		register_rest_route($namespace,
			'/coupons/',
			array(
				array(
					'methods' => WP_REST_Server::READABLE,
					'callback' => 'glazzol_rest_get_coupons',
				),
			)
		);
		// --> /shippingmethods/
		register_rest_route($namespace,
			'/shippingmethods/',
			array(
				array(
					'methods' => WP_REST_Server::READABLE,
					'callback' => 'glazzol_rest_get_shippingmethods',
				),
			)
		);
		// --> /contact/
		register_rest_route($namespace,
			'/contact/',
			array(
				array(
					'methods' => WP_REST_Server::ALLMETHODS,
					'callback' => 'glazzol_rest_get_contact',
				),
			)
		);
		// --> /returns/
		register_rest_route($namespace,
			'/returns/',
			array(
				array(
					'methods' => WP_REST_Server::ALLMETHODS,
					'callback' => 'glazzol_rest_create_return',
				),
			)
		);
		// --> /users/
		register_rest_route($namespace,
			'/users/',
			array(
				array(
					'methods'	=> WP_REST_Server::CREATABLE
					,'callback' => 'glazzol_rest_register_users',
				),
			)
		);
		register_rest_route($namespace,
			'/users/(?P<mail>.*)/reset/',
			array(
				array(
					'methods'	=> WP_REST_Server::READABLE
					,'callback' => 'glazzol_reset_user_pass',
				),
			)
		);
		// --> /users/<mail>/pass/<pass>/
		register_rest_route($namespace,
			'/users/(?P<mail>.*)/pass/(?P<pass>.*)/',
			array(
				array(
					'methods'	=> WP_REST_Server::READABLE
					,'callback' => 'glazzol_rest_login_users',
				),
			)
		);
		// --> /users/<iduser>/purchases/
		register_rest_route($namespace,
			'/users/(?P<iduser>\d+)/purchases/',
			array(
				array(
					'methods' => WP_REST_Server::READABLE,
					'callback' => 'glazzol_rest_get_users_purchases',
				),
			)
		);
		// --> /users/<iduser>/purchases/
		register_rest_route($namespace,
			'/users/(?P<iduser>\d+)/purchases/(?P<purchase_id>\d+)',
			array(
				array(
					'methods' => WP_REST_Server::READABLE,
					'callback' => 'glazzol_rest_get_users_purchases_single',
				),
			)
		);
		// --> /users/<iduser>/avatars/
		register_rest_route($namespace,
			'/users/(?P<iduser>\d+)/avatars/',
			array(
				array(
					'methods' => WP_REST_Server::ALLMETHODS,
					'callback' => 'glazzol_rest_users_avatars',
				),
			)
		);
		
		register_rest_route($namespace,
			'/demoresponses/',
			array(
				array(
					'methods' => WP_REST_Server::READABLE,
					'callback' => 'glazzol_rest_get_responses',
				),
			)
		);
		//glazzol_rest_get_users_favs
		register_rest_route($namespace,
			'/users/(?P<iduser>\d+)/favorites/',
			array(
				array(
					'methods' => WP_REST_Server::ALLMETHODS,
					'callback' => 'glazzol_rest_get_users_favs',
				),
			)
		);
		//glazzol_rest_get_users_polls
		register_rest_route($namespace,
			'/users/(?P<iduser>\d+)/polls/',
			array(
				array(
					'methods' => WP_REST_Server::ALLMETHODS,
					'callback' => 'glazzol_rest_crud_users_polls',
				),
			)
		);
		
		register_rest_route($namespace,
			'/users/(?P<customermail>.*)/address/',
			array(
				array(
					'methods' => WP_REST_Server::ALLMETHODS,
					'callback' => 'glazzol_rest_get_addressesByMail',
				),
			)
		);
		
		register_rest_route($namespace,
			'/globales/',
			array(
				array(
					'methods' => WP_REST_Server::READABLE,
					'callback' => 'glazzol_rest_get_globals',
				),
			)
		);

		register_rest_route($namespace,
			'/slugs/',
			array(
				array(
					'methods' => WP_REST_Server::READABLE,
					'callback' => 'glazzol_rest_get_slugs',
				),
			)
		);
	}

	
	
	/* ********************************************************* */
	
	require_once('catalogFunctions.php');
	
	require_once('usersFunctions.php');	

	require_once('contactReturnFunctions.php');

	require_once('shoppingPaymentFunctions.php'); // <--- pasarelas de pago... conekta y paypal

	function glazzol_rest_get_responses(WP_REST_Request $request){

		$result = rest_ensure_response(array('result'=>'OK','message'=>'si jala esta vaina',"tipo de solicitud"=>$_SERVER['REQUEST_METHOD']));
		$result->set_status(405);
		
		return $result;
	}

	function glazzol_rest_get_globals(WP_REST_Request $request){
		$result = rest_ensure_response(array('result'=>'OK','message'=>'si jala esta vaina',"tipo de solicitud"=>$GLOBALS));
		$result->set_status(200);
		
		return $result;
	}
	function glazzol_rest_get_slugs(WP_REST_Request $request){
	  $taxonomy     = 'product_cat';
	  $orderby      = 'name';  
	  $show_count   = 0;      // 1 for yes, 0 for no
	  $pad_counts   = 0;      // 1 for yes, 0 for no
	  $hierarchical = 1;      // 1 for yes, 0 for no  
	  $title        = '';  
	  $empty        = 0;

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
			echo '<br />'. $cat->slug;

			$args2 = array(
					'taxonomy'     => $taxonomy,
					'child_of'     => 0,
					'parent'       => $category_id,
					'orderby'      => $orderby,
					'show_count'   => $show_count,
					'pad_counts'   => $pad_counts,
					'hierarchical' => $hierarchical,
					'title_li'     => $title,
					'hide_empty'   => $empty
			);
			/*
			$sub_cats = get_categories( $args2 );
			if($sub_cats) {
				foreach($sub_cats as $sub_category) {
					echo  $sub_category->name ;
				}   
			}
			*/
		}       
	}
}