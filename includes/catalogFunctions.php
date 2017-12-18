<?php
	function glazzol_rest_get_filters( WP_REST_Request $request ) {
		global $wpdb; 
		$listadoMarcas = [];
		$listadoTipoLente = [];
		$listadocolorArmazon = [];
		$listadoGeneroLente = [];
		$listadoTamanoCara =[];
		$precios = [];
		$marcas = get_terms( 'product_cat',array('term_id','name','hide_empty' => true));
		foreach($marcas as $marca){
			if(! empty($marca->name) ){
				array_push($listadoMarcas, ['id'=>$marca->term_id,'name'=>$marca->name]);
			}
		}
		$tipoLente = get_terms('pa_tipo-lente');//,array('hide_empty'=>true)
		foreach($tipoLente as $elemento){
			//if(! empty($elemento->name) ){
				array_push($listadoTipoLente,['id' => $elemento->term_id,'name'=>$elemento->name ]);
			//}
		}
		$tipoLente = get_terms('pa_genero-lente',array('hide_empty'=>true));
		foreach($tipoLente as $elemento){
			if(! empty($elemento->name) ){
				array_push($listadoGeneroLente,['id' => $elemento->term_id,'name'=>$elemento->name ]);
			}
		}
		$tamanoCara = get_terms('pa_tamano-lente',array('hide_empty'=>true));
		foreach($tamanoCara as $elemento){
			if(! empty($elemento->name) ){
				array_push($listadoTamanoCara,['id' => $elemento->term_id,'name'=>$elemento->name ]);
			}
		}
		$qry = "SELECT CAST(meta_value AS decimal) AS min FROM wpvl_postmeta WHERE meta_key = '_sale_price' ORDER BY CAST(meta_value AS decimal) ASC";
		//echo $qry;
		$result= $wpdb->get_row($qry,0,1);
		//die();
		$qry2 = "SELECT CAST(meta_value AS decimal) AS max FROM wpvl_postmeta WHERE meta_key = '_sale_price' ORDER BY CAST(meta_value AS decimal) DESC";
		$result2= $wpdb->get_row($qry2,0,0);
		$precios[]= array('min'=>$result->min,'max'=>$result2->max);
		$arregloAJson = array("status"=> "OK",'gender'=>$listadoGeneroLente,'brand'=>$listadoMarcas,'frameType'=>$listadoTipoLente,'faceSize'=>$listadoTamanoCara,'limitPrice'=>$precios);
		return rest_ensure_response( $arregloAJson );
	}
	/* ********************************************************* */
	function glazzol_rest_get_catalog(WP_REST_Request $request){
		global $wpdb; 
		$upload_dir = wp_upload_dir();
		$catalog = array(
			'response'			=> array()
			,'status' 			=> array()
			,'paginaActual' 	=> array()
			,'totalPaginas'		=> array()
			,'totalProductos'	=> array()
			,'glasses'			=> array()
		);
		if(!isset($_REQUEST['brand']) && !isset($_REQUEST['gender']) && !isset($_REQUEST['frameType']) && !isset($_REQUEST['faceSize']) && !isset($_REQUEST['outlet'])){
			$arr="";
			$arrPrecios="";
		}
		else{
			$arr = [];
			$arr[]=array('taxonomy' => 'product_cat','field'=> 'term_id','terms'=> '','operator' => 'NOT IN');
			if(isset($_REQUEST['brand'])   ){
				$arr[]=array('taxonomy' => 'product_cat','field'=> 'term_id','terms'=> $_REQUEST['brand']);
			}
			if(isset($_REQUEST['gender'])  ){
				$arr[]=array('taxonomy' => 'pa_genero-lente','field'=> 'term_id','terms'=> $_REQUEST['gender']);
			}
			if(isset($_REQUEST['frameType'])){
				$arr[]=array('taxonomy' => 'pa_tipo-lente','field'=> 'term_id','terms'=> $_REQUEST['frameType']);
			}
			if(isset($_REQUEST['faceSize'])){
				$arr[]=array('taxonomy' => 'pa_tamano-lente','field'=> 'term_taxonomy_id','terms'=> $_REQUEST['faceSize']);
			}
			if(isset($_REQUEST['outlet'])){
				$arr[]=array('product_tag'  => 'outlook');
			}
			//&maxPrice=5678&minPrice=1234
			if(isset($_REQUEST['minPrice']) && isset($_REQUEST['maxPrice'])){
				$arrPrecios[] = array('key'=>'_price','value' => array($_REQUEST['minPrice'],$_REQUEST['maxPrice']),'compare' => 'BETWEEN','type' => 'NUMERIC');
				//$arr2[] = array('key'=>'_regular_price','value' => $_REQUEST['maxPrice'],'compare' => '<=');
			}

		}
		$arrThumns =  array('key'=>'_thumbnail_id','compare' => 'EXISTS');
		(isset($_REQUEST['paged'])?$page = $_REQUEST['paged'] : $page = 1 );
		//var_dump($arrThumns);
		$args = array(
			'tax_query' => array(
				$arr
			),
			'meta_query' => array(
				array(
					'key'=>'_app',
					'value'   => '',
					'compare' => '!='
				)
				,$arrPrecios
			), 
			'post_type' => 'product',
			'post_status' => 'publish',
			'orderby' => array( 'id' => 'ASC'),
			'paged' => $page,
			'posts_per_page' => 30
		);
		$contador = 0;
		$loop = new WP_Query( $args );
		if ( $loop->have_posts() ) {
			while ( $loop->have_posts() ) : $loop->the_post();
				//$glasses['id'] = $loop->post->ID;
				$glasses['id'] = $loop->post->ID;
				$producto = wc_get_product($loop->post->ID);
				$images_array = array();
				if( ! empty( get_post_meta( $loop->post->ID, '_app', true) )){
					$images = explode( ',', get_post_meta( $loop->post->ID, '_app', true) );
					foreach($images as $img){
						$images_array[]['src']= wp_get_attachment_url( $img );
					}
				}	
				$nombreCat = get_the_terms($loop->post->ID,'product_cat');
				$thumb_id = get_woocommerce_term_meta( $nombreCat[0]->term_id, 'thumbnail_id', true ); 
				
				// var_dump($nombreCat[0]);
				$glasses['glassesImage'] = $images_array;
				//$upload_dir['baseurl'] . '/' .
				//$glasses['brand_image'] = get_site_url()."/wp-content/uploads/logosapp/".$nombreCat[0]->slug.".png";//wp_get_attachment_url(  
				$glasses['brand_image'] = $upload_dir['baseurl'] . "/logosapp/".$nombreCat[0]->slug.".png";//wp_get_attachment_url(  $thumb_id );
				//var_dump($glasses['brand_image']);
				$isThisFav = false;

				if(isset($_REQUEST['user_id'])){
					$qrGetIsFav = $wpdb->get_row("SELECT sku FROM tr_favorites WHERE user_id = ".$_REQUEST['user_id']." AND sku = ".$loop->post->ID);
					if(!empty($qrGetIsFav)) {
						$isThisFav = true;
					}
				}
				$glasses['favorite'] = $isThisFav;
				$glasses['name']  = $loop->post->post_title;
				$glasses['price'] = ceil($producto->get_sale_price());
				$glasses['regularPrice'] = ceil($producto->get_regular_price());
				$glasses['available'] = (get_post_meta($loop->post->ID, '_stock', true)>0?true:false);
				$glasses['tryable'] = (get_post_meta($loop->post->ID, '_probador', true)!=false?true:false);
				$glasses['promotionalDateFinish'] = $producto->get_date_on_sale_to(); // fin de promo --> pringin and disc
				/* ************************************ */
				$characteristics = [];
				$tamanoLente = get_the_terms($loop->post->ID, 'pa_tamano-lente');
				$generoLente = get_the_terms($loop->post->ID, 'pa_genero-lente');
				$graduableLente = get_the_terms($loop->post->ID, 'pa_graduable-lente');
				array_push($characteristics,array('gender'=>$generoLente[0]->term_id));
				array_push($characteristics,array('faceSize'=>$tamanoLente[0]->term_id));
				array_push($characteristics,array('polarizable'=>$graduableLente[0]->term_id));
				$glasses['characteristics'] = array('gender'=>$generoLente[0]->term_id,'faceSize'=>$tamanoLente[0]->term_id,'polarizable'=>$graduableLente[0]->term_id);
				$glasses['UPC'] = get_post_meta($loop->post->ID, '_UPC',true);
				$catalog['response']='200';
				$catalog['status']="OK";
				$catalog['paginaActual'] = $page;
				$catalog['totalPaginas'] = $loop->max_num_pages;
				$imgMarca = $_SERVER['DOCUMENT_ROOT']."/wp-content/uploads/logosapp/".$nombreCat[0]->slug.".png";
				if(file_exists($imgMarca)){
					$catalog['glasses'][] = $glasses;
					$contador++;
				}
			endwhile;
			$catalog['totalProductos']=$contador;
			return rest_ensure_response($catalog);
		} 
		else{
			return new WP_Error( 'Error', __('Los filtros no arrojaron ningún resultado'), array( 'status' => 404 ) );
		}
	}
	/* ******************************************************** */
	function glazzol_rest_get_single(WP_REST_Request $request){
		$idproduct = (int) $request->get_param('idproduct'); // si mandasen letras, el rest de wp los bloquearía ;)
		$post = get_post($idproduct);
		if(!is_null($post->ID)){
			$producto = wc_get_product( $post->ID );
			$glass = array();
			$terms = get_the_terms( $post->ID, 'product_cat' );
			$thumbnail_id = get_woocommerce_term_meta( $terms[0]->term_id, 'thumbnail_id', true );
			$image = wp_get_attachment_url( $thumbnail_id );
			$glass['brand_image'] = $image;
			$glass['favorite'] = true;
			$images_array = array();
			if( ! empty( get_post_meta( $post->ID, '_app', true) )){
				$images = explode( ',', get_post_meta( $post->ID, '_app', true) );
				foreach($images as $img){
					$images_array['src'][] = wp_get_attachment_url( $img );
				}
			}
			$glass['id'] = $post->ID;
			$glass['name'] = $post->post_title;
			$glass['images']=$images_array;
			$glass['price'] = ceil($producto->get_sale_price());
			$glass['regularPrice'] = ceil($producto->get_regular_price());
			$glass['promotionalDateFinish'] = $producto->get_date_on_sale_to();
			$glass['available'] = (get_post_meta($post->ID, '_stock', true)>0?true:false);
			$glass['tryable'] 	= (get_post_meta( $post->ID, '_probador', true)!=false?true:false);
			//$additionalInfo = get_the_terms($id, 'pa_tamano-lente');
			$glass['additionalInfo'] = $additionalInfo['name'];
			/* ************************************** */
			$tamanoLente = get_the_terms($post->ID, 'pa_tamano-lente');
			$generoLente = get_the_terms($post->ID, 'pa_genero-lente');
			$graduableLente = get_the_terms($post->ID, 'pa_graduable-lente');
			$glass['characteristics'] = array('gender'=>$generoLente[0]->term_id,'faceSize'=>$tamanoLente[0]->term_id,'polarizable'=>$graduableLente[0]->term_id);
			$glass['UPC'] = get_post_meta($post->ID, '_UPC',true);
			return rest_ensure_response($glass);
		}
		else{
			return new WP_Error( 'Error', __('Producto  no encontrado'), array( 'status' => 404 ) );
		}
	}
	/* ********************************************************* */

	function glazzol_rest_get_shopping(WP_REST_Request $request){
		global $woocommerce;
		global $wpdb;
		switch($_SERVER['REQUEST_METHOD']){
			case "GET":
				if($_REQUEST['id']!=""){
					$productos = explode("|",$_REQUEST['id']);
					$shopping = [];
					foreach($productos as $item){
						$post = get_post($item);
						$producto =  wc_get_product( $post->ID );
						$stock = get_post_meta($item,'_stock');
						array_push($shopping,array('id'=>$item,'price'=>ceil($producto->get_sale_price()),'stock'=>$stock[0],'backorder'=>get_post_meta($producto,'_backorders', true)));	
					}
					$regresa = rest_ensure_response(array('result'=>'success',"models"=>$shopping));
					$regresa->set_status(200);
				}
				else{
					$regresa = rest_ensure_response(array('result'=>'error',"message"=>"no hay id's que consultar"));
					$regresa->set_status(404);
				}
				return $regresa;
			break;
			case "POST":
				
				$json = file_get_contents('php://input');
				$json_string = stripslashes($json);
				$data = json_decode($json_string, true);
				//$keys = array_keys($data);
 				$elementCount  = count($data);
 				//var_dump($elementCount)
				if(!is_null($data)) {
					$basicInfoJSON = [];
					$shippingInfoJSON = [];
					$billInfoJSON = [];
					$purchaseInfoJSON = [];
					$detailPurchaseInfoJSON = [];
					$counter = 1;
					$couponValue='';
					$couponAplicable='';
					//$in = array_keys($keys);
					//$shopping = array('message'=>'OK','data_recived'=>$keys,'inside'=>$in);
					foreach($data as $header){
						foreach($header as $key => $val){
							switch($counter){
								case 1:
									$basicInfoJSON[] = $val;
								break;
								case 2:
									$shippingInfoJSON[] = $val;
								break;
								case 3:
									$billInfoJSON[] = $val;
								break;
								case 4:
									$purchaseInfoJSON[] = $val;
								break;
								case 5:
									$detailPurchaseInfoJSON[] = $val;
								break;
							}
						}
						$counter++;
					}
					// Con esto creamos la orden de compra
					$order = wc_create_order();

					if($detailPurchaseInfoJSON[0]!= "" ){//evaluamos si el valor del cupon tiene algo.
						$qrGetCoupon = $wpdb->get_row("SELECT ID FROM ".$wpdb->prefix."posts WHERE post_title = '".$detailPurchaseInfoJSON[0]."'");
						$dueDate = get_post_meta($qrGetCoupon->ID, 'expiry_date', true);
						$coupon_amount = get_post_meta($qrGetCoupon->ID,'coupon_amount',true);//coupon_amount
						$discount_type = get_post_meta($qrGetCoupon->ID,'discount_type',true);//discount_type
						$order->add_coupon($detailPurchaseInfoJSON[0], WC()->cart->get_coupon_discount_amount($detailPurchaseInfoJSON[0]));
					}
					
					foreach ($purchaseInfoJSON as $productId => $productOrdered) {
						$id = $productOrdered['id'];
						$cantidad = (int)$productOrdered['quantity'];
						
						if($detailPurchaseInfoJSON[0]!= "" ){
							$product_to_add = get_product( $id );
							$sale_price = ceil($product_to_add->get_sale_price());
							$final_price = ceil($sale_price*((100-$coupon_amount)/100));
							$price_params = array( 'totals' => array( 'subtotal' => $sale_price, 'total' => $final_price ) );
							$order->add_product( get_product($id), $cantidad, $price_params);
						}
						else{
							$product_to_add = get_product( $id );
							$sale_price = ceil($product_to_add->get_sale_price());
							$final_price = ceil($sale_price);
							$price_params = array( 'totals' => array( 'subtotal' => $sale_price, 'total' => $final_price ) );
							$order->add_product( get_product($id), $cantidad);
						}

					}
					$addressShip = array(
						'first_name' => $basicInfoJSON[1],
						'last_name'  => $basicInfoJSON[2],
						'company'    => '',
						'email'      => $basicInfoJSON[3],
						'phone'      => $basicInfoJSON[4],
						'address_1'  => $shippingInfoJSON[0],
						'address_2'  => $shippingInfoJSON[1],
						'city'       => $shippingInfoJSON[2],
						'state'      => $shippingInfoJSON[3],
						'postcode'   => $shippingInfoJSON[4],
						'country'    => 'MX'
					);
					$addressBill = array(
						'first_name' => $basicInfoJSON[1],
						'last_name'  => $basicInfoJSON[2],
						'rfc'        => $billInfoJSON[2],
						'email'      => $basicInfoJSON[3],
						'phone'      => $billInfoJSON[1],
						'address_1'  => $billInfoJSON[3],
						'address_2'  => $billInfoJSON[4],
						'city'       => $billInfoJSON[5],
						'state'      => $billInfoJSON[6],
						'postcode'   => $billInfoJSON[7],
						'country'    => 'MX'
					);
					
					if($billInfoJSON[3]!=""){
						$order->set_address( $addressBill,'billing' );
					}
					else{
						$order->set_address( $addressShip,'billing' );
					}
					$order->set_address( $addressShip,'shipping' );
					// se calcula el total del carro de compra
					$order->calculate_totals();
					$envio = explode("|",$detailPurchaseInfoJSON[1]);
					//var_dump($envio[1]);

					$shipping = new WC_Shipping_Rate('',$envio[0],$envio[1]);
					$order->add_shipping($shipping);
					$order->calculate_totals();
					// mandamos llamar la función dependiendo del método de pago
					$response = false;
					// id->"paypal"
					// id->"conektacard"
					// id->"conektaoxxopay"
					// id->"conektaspei"
					if($basicInfoJSON[0] != ""){
						update_post_meta( $order->id, '_customer_user', $basicInfoJSON[0]);
					}
					if($detailPurchaseInfoJSON[3] == "conektacard"){
						$response = conektaGatewayFunct($order->id,$detailPurchaseInfoJSON[4]);
						if($response == true) {
							# code...
							$order->update_status("processing");
							update_post_meta( $order->id, '_payment_method', 	$detailPurchaseInfoJSON[3]);
							update_post_meta( $order->id, '_payment_method_title', $detailPurchaseInfoJSON[3]);
							$regresa = rest_ensure_response(array('result'=>'Éxito','message'=>'Compra registrada',"paymethod"=>"conektacard"));
							$regresa->set_status(201);
						}
						else{
							$order->update_status("cancelled", 'Imported order', TRUE);
							$regresa = rest_ensure_response(array('result'=>'Error','message'=>'No se registra '));
							$regresa->set_status(404);
						}
					}
					if($detailPurchaseInfoJSON[3] == "paypal"){
						// id: ppec_paypal
						$available_gateways = WC()->payment_gateways->get_available_payment_gateways();
						$response = $available_gateways[ 'paypal' ]->process_payment( $order->id );
						// actualizar status de la compra
						update_post_meta( $order->id, '_payment_method', 	$detailPurchaseInfoJSON[3]);
						update_post_meta( $order->id, '_payment_method_title', $detailPurchaseInfoJSON[3]);
						$order->update_status("on-hold");
						// respondemos a la app
						$regresa = rest_ensure_response(array('result'=>'Éxito','message'=>'Compra Pasada a PayPal','redirect'=>$response['redirect'],"paymethod"=>"paypal"));
						$regresa->set_status(201);
						
						//var_dump();
					}

				}
				else{
					
					$regresa = rest_ensure_response(array('status'=>'Error',"mensaje"=>"La compra no pudo ser procesada"));
					$regresa->set_status(400);
				}
				return $regresa;
				
			break;
		}
	}
	/* ********************************************************* */
	function glazzol_rest_get_coupons(WP_REST_Request $request){
		$cuponValid= [];
		$cupon = $_REQUEST['coupon'];
		$items = explode("|", $_REQUEST['id']);

		$cantidad = explode("|", $_REQUEST['quantity']);
		global $wpdb;
		global $woocommerce;
		//	
		$qrGetCoupon = $wpdb->get_row("SELECT ID FROM ".$wpdb->prefix."posts WHERE post_title = '".$cupon."'");
		$response = "";
		// VERIFICAMOS SI EXISTE O NO UN CUPON CON EL NOMBRE DADO
		if(!empty($qrGetCoupon)) {
			//$cuponValid['id'] = $qrGetCoupon->ID;
			$dueDate = get_post_meta($qrGetCoupon->ID, 'expiry_date', true);
			// COMPARAMOS LA VIGENCIA DEL CUPON ANTES DE RESPONDER
			if(strtotime(date('Y-m-d')) <= strtotime($dueDate)){
				// ENSAMBLO ARREGLO ANTES DE ENVIARLO
				$typeDiscount = get_post_meta($qrGetCoupon->ID, 'discount_type', true);
				$value = get_post_meta($qrGetCoupon->ID, 'coupon_amount', true);
				$expiry = $dueDate;
				$montoMinimo = get_post_meta($qrGetCoupon->ID, 'minimum_amount', true);
				$montoMaximo = get_post_meta($qrGetCoupon->ID, 'maximum_amount', true);
				$limiteUso = get_post_meta($qrGetCoupon->ID, 'usage_limit', true);
				$productId = get_post_meta($qrGetCoupon->ID, 'product_ids', true);
				$categoriasAceptadas = get_post_meta($qrGetCoupon->ID, 'product_categories', true);
				$categoriasNoAceptadas = get_post_meta($qrGetCoupon->ID, 'exclude_product_categories', true);
				$contador = 0;
				$montoAplicable = 0;
				$montoExcento = 0;
				//var_dump($_REQUEST['quantity']);
				foreach ($items as &$valor) {
					//$valor = $valor * 2;
					$post = get_post($valor);
					//var_dump($post);
					$producto =  wc_get_product( $post->ID );
					$nombreCat = get_the_terms($post->ID,'product_cat');
					$nombreCat[0]->term_id;
					if(!empty($categoriasAceptadas)){
						// var_dump($categoriasAceptadas);
						// var_dump("entra  en que hay categorías q si se aceptan");
						if (in_array($nombreCat[0]->term_id, $categoriasAceptadas)){
							$montoAplicable+= $montoAplicable + ($producto->get_sale_price()) * $cantidad[$contador];
						}
						else{
							$montoExcento+= $montoExcento + ($producto->get_sale_price()) * $cantidad[$contador];
						}
					}
					else if(!empty($categoriasNoAceptadas)){
						if (in_array($nombreCat[0]->term_id, $categoriasNoAceptadas)){
							$montoExcento+= ($producto->get_sale_price()) * $cantidad[$contador];
						}
						else{
							$montoAplicable+= ($producto->get_sale_price()) * $cantidad[$contador];
						}
					}
					else{
						$montoAplicable+= ($producto->get_sale_price()) * $cantidad[$contador];
					}
					$contador++;
				}
				if(($montoAplicable + $montoExcento) > $montoMinimo){
					if($montoMaximo!=""){
						if(($montoAplicable + $montoExcento) > $montoMaximo){
							$totalAEnviar = ($montoAplicable + $montoExcento);
						}
						else{
							switch ($typeDiscount) {
								case 'fixed_cart':
									$total = ($montoAplicable + $montoExcento) - $value;
								break;
								case 'percent':
									$total = $montoExcento + (1 - ($value/100)) * $montoAplicable;
									//$total = ($montoAplicable + $montoExcento) - $value;
								break;
								default:
									$total = $montoExcento + (1 - ($value/100)) * $montoAplicable;
								break;
							}
						}
					}
					else{
						switch ($typeDiscount) {
							case 'fixed_cart':
								$total = ($montoAplicable + $montoExcento) - $value;
							break;
							case 'percent':
								$total = $montoExcento + (1 - ($value/100)) * $montoAplicable;

							break;
							default:
								$total = $montoExcento + (1 - ($value/100)) * $montoAplicable;

							break;
						}
					}
				}
				else{
					$total = ($montoAplicable + $montoExcento);
				}
				$response = rest_ensure_response(array('result'=>'success','total'=>ceil($total)));
				$response->set_status(200);
			}
			else{
				$response = rest_ensure_response(array('result'=>'error','message'=>'cupón no válido'));
				$response->set_status(404);
			}
		}
		else{
			array_push($cuponValid,array('response'=>'Not found','Message'=>'cupón no encontrado'));
			$response = rest_ensure_response(array('result'=>'error','message'=>'cupón no encontrado'));
			$response->set_status(400);
		}
		return $response;
	}
	/* ********************************************************* */
	function glazzol_rest_get_shippingmethods(WP_REST_Request $request){
		global $wpdb;
		global $woocommerce;
		$methodsNShipArr = ['FedEx Envío Gratis'=>'wbs:4f9afd0b_fedex_env_o_gratis','FedEx Envío Express"'=>'wbs:06b660e1_fedex_env_o_express','FedEx'=>'wbs:f2d4a36f_fedex'];
		/*
			wbs:4f9afd0b_fedex_env_o_gratis
			wbs:06b660e1_fedex_env_o_express
			wbs:4a390dc2_env_o_gratuito_por_boss_mandados
			wbs:f2d4a36f_fedex
			shipping_method_0_wbs06b660e1_fedex_env_o_express
			shipping_method_0_wbs4f9afd0b_fedex_env_o_gratis	
		*/
		$shipping_methods = $woocommerce->shipping->load_shipping_methods();    
		$var1 = $shipping_methods['wbs']->settings['rules'];
		$payMethodsArr = array();
		$contador = 0;
		foreach ($var1 as $item) {
			$tempPayMethods = [];
			foreach($item as $key => $value){
				switch ($key) {
					case 'meta':
						// echo $value['title']."---";
						$titulo = $value['title'];
						$activo = $value['enabled'];
					break;

					case 'conditions':
						// echo $value['subtotal']['range']['min']."---";
						$minimo = $value['subtotal']['range']['min'];
					break;

					case 'charges':
						// echo $value['base']."---";
						switch($titulo){
							case 'RedPack':
								$cargo = 130;
								$shippingArray = 'a:1:{i:0;s:20:"wbs:04e33b71_redpack";}';
							break;
							case 'RedPack Express':
								$cargo = 180;
								$shippingArray = 'a:1:{i:0;s:28:"wbs:0e593a3d_redpack_express";}';
							break;
							default:
								$cargo = $value['base'];
								$shippingArray = 'a:1:{i:0;s:39:"wbs:dd991c80_env_o_gratuito_por_redpack";}';
							break;
							//
						}
					break;

					default:
						# code...
					break;
				}
				if($activo){
					$payMethodsArr[$contador] = array("titulo"=>$titulo,"minimo"=>$minimo,"cargo"=>$cargo,"shipMethod"=>$shipping_methods);
					// $payMethodsArr[$contador] = array("titulo"=>$titulo,"minimo"=>$minimo,"cargo"=>$cargo,"shipMethod"=>$shippingArray);
				}
			}
			$contador++;
		}
		return rest_ensure_response($payMethodsArr);
		//return rest_ensure_response($ship);
	}
	/* ********************************************************* */
	function glazzol_rest_get_paygmethods(WP_REST_Request $request){
		global $wpdb;
		global $woocommerce;
		$available_gateways = WC()->payment_gateways->get_available_payment_gateways();
    //$result = $available_gateways[ 'ideal' ]->process_payment( $order->id );
		return rest_ensure_response($available_gateways);
	}