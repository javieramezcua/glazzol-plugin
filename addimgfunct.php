<?php
	//include('../../../../wp-config.php');
	require_once('../../../wp-config.php');
	require_once('../../../wp-load.php');
	global $wpdb;
	
	if(($_POST["idimg"]!="") && ($_POST["post_id"]!="") && ($_POST["accion"]!="")){

		$metaValue = ltrim($_POST["idimg"],','); //al parámetro recibido le quitamos la coma inicial de la concatenación
		$post_id = $_POST["post_id"];
		$accion = $_POST["accion"];
		$metaKeyVal = "_".$_POST["tipo"];
		/* 
		tipos de imagenes:
		- _product_image_gallery y thumbnail_id  para wordpress
		*/
		$sql = "SELECT * FROM ".$wpdb->prefix."postmeta WHERE post_id = ".$post_id." AND meta_key = '".$metaKeyVal."' ";
		$result = $wpdb->get_results($sql);
		if(empty($result)) { 
			$sql = "INSERT INTO ".$wpdb->prefix."postmeta VALUES (null,'".$post_id."','".$metaKeyVal."','".$metaValue."')";
			$res  = $wpdb->query($sql);
			//echo "<script>alert('".$sql."')</script>";
			if($res > 0) { 
				echo '<script>alert("Registrio exitoso")</script>'; 
			}
			else {
				echo '<script>alert("Registrio fallido")</script>'; 
			}
		}
		else {
			$sql = "UPDATE ".$wpdb->prefix."postmeta ";
			$sql.= " SET meta_value = CONCAT(meta_value,',','".$metaValue."')";
			$sql.= " WHERE post_id = ".$post_id." AND meta_key = '".$metaKeyVal."' ";
			$res  = $wpdb->query($sql);
			//echo "<script>alert('".$sql."')</script>";
			if($res > 0) { 
				echo '<script>alert("Registrio exitoso")</script>'; 
			}
			else {
				echo '<script>alert("Registrio fallido")</script>'; 
			}
		}
		

	}
?>