<?php
	
	require_once('../../../wp-config.php');
	require_once('../../../wp-load.php');
	global $wpdb;
	$search = str_replace(' ','.+', $_POST['rs']); //parametro enviado
	$tipo = trim($_POST['tipo']);
	/*
		SELECT meta_value FROM $wpdb->prefix.postmeta WHERE post_id = $row->ID  AND meta_key  ='_".$tipo."'
	*/
	
	if($search!=''){
		$sql = "SELECT ID,post_content,post_title, post_name FROM wpvl_posts WHERE post_type='product' && (post_title REGEXP '".$search."' || post_content REGEXP '".$search."') ORDER BY post_title ";
		$result = $wpdb->get_results($sql);
		if(empty($result)) { 
			echo '<li><p>No hay productos.</p></li>'; 
		}
		else {
			foreach($result as $row){
				echo '<li data-prod="'.$row->post_name.'|'.$row->ID.'|'.$row->post_title.'" class="datoslente">';
				if($tipo == "product_image_gallery"){
					$feat_image = wp_get_attachment_url( get_post_thumbnail_id($row->ID) );
					if($feat_image!='') { 
						echo '<img src="'.$feat_image.'" alt="" >'; 
					}
					else{
						echo '<img src="'.plugin_dir_url( __FILE__ ).'/assets/images/missing.png" alt="" >'; 
					}
				}
				else{
					$sqlGetExtra = 'SELECT meta_value FROM '.$wpdb->prefix.'postmeta WHERE post_id = '.$row->ID.'  AND meta_key  ="_'.$tipo.'"';
					$resultExtra = $wpdb->get_row($sqlGetExtra);
					if(empty($resultExtra)) {
						echo '<img src="'.plugin_dir_url( __FILE__ ).'/assets/images/missing.png" alt="" >';
					}
					else{
						$limpiaImgABuscar = ltrim($resultExtra->meta_value,',');//limpiamos posible residuo de coma a la izquierda
						$imagenABuscar = explode(",",$limpiaImgABuscar);
						$sqlGetExtraInner = 'SELECT guid FROM '.$wpdb->prefix.'posts WHERE ID ='.$imagenABuscar[0];
						//echo $sqlGetExtraInner;
						$resultExtraInner = $wpdb->get_row($sqlGetExtraInner);
						if(!empty($resultExtraInner)) {
							echo '<img src="'.$resultExtraInner->guid.'" alt="" style="width:40px;">'; 
						}
					}
				}
				echo '<span>'.$row->post_title.'</span>'.substr(trim(strip_tags($row->post_content)),0,50).'</li>';
			}// fin foreach
		}// fin  else
	}
	
?>
