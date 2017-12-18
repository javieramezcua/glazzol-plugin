<?php
	/* ********************************************************* */
	function glazzol_rest_get_contact(WP_REST_Request $request){
		global $wpdb;

		$data = "";
		
		$json = file_get_contents('php://input');
		$json_string = stripslashes($json);
		$data = json_decode($json_string);
		//$keys = array_keys($data);
		$regresa  = "";
		$to = "hola@glazzol.com";
		$subject = $data->subject;
		$message = $data->message;
		//$headers = 'From: '.$data['name'].' <'.$data['email'].'>\n'.'BCC: Javier Amezcua <javieramezcuaduran@gmail.com >\n';
		$headers = "Reply-To:".$data->name." <".$data->email.">";
		//$headers.= "BCC: Javier Amezcua <javieramezcuaduran@gmail.com >\r\n";
		if(wp_mail( $to, $subject, $message, $headers)){
		
			$regresa = rest_ensure_response(array('result'=>'Éxito','message'=>'Petición de contacto registrada'));
			$regresa->set_status(201);
		}
		else{
			
			$regresa = rest_ensure_response(array('status'=>'Error',"mensaje"=>"Petición de contacto no registrada"));
			$regresa->set_status(400);
		}
		return $regresa;
	}
	/* ********************************************************* */
	function glazzol_rest_create_return(WP_REST_Request $request){
		global $wpdb;
		$data = "";
		// RECIBIMOS EL POST
		$json = file_get_contents('php://input');
		$json_string = stripslashes($json);
		$data = json_decode($json_string);
		// EXTRAEMOS LOS PARAMETROS 
		$to = "hola@glazzol.com";
		$subject = "Registro de devolución";
		$message = $data->message;
		$headers = "Reply-To:".$data->name." <".$data->email.">\r\n";
		// REGISTRAMOS EN LA BASE DE DATOS
		$qryInsBasic = "INSERT INTO ".$wpdb->prefix."cf7_data";
		$qryInsBasic.= " VALUES (null,NOW())";
		$wpdb->query($qryInsBasic);
		$idInsertado =  $wpdb->insert_id;
		$qryIns2 = "INSERT INTO ".$wpdb->prefix."cf7_data_entry ";
		$qryIns2.= " VALUES ";
		$qryIns2.= " (null,'4098','".$idInsertado."','_wpcf7_container_post','0')";
		$qryIns2.= " ,(null,'4098','".$idInsertado."','your-name','".$data->name."' )";
		$qryIns2.= " ,(null,'4098','".$idInsertado."','your-email','".$data->email."')";
		$qryIns2.= " ,(null,'4098','".$idInsertado."','your-phone','".$data->phone."')";
		$qryIns2.= " ,(null,'4098','".$idInsertado."','your-shop','".$data->order_number."')";
		$qryIns2.= " ,(null,'4098','".$idInsertado."','tipo-compra','".$data->returnType."')";
		$qryIns2.= " ,(null,'4098','".$idInsertado."','your-message','".$data->message."')";
		$qryIns2.= " ,(null,'4098','".$idInsertado."','g-recaptcha-response','')";
		$qryIns2.= " ,(null,'4098','".$idInsertado."','submit_time',now())";
		$qryIns2.= " ,(null,'4098','".$idInsertado."','submit_ip','') ";
		$qryIns2.= " ,(null,'4098','".$idInsertado."','submit_user_id',0)";
		$wpdb->query($qryIns2);
		// ENVIAMOS CORREO DE NOTIFICACIÓN AL EQUÍPO
		/* **************************************** */
		if(wp_mail( $to, $subject, $message, $headers)){
			$regresa = rest_ensure_response(array('result'=>'Éxito','message'=>'Petición de devolución  registrada'));
			$regresa->set_status(201);
		}
		else{
			$regresa = rest_ensure_response(array('status'=>'Error',"mensaje"=>"La información proporcionada no pudo ser procesada"));
			$regresa->set_status(400);
		}
		return $regresa;
	}