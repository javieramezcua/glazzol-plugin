<?php	
	function conektaGatewayFunct($order_id,$tokenId){
		global $wpdb;
		global $woocommerce;
		// Conekta::setApiKey("key_qRxow5yPLtCEgxzsuJXDdw"); // <-- llave de produccion
		\Conekta\Conekta::setApiVersion("2.0.0");
		\Conekta\Conekta::setApiKey("key_kWjAftXE9DtE8yLXcZHq5A"); // <-- llave de pruebas
		\Conekta\Conekta::setLocale('es');
		$data ="";
		// Get an instance of the WC_Order object (same as before)
		//var_dump($order_id);
		
		$order = wc_get_order($order_id);
		//var_dump($order->get_items());
		$order_data = $order->get_data(); // The Order data

		$order_id = $order_data['id'];
		$order_parent_id = $order_data['parent_id'];
		$order_status = $order_data['status'];
		$order_currency = $order_data['currency'];
		$order_version = $order_data['version'];
		$items            = $order->get_items();
		$line_items       = jjad_build_line_items($items);
		## Creation and modified WC_DateTime Object date string ##
		//  crear usuario
		$response = array('mensaje' => '' );
		try {
		  $customer = \Conekta\Customer::create(
		    array(
		      "name" => $order_data['shipping']['first_name'].' '.$order_data['shipping']['last_name'],
		      "email" => $order_data['billing']['email'],
		      "phone" => $order_data['billing']['phone'],
		      "payment_sources" => array(
		        array(
		            "type" => "card",
		            "token_id" => $tokenId
		        )
		      )//payment_sources
		    )//customer
		  );
		} catch (\Conekta\ProccessingError $error){
		  $response['mensaje'] .= "Customer error: ".$error->getMesage();
		} catch (\Conekta\ParameterValidationError $error){
		  $response['mensaje'] .= "Customer error: ".$error->getMessage();
		} catch (\Conekta\Handler $error){
		  $response['mensaje'] .= "Customer error: ".$error->getMessage();
		}

		try {
			$error = false;
			$mensaje = 'Pago exitoso';
			// Llamada a Conekta...
			
			$order = \Conekta\Order::create(array(
					'line_items'=>$line_items,
					'currency' => "MXN",
					"monthly_installments"=>3,
					"shipping_contact" => array(
		        "phone" => $order_data['billing']['phone'],
		        "receiver" => $order_data['shipping']['first_name'].' '.$order_data['shipping']['last_name'],
		        "address" => array(
		          "street1" => $order_data['billing']['address_1'],
		          "city" => $order_data['shipping']['city'],
		          "state" => $order_data['shipping']['state'],
		          "country" => "MX",
		          "postal_code" => $order_data['shipping']['postcode'],
		          "residential" => true
		        )//address
		      )
				)
			);
			return true;
		} 
		catch (\Conekta\ProccessingError $error){
		  $response['mensaje'] .= "Order error: ".$error->getMesage();
		} catch (\Conekta\ParameterValidationError $error){
		  $response['mensaje'] .= "Order error: ".$error->getMessage();
		} catch (\Conekta\Handler $error){
		  $response['mensaje'] .= "Order error: ".$error->getMessage();
		}
	}
	function jjad_build_line_items($items){
    $line_items = array();

    foreach ($items as $item) {

        $subTotal    = floatval($item['line_subtotal']) * 1000;
        $subTotal    = $subTotal / floatval($item['qty']);
        $productmeta = new WC_Product($item['product_id']);
        $sku         = $productmeta->get_sku();
        $unit_price  = $subTotal;
        $itemName    = itemNameValidation($item['name']);
        $unitPrice   = intval(round(floatval($unit_price) / 10), 2);
        $quantity    = intval($item['qty']);

        $line_item_params = array(
            'name'        => $itemName,
            'unit_price'  => $unitPrice,
            'quantity'    => $quantity,
            'tags'        => ['WooCommerce', "Conekta "],
            'metadata'    => array('soft_validations' => true)
        );

        if (!empty($sku)) {
            $line_item_params = array_merge(
                $line_item_params, 
                array(
                    'sku' => $sku
                )
            );
        }
        $line_items = array_merge($line_items, array($line_item_params));
    }
    return $line_items;
	}