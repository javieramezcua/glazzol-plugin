<?php

	function glazzol_rest_register_users(WP_REST_Request $request){
		global $wpdb;
		/* antes de insertar user, validamos que exista */
		$json = file_get_contents('php://input');
		$json_string = stripslashes($json);
		$data = json_decode($json_string);
		//return rest_ensure_response(array('result'=>'OK','me enviaste'=>$json));
		
		if(isset($data->mail) && isset($data->pass) ){
			if($data->mail != "" && $data->pass != ""){
				$qryPrevia  = "SELECT user_email,user_pass FROM ".$wpdb->prefix."users WHERE user_email ='".$data->mail."'";
				$rsGet = $wpdb->get_row($qryPrevia);
				
				if($wpdb->num_rows < 1){
					$getNicename = explode("@",$data->mail);
					$qryInsBasic = "INSERT INTO ".$wpdb->prefix."users";
					$qryInsBasic.= " (user_login,user_pass,user_nicename,user_email,user_registered,display_name)";
					$qryInsBasic.= " VALUES ( ";
					$qryInsBasic.= "'".$getNicename[0]."','".wp_hash_password($data->pass)."','".$getNicename[0]."','".trim($data->mail)."',NOW(),'".$getNicename[0]."')";
					$wpdb->query($qryInsBasic);
					$idInsertado =  $wpdb->insert_id;
					$qryInsMetaValues = "INSERT INTO ".$wpdb->prefix."usermeta ";
					$qryInsMetaValues.= " (user_id,meta_key,meta_value) VALUES ";
					$qryInsMetaValues.= " (".$idInsertado.",'nickname','".$getNicename[0]."')";
					$qryInsMetaValues.= " ,(".$idInsertado.",'first_name','".$data->name."')";
					$qryInsMetaValues.= " ,(".$idInsertado.",'last_name','')";
					$qryInsMetaValues.= " ,(".$idInsertado.",'description','')";
					$qryInsMetaValues.= " ,(".$idInsertado.",'rich_editing','true')";
					$qryInsMetaValues.= " ,(".$idInsertado.",'comment_shortcuts','false')";
					$qryInsMetaValues.= " ,(".$idInsertado.",'admin_color','fresh')";
					$qryInsMetaValues.= " ,(".$idInsertado.",'use_ssl','0')";
					$qryInsMetaValues.= " ,(".$idInsertado.",'show_admin_bar_front','true')";
					$qryInsMetaValues.= " ,(".$idInsertado.",'locale','')";
					$qryInsMetaValues.= " ,(".$idInsertado.",'wpvl_capabilities','a:1:{s:8:\"customer\";b:1;}')";
					$qryInsMetaValues.= " ,(".$idInsertado.",'wpvl_user_level','0')";
					$qryInsMetaValues.= " ,(".$idInsertado.",'_order_count','0')";
					$qryInsMetaValues.= " ,(".$idInsertado.",'_money_spent','0')";
					$qryInsMetaValues.= " ,(".$idInsertado.",'session_tokens','')";
					$qryInsMetaValues.= " ,(".$idInsertado.",'last_update','')";
					$qryInsMetaValues.= " ,(".$idInsertado.",'shipping_first_name','')";
					$qryInsMetaValues.= " ,(".$idInsertado.",'shipping_last_name','')";
					$qryInsMetaValues.= " ,(".$idInsertado.",'shipping_company','')";
					$qryInsMetaValues.= " ,(".$idInsertado.",'shipping_country','')";
					$qryInsMetaValues.= " ,(".$idInsertado.",'shipping_address_1','')";
					$qryInsMetaValues.= " ,(".$idInsertado.",'shipping_address_2','')";
					$qryInsMetaValues.= " ,(".$idInsertado.",'shipping_city','')";
					$qryInsMetaValues.= " ,(".$idInsertado.",'shipping_state','')";
					$qryInsMetaValues.= " ,(".$idInsertado.",'shipping_postcode','')";
					$qryInsMetaValues.= " ,(".$idInsertado.",'billing_first_name','')";
					$qryInsMetaValues.= " ,(".$idInsertado.",'billing_last_name','')";
					$qryInsMetaValues.= " ,(".$idInsertado.",'billing_company','')";
					$qryInsMetaValues.= " ,(".$idInsertado.",'billing_country','')";
					$qryInsMetaValues.= " ,(".$idInsertado.",'billing_address_1','')";
					$qryInsMetaValues.= " ,(".$idInsertado.",'billing_address_2','')";
					$qryInsMetaValues.= " ,(".$idInsertado.",'billing_city','')";
					$qryInsMetaValues.= " ,(".$idInsertado.",'billing_state','')";
					$qryInsMetaValues.= " ,(".$idInsertado.",'billing_postcode','')";
					$qryInsMetaValues.= " ,(".$idInsertado.",'billing_phone','')";
					$qryInsMetaValues.= " ,(".$idInsertado.",'billing_email','".trim($data->mail)."')";
					$wpdb->query($qryInsMetaValues);
					$idInsertado2 =  $wpdb->insert_id;
					$regresa = rest_ensure_response(array('result'=>'OK','message'=>'Usuario registrado correctamente','idUser'=>$idInsertado));
					$regresa->set_status(201);
					return $regresa;			
				}
				else{
					return new WP_Error( 'Error', __('Ya existe un correo electrónico registrado'), array( 'status' => 400 ) );
				}
			}
			else{
				return new WP_Error( 'Not posible', __('los valores no se encuentran'), array( 'status' => 400 ) );
			}
		}
		else{
			return new WP_Error( 'Not posible', __('El JSON no cuenta con los datos necesarios'), array( 'status' => 400 ) );
		}
		
	}
	/* ********************************************************* */
	function glazzol_rest_login_users(WP_REST_Request $request){
		$mail = (string) $request->get_param('mail'); // 
		$pass = (string) $request->get_param('pass'); // 
		/* ******************************************* */
		if(isset($mail) && isset($pass)){
			$datosUsuario = get_user_by('email',$mail);
			$nombreUsuario = get_user_meta($datosUsuario->ID,'first_name',true);
			$passSaved = $datosUsuario->user_pass;
			if($datosUsuario != false){
				$plain_password = $pass;
				if( wp_check_password( $plain_password , $datosUsuario->user_pass, $datosUsuario ->ID)) {
					$regresa  = rest_ensure_response(array('result'=>'success','message'=>'correcto','idUser'=>$datosUsuario->ID,'nombre'=>$nombreUsuario));
					$regresa->set_status(200);
				} else {
					$regresa  = rest_ensure_response(array('result'=>'Error','message'=>'No tienes acceso'));
					$regresa->set_status(400);
				}
			}
			else{
				$regresa  = rest_ensure_response(array('result'=>'Error','message'=>'No existe cuenta'));
				$regresa->set_status(400);		
			}
		}
		else{
			$regresa  = rest_ensure_response(array('result'=>'Error','message'=>'parametros erroneos'));
			$regresa->set_status(400);
		}
		return $regresa;
	}
	/* ********************************************************* */
	function glazzol_reset_user_pass(WP_REST_Request $request){
		$mail = (string) $request->get_param('mail'); // 
		//$pass = (string) $request->get_param('pass'); // 
		/* ******************************************* */
		if(isset($mail) ){
			$userData = get_user_by( "email",$mail);
			$user_login = $userData->user_login;
			$user_email = $userData->user_email;
			$key = get_password_reset_key( $userData );

			$subject = "Recuperación de contraseña Glazzol";
			$headers = "Reply-To: Atención a Clientes Glazzol <hola@glazzol.com>";
			$message = __('Someone requested that the password be reset for the following account:') . "\r\n\r\n";
			$message .= network_home_url( '/' ) . "\r\n\r\n";
			$message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
			$message .= __('If this was a mistake, just ignore this email and nothing will happen.') . "\r\n\r\n";
			$message .= __('To reset your password, visit the following address:') . "\r\n\r\n";
			$message .= network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login');
			if(wp_mail( $userData->user_email, $subject, $message, $headers)){
				$regresa = rest_ensure_response(array('result'=>'Éxito','message'=>'Petición de contacto registrada'));
				$regresa->set_status(200);
			}
			else{
				
				$regresa = rest_ensure_response(array('status'=>'Error',"mensaje"=>"Petición de recuperación no registrada"));
				$regresa->set_status(400);
			}
		}
		else{
			$regresa  = rest_ensure_response(array('result'=>'Error','message'=>'parametros erroneos'));
			$regresa->set_status(400);
		}
		return $regresa; 
	}
	/* ********************************************************* */
	function glazzol_rest_get_users_purchases(WP_REST_Request $request){
		global $wpdb;
		global $woocommerce;
		$regresa = "";
		$catalog = array(
			'status' =>array()
			,'purchases'=> array()
		);
		$idUser = (int) $request->get_param('iduser'); // si mandasen letras, el rest de wp los bloquearía ;)
		$user_info = get_userdata($idUser);
		if($user_info->ID!=null){
			$customer_orders = get_posts( apply_filters( 'woocommerce_my_account_my_orders_query', array(
				'numberposts' => $order_count,
				'meta_key'    => '_customer_user',
				'meta_value'  => $user_info->ID,//get_current_user_id()
				'post_type'   => wc_get_order_types( 'view-orders' ),
				'post_status' => array_keys( wc_get_order_statuses() ),
			) ) );
			
			foreach ( $customer_orders as $customer_order ) {
				//foreach ( $customer_orders as $customer_order ){
				$order = wc_get_order( $customer_order );
				// *********************************************
				$orden['usuario'] =  $user_info->ID;
				$orden['itemscomprados'] = $item_count = $order->get_item_count();
				$orden['numero_orden'] 	= $order->get_order_number();
				$orden['totalcompra'] 	= ceil($order->get_total());
				$orden['envio']			= $order->get_shipping_method();
				$order_data 			= $order->get_data();
				$orden['costoenvio'] 	= ceil($order_data['shipping_total']);
				// *********************************************
				$product = "";
				$prodArr = "";
				foreach ($order->get_items() as $item_key => $item_values):
					## Using WC_Order_Item methods ##
					$item_id = $item_values->get_id();
					## Using WC_Order_Item_Product methods ##
					$item_data 			= $item_values->get_data();
					$product["item"] 	= $item_data['name'];
					$product_id 		= $item_data['product_id'];
					$product["quantity"]= $item_data['quantity'];
					$product["price"]	= ceil($item_data['total']);				
					$line_total 		+= ceil($item_data['total']);
					// obtenemos imagen
					if( ! empty( get_post_meta( $item_data['product_id'], '_app', true) )){
						$images = explode( ',', get_post_meta( $item_data['product_id'], '_app', true) );
						foreach($images as $img){
							$images_array[]['src']= wp_get_attachment_url( $img );
						}
					}
					$product["img"]	= $images_array[0]['src']= wp_get_attachment_url( $img );
					$prodArr[] =  $product;
				endforeach;
				$orden['items'] 		= $prodArr;
				$orden['fecha_compra'] 	= wc_format_datetime( $order->get_date_created() );
				$orden['estatus'] 		= wc_get_order_status_name( $order->get_status() );
				$orden["name"] 			= $order_data['billing']['first_name'];
				$orden["last_name"] 	= $order_data['billing']['last_name'];
				$orden["address_1"] 	= $order_data['shipping']['address_1'];
				$orden["address_2"] 	= $order_data['shipping']['address_2'];
				$orden["city"] 			= $order_data['shipping']['city'];
				$orden["state"] 		= $order_data['shipping']['state'];
				$orden["postcode"] 		= $order_data['shipping']['postcode'];
				$orden["country"] 		= $order_data['shipping']['country'];
				$orden["phone"] 		= $order_data['billing']['phone'];
				$orden["mail"] 			= $order_data['billing']['email'];

				$catalog['status']  	= "OK";
				
				$catalog['purchases'][] = $orden;
				// *********************************************
			}
			$regresa = rest_ensure_response($catalog);
			$regresa->set_status(200);
		}
		else{
			$regresa = rest_ensure_response(array('status'=>'Error','mensaje'=>'No tiene compras'));
			$regresa->set_status(404);
		}
		
		return $regresa;
	}
	/* ***************************************************************** */
	function glazzol_rest_get_users_purchases_single(WP_REST_Request $request){
		global $wpdb;
		global $woocommerce;
		$regresa = "";
		$catalog = array(
			'status' =>array()
			,'purchases'=> array()
		);
		$idUser = (int) $request->get_param('iduser'); // si mandasen letras, el rest de wp los bloquearía ;)
		$purchase_id = (int) $request->get_param('purchase_id'); // si mandasen letras, el rest de wp los bloquearía ;)
		$user_info = get_userdata($idUser);
		if($user_info->ID!=null){
			$order = wc_get_order($purchase_id);
			foreach ($order->get_items() as $item_key => $item_values):
				## Using WC_Order_Item methods ##
				$item_id = $item_values->get_id();
				## Using WC_Order_Item_Product methods ##
				$item_data 			= $item_values->get_data();
				$product[]["item"] 	= $item_data['name'];
				$product_id 		= $item_data['product_id'];
				$product[]["quantity"]= $item_data['quantity'];
				$product[]["price"]	= $item_data['total'];				
				$line_total 		+= $item_data['total'];
				// obtenemos imagen
				if( ! empty( get_post_meta( $item_data['product_id'], '_app', true) )){
					$images = explode( ',', get_post_meta( $item_data['product_id'], '_app', true) );
					foreach($images as $img){
						$images_array[]['src']= wp_get_attachment_url( $img );
					}
				}
				$product[]["img"]		= $images_array[0]['src']= wp_get_attachment_url( $img );
			endforeach;
			$order_data 			= $order->get_data();
			$direccion["name"] 		= $order_data['billing']['first_name'];
			$direccion["last_name"] = $order_data['billing']['last_name'];
			$direccion["address_1"] = $order_data['shipping']['address_1'];
			$direccion["address_2"] = $order_data['shipping']['address_2'];
			$direccion["city"] 		= $order_data['shipping']['city'];
			$direccion["state"] 	= $order_data['shipping']['state'];
			$direccion["postcode"] 	= $order_data['shipping']['postcode'];
			$direccion["country"] 	= $order_data['shipping']['country'];
			$direccion["phone"] 	= $order_data['billing']['phone'];
			$direccion["mail"] 		= $order_data['billing']['email'];
			$direccion['costoenvio'] = $order_data['shipping_total'];
			//$address = $order->get_billing_address();
			$regresa = rest_ensure_response(array('result'=>'Success',"productos"=>$product,"total"=>$line_total,"shippingTo"=>$direccion));
			$regresa->set_status(200);
		}
		else{
			$regresa = rest_ensure_response(array('status'=>'Error','mensaje'=>'No tiene compras'));
			$regresa->set_status(404);
		}
		return $regresa;
	}
	/* ********************************************************* */
	function glazzol_rest_users_avatars(WP_REST_Request $request){
		global $wpdb;
		$idUser = (int) $request->get_param('iduser');
		$regresa  = "";
		switch($_SERVER['REQUEST_METHOD']){
			case "POST":
				$json = file_get_contents('php://input');
				$json_string = stripslashes($json);
				$data = json_decode($json_string);
				$val1 = json_encode($data->avatar);
				$val2 = json_encode($data->landmark);
				// $qryInsertAvatar = "INSERT INTO tr_users (user_id,avatar,landmarks,created_at) VALUES ";
				// $qryInsertAvatar.= " (".$idUser.",'".$val1."','".$val2."',NOW())";
				$qry = "SELECT meta_value FROM ".$wpdb->prefix."usermeta WHERE user_id = ".$idUser." AND meta_key IN ('_avatar','_landmarks')  ";
				$rsGetAvatar = $wpdb->get_results($qry);
				if($rsGetAvatar==null){
					$qryInsertAvatar = "INSERT INTO ".$wpdb->prefix."usermeta (user_id,meta_key,meta_value) VALUES ";
					$qryInsertAvatar.= " (".$idUser.",'_avatar','".$val1."')";
					$qryInsertAvatar.= " ,(".$idUser.",'_landmarks','".$val2."')";
					$resultado = $wpdb->query($qryInsertAvatar);
					if($resultado == true){
						$regresa = rest_ensure_response(array('status'=>'OK',"mensaje"=>"Avatar registrado exitosamente"));
						$regresa->set_status(201);
					}
					else{
						$regresa = rest_ensure_response(array('status'=>'Error',"mensaje"=>"Error en el registro del avatar"));
						$regresa->set_status(400);
					}
				}
				else{
					$regresa = rest_ensure_response(array('status'=>'Error',"mensaje"=>"Ya existe un registro del avatar asignado al usuario"));
						$regresa->set_status(400);
				}
			break;

			case "GET":
				$qry = "SELECT meta_value FROM ".$wpdb->prefix."usermeta WHERE user_id = ".$idUser." AND meta_key IN ('_avatar','_landmarks')  ORDER BY meta_key ";
				$rsGetAvatar = $wpdb->get_results($qry);
				if($rsGetAvatar!=null){
					$avatar = "";
					$landmarks = "";
					$contador  = 0;
					foreach($rsGetAvatar as $row){
						switch($contador){
							case 0:
								$avatar = $row->meta_value;
							break;
							case 1:
								$landmarks = $row->meta_value;
							break;
						}
						$obj=$row;
						$contador++;
					}
					$regresa = rest_ensure_response(array('status'=>'OK',"user_avatar"=>json_decode($avatar),"user_landmarks"=>json_decode($landmarks)));
					$regresa->set_status(200);
				}

				else{
					$regresa = rest_ensure_response(array('result'=>'error',"message"=>"El id de usuario no existe"));
					$regresa->set_status(404);
				}
			break;

			case "DELETE":
				$qry = "DELETE FROM ".$wpdb->prefix."usermeta WHERE user_id = ".$idUser." AND meta_key IN ('_avatar','_landmarks')  ";
				$resultado = $wpdb->query($qry);
				if($resultado == true){
					$regresa = rest_ensure_response(array('status'=>'OK',"mensaje"=>"Avatar eliminado exitosamente"));
					$regresa->set_status(200);
				}
				else{
					$regresa = rest_ensure_response(array('status'=>'Error',"mensaje"=>"El id de usuario no existe o el id de avatar no existe"));
					$regresa->set_status(400);
				}
			break;

			default:
				$regresa  = rest_ensure_response(array('result'=>'error',"message"=>"Acceso denegado"));
				$regresa->set_status(403);
			break;

		}
		return $regresa;
	}
	/* ****************************************************** */
	function glazzol_rest_get_users_favs(WP_REST_Request $request){
		global $wpdb;
		global $woocomerce;
		$idUser = (int) $request->get_param('iduser');
		if($request->get_param('idproduct')!=null){$idprod = (int) $request->get_param('idproduct');}
		$keys = [];
		$regresa  = "";
		switch($_SERVER['REQUEST_METHOD']){
			case "POST":
				$json = file_get_contents('php://input');
				$json_string = stripslashes($json);
				$data = json_decode($json_string);
				$qryInsertFav = "INSERT INTO tr_favorites (sku,user_id) VALUES ";
				$qryInsertFav.= " (".$data->id.",".$idUser.")";
				$resultado = $wpdb->query($qryInsertFav);
				if($resultado == true){
					$regresa = rest_ensure_response(array('status'=>'OK',"mensaje"=>"Item Favorito registrado exitosamente"));
					$regresa->set_status(201);
				}
				else{
					$regresa = rest_ensure_response(array('status'=>'Error',"mensaje"=>"Error en el registro del Item Favorito"));
					$regresa->set_status(400);
				}
			break;
			case "GET":
				$qry = "SELECT sku FROM tr_favorites WHERE user_id = ".$idUser;
				$rsGetFav = $wpdb->get_results($qry);
				$catalog = array(
					'status'=>  array()
					,'favorites'=> array()
				);
				if ( $rsGetFav ){
					foreach ( $rsGetFav as $favorito ){
						$post = get_post($favorito->sku);
						$producto = wc_get_product( $post->ID );
						$images_array = array();
						if( ! empty( get_post_meta( $post->ID, '_app', true) )){
							$images = explode( ',', get_post_meta( $post->ID, '_app', true) );
							foreach($images as $img){
								$images_array[]['src']= wp_get_attachment_url( $img );
							}
						}
						$nombreCat = get_the_terms($post->ID,'product_cat');
						$thumb_id = get_woocommerce_term_meta( $nombreCat[0]->term_id, 'thumbnail_id', true ); 
						$glasses['id'] = $post->ID;
						$glasses['glassesImage'] = $images_array;
						$glasses['brand_image'] = wp_get_attachment_url(  $thumb_id );
						$glasses['favorite'] = true;
						$glasses['name']  = get_the_title($post->ID);
						$glasses['price'] = ceil($producto->get_sale_price());
						$glasses['regularPrice'] = ceil($producto->get_regular_price());
						$glasses['available'] = (get_post_meta($post->ID, '_stock', true)>0?true:false);
						$glasses['tryable'] 	= (get_post_meta($post->ID, '_probador', true)!=false?true:false);
						/* ************************************ */
						$characteristics = [];
						$tamanoLente = get_the_terms($post->ID, 'pa_tamano-lente');
						$generoLente = get_the_terms($post->ID, 'pa_genero-lente');
						$graduableLente = get_the_terms($post->ID, 'pa_graduable-lente');
						array_push($characteristics,array('gender'=>$generoLente[0]->term_id));
						array_push($characteristics,array('faceSize'=>$tamanoLente[0]->term_id));
						array_push($characteristics,array('polarizable'=>$graduableLente[0]->term_id));
						$glasses['characteristics'] = array('gender'=>$generoLente[0]->term_id,'faceSize'=>$tamanoLente[0]->term_id,'polarizable'=>$graduableLente[0]->term_id);

						$catalog['status']="OK";
						if(wp_get_attachment_url($thumb_id) != null){
							$catalog['glasses'][] = $glasses;
						}
					}
					$regresa = rest_ensure_response($catalog);
					$regresa->set_status(200);
				}
				else{
					$regresa = rest_ensure_response(array('status'=>'Error',"mensaje"=>"Error en el registro del Item Favorito"));
					$regresa->set_status(400);
				}
			break;
			case "DELETE":
				if($_REQUEST['id']!=""){
					$productos = explode("|",$_REQUEST['id']);
					// var_dump($_REQUEST['id']);
					$shopping = [];
					foreach($productos as $item){
						$qry = "DELETE FROM tr_favorites WHERE user_id = ".$idUser ." AND sku  = ".$item ;
						$resultado = $wpdb->query($qry);
					}
					if($resultado == true){
						$regresa = rest_ensure_response(array('status'=>'OK',"mensaje"=>"Favorito eliminado"));
						$regresa->set_status(200);
					}
					else{
						// $regresa = rest_ensure_response(array('status'=>'Error',"mensaje"=>"El id de usuario no existe o el id de avatar no existe"));
						$regresa = rest_ensure_response(array('status'=>'Error',"mensaje"=>"El id de usuario no existe o el id de avatar no existe","idRecibido"=>urldecode($idprod)));
						$regresa->set_status(400);
					}
				}
				else{
					$regresa = rest_ensure_response(array('result'=>'error',"message"=>"no hay id's que consultar"));
					$regresa->set_status(404);
				}
				return $regresa;
			break;
			default:
				$regresa  = rest_ensure_response(array('result'=>'error',"message"=>"Acceso denegado"));
				$regresa->set_status(403);
			break;
		}
		return $regresa;
	}
	/* ****************************************************** */
	function glazzol_rest_crud_users_polls(WP_REST_Request $request){
		global $wpdb;
		global $woocomerce;
		$idUser = (int) $request->get_param('iduser');
		if($request->get_param('idpoll')!=null){$idPoll = (int) $request->get_param('idpoll');}
		$keys = [];
		$regresa  = "";
		switch($_SERVER['REQUEST_METHOD']){
			case "POST":
				$json = file_get_contents('php://input');
				$json_string = stripslashes($json);
				$data = json_decode($json_string);
				$models = json_encode($data->glasses);
				$qryInsertFav = "INSERT INTO tr_polls (name,models,user_id,created_at) VALUES ";
				$qryInsertFav.= " ('".$data->name."','".$models."',".$idUser.",NOW())";
				$resultado = $wpdb->query($qryInsertFav);
				//var_dump($qryInsertFav);
				if($resultado == true){
					$regresa = rest_ensure_response(array('status'=>'OK',"mensaje"=>"Item encuesta registrada exitosamente"));
					$regresa->set_status(201);
				}
				else{
					$regresa = rest_ensure_response(array('status'=>'Error',"mensaje"=>"Error en el registro de encuesta"));
					$regresa->set_status(400);
				}
			break;
			case "GET":
				$qry = "SELECT sku FROM tr_polls WHERE user_id = ".$idUser." AND poll_id = ".$idpoll;
				$rsGetFav = $wpdb->get_results($qry);
				$catalog = array(
					'status'=>  array()
					,'favorites'=> array()
				);
				if ( $rsGetFav ){
					foreach ( $rsGetFav as $favorito ){
						$post = get_post($favorito->sku);
						$producto = wc_get_product( $post->ID );
						$images_array = array();
						if( ! empty( get_post_meta( $post->ID, '_app', true) )){
							$images = explode( ',', get_post_meta( $post->ID, '_app', true) );
							foreach($images as $img){
								$images_array[]['src']= wp_get_attachment_url( $img );
							}
						}
						$nombreCat = get_the_terms($post->ID,'product_cat');
						$thumb_id = get_woocommerce_term_meta( $nombreCat[0]->term_id, 'thumbnail_id', true ); 
						$glasses['id'] = $post->ID;
						$glasses['glassesImage'] = $images_array;
						$glasses['brand_image'] = wp_get_attachment_url(  $thumb_id );
						$glasses['favorite'] = true;
						$glasses['name']  = get_the_title($post->ID);
						$glasses['price'] = ceil($producto->get_sale_price());
						$glasses['regularPrice'] = ceil($producto->get_regular_price());
						$glasses['available'] = (get_post_meta($post->ID, '_stock', true)>0?true:false);
						$glasses['tryable'] 	= (get_post_meta($post->ID, '_probador', true)!=false?true:false);
						$glasses['promotionalDateFinish'] = $producto->get_date_on_sale_to(); // fin de promo --> pringin and disc
						/* ************************************ */
						$characteristics = [];
						$tamanoLente = get_the_terms($favorito, 'pa_tamano-lente');
						$generoLente = get_the_terms($favorito, 'pa_genero-lente');
						$graduableLente = get_the_terms($favorito, 'pa_graduable-lente');
						array_push($characteristics,array('gender'=>$generoLente[0]->term_id));
						array_push($characteristics,array('faceSize'=>$tamanoLente[0]->term_id));
						array_push($characteristics,array('polarizable'=>$graduableLente[0]->term_id));
						$glasses['characteristics'] = array('gender'=>$generoLente[0]->term_id,'faceSize'=>$tamanoLente[0]->term_id,'polarizable'=>$graduableLente[0]->term_id);

						$catalog['status']="OK";
						if(wp_get_attachment_url($thumb_id) != null){
							$catalog['glasses'][] = $glasses;
						}
					}
					$regresa = rest_ensure_response($catalog);
					$regresa->set_status(200);
				}
				else{
					$regresa = rest_ensure_response(array('status'=>'Error',"mensaje"=>"Error en el registro del Item Favorito"));
					$regresa->set_status(400);
				}
			break;
			default:
				$regresa  = rest_ensure_response(array('result'=>'error',"message"=>"Acceso denegado"));
				$regresa->set_status(403);
			break;
		}
		return $regresa;
	}
	function glazzol_rest_get_addressesByMail(WP_REST_Request $request){
		$customerMail = (int) $request->get_param('customermail');
		global $wpdb;
		global $woocommerce;
		$regresa = "";
		$catalog = array(
			'status' =>array()
		);
		$idUser = (int) $request->get_param('customermail'); // si mandasen letras, el rest de wp los bloquearía ;)
		$user_info = get_userdata($idUser);
		if($user_info->ID!=null){
			$customer_orders = get_posts( apply_filters( 'woocommerce_my_account_my_orders_query', array(
				'numberposts' => $order_count,
				'meta_key'    => '_customer_user',
				'meta_value'  => $user_info->ID,//get_current_user_id()
				'post_type'   => wc_get_order_types( 'view-orders' ),
				'posts_per_page'=>1,
				'orderby'=>'ID',
                'orderby'=>'DESC',
				'post_status' => array_keys( wc_get_order_statuses() ),
			) ) );
			
			foreach ( $customer_orders as $customer_order ) {
				//foreach ( $customer_orders as $customer_order ){
				$order = wc_get_order( $customer_order );
				// *********************************************
				$orden['usuario'] =  $user_info->ID;
				$order_data 			= $order->get_data();
				// *********************************************
				//$orden['items'] 		= $prodArr;
				//$orden['fecha_compra'] 	= wc_format_datetime( $order->get_date_created() );
				//$orden['estatus'] 		= wc_get_order_status_name( $order->get_status() );
				$orden["first_name"]	= $order_data['billing']['first_name'];
				$orden["last_name"] 	= $order_data['billing']['last_name'];
				$orden["address_1"] 	= $order_data['shipping']['address_1'];
				$orden["address_2"] 	= $order_data['shipping']['address_2'];
				$orden["city"] 			= $order_data['shipping']['city'];
				$orden["state"] 		= $order_data['shipping']['state'];
				$orden["postcode"] 		= $order_data['shipping']['postcode'];
				$orden["country"] 		= $order_data['shipping']['country'];
				$orden["phone"] 		= $order_data['billing']['phone'];
				$orden["mail"] 			= $order_data['billing']['email'];

				$catalog['status']  	= "OK";
				
				$catalog['lastAddress'] = $orden;
				// *********************************************
			}
			$regresa = rest_ensure_response($catalog);
			$regresa->set_status(200);
		}
		else{
			$regresa = rest_ensure_response(array('status'=>'Ok','lastAddress'=>''));
			$regresa->set_status(200);
		}
		
		return $regresa;
		/*
		global $wpdb;
		global $woocomerce;
		$regresa = "";
		$qryGetLastShopByMail = "SELECT max(post_id) AS 'order_id' FROM ".$wpdb->prefix."postmeta WHERE meta_value = '".$customerMail."'";
		$qrGetIsFav = $wpdb->get_row($qryGetLastShopByMail);
		if($qrGetIsFav->order_id != null) {
			// Get an instance of the WC_Order object
			$order 					 = wc_get_order($qrGetIsFav->order_id);
			$order_data 			 = $order->get_data();
			$address 				 = $order->get_order_number();
			$direccion["first_name"] = $order_data['billing']['first_name'];
			$direccion["last_name"]  = $order_data['billing']['last_name'];
			$direccion["phone"] 	 = $order_data['billing']['phone'];
			$direccion["address_1"]  = $order_data['shipping']['address_1'];
			$direccion["address_2"]  = $order_data['shipping']['address_2'];
			$direccion["city"] 		 = $order_data['shipping']['city'];
			$direccion["state"] 	 = $order_data['billing']['state'];
			$direccion["postcode"] 	 = $order_data['billing']['postcode'];
			$direccion["country"] 	 = $order_data['billing']['country'];
			//$address = $order->get_billing_address();
			$regresa = rest_ensure_response(array('result'=>'Success',"lastAddress"=>$direccion));
			$regresa->set_status(200);
		}
		else{
			$regresa = rest_ensure_response(array('result'=>'Success',"lastAddress"=>""));
			$regresa->set_status(200);
		}
		return $regresa;
		*/
	}
