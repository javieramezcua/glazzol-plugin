<?php
	/*
	Plugin Name: Glazzol Media Manager
	Description: Simple plugin para cotejar Productos e imagenes, así como sus tipos de archivos adjuntos
	Author: Javier Amezcua
	Version: 0.0.1

	*/
	if ( !defined( 'ABSPATH' ) ) {
		exit;
	}

	// define( 'Javier_test_plugin_url',   plugin_dir_url( __FILE__ ) );
	define( 'Javier_test_plugin_version', '0.0.2' );
	define( 'Javier_test_plugin',  __FILE__  );
	define( 'Javier_test_plugin_url', untrailingslashit( dirname( Javier_test_plugin) ) );
	register_activation_hook(__FILE__,'javier_init');
	$dir=plugin_dir_url(__FILE__);
	require_once Javier_test_plugin_url."/includes/glazzol-rest-api.php";
	add_action('admin_menu', 'test2_plugin_setup_menu');
	
	function test2_plugin_setup_menu(){
		add_menu_page( 'GlazzolV2 Media Manager', 'GlazzolV2 Media Manager', 'manage_options', 'glazzolV2-media-manager', 'javier_init' );
	}
	/* --------------------------------------------------- */
	
	wp_enqueue_script('dropzone','https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.1.1/min/dropzone.min.js', array('jquery'));
	if( $_GET['page'] == 'glazzolV2-media-manager'){

		wp_enqueue_style('bootstrap','https://bootswatch.com/3/yeti/bootstrap.min.css');
		wp_enqueue_style('font-awesome','https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
		wp_enqueue_script('bootstrap','https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js', array('jquery'));
	}
	//wp_enqueue_script('my-script',$dir.'dropzone-js.js',array('jquery','dropzone'));
	
	//wp_localize_script('my-script','dropParam', $drop_param);
	/* --------------------------------------------------- */
	function javier_init(){
		// $upload_dir = wp_upload_dir();
		// $upload_path = $upload_dir['path'] . "/";
		// $resultUpload = myApp_uploadFiles($upload_path);
		// echo $upload_path."<br>".admin_url( 'admin-ajax.php' )."<br/>";
		
	?>
	<link href="<?php echo plugin_dir_url( __FILE__ ); ?>/assets/css/gmmstyles.css" rel="stylesheet" type="text/css">
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-12">
				<h1>Gestor de Archivos Glazzol</h1>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-12">
				<div class="main-search">
					<h5>Seleccione el típo de imágenes que desea cargar</h5>
					<div>
						<label class="radio-inline">	
							<input type="radio" id="rbtTipo" name="rbtTipo" value="product_image_gallery" >Imagenes Wordpress 
						</label>
						<label class="radio-inline">	
							<input type="radio" id="rbtTipo" name="rbtTipo" value="appGlazzol" >Imagenes Aplicación 
						</label>
						<label class="radio-inline">	
							<input type="radio" id="rbtTipo" name="rbtTipo" value="probadorGlazzol" >Imagenes Probador 
						</label>
					</div>
					<hr>
					<div class="col-sm-6">
						<div class="input-group">
							<span class="input-group-btn">
									<button class="btn btn-default" type="button">Buscar Producto</button>
								</span>
							<input class="form-control" id="s" name="s" type="text" value="Buscar...." name="s" onblur="if (this.value == '') {this.value = 'Buscar....';}" onfocus="if (this.value == 'Buscar....') {this.value = '';}" autocomplete="off" required>
							<input type="hidden" id="txtNombreFoto" name="txtNombreFoto" disabled>
							<!--  -->
							<input type="hidden" id="txtPostId" name="txtPostId" disabled>
							<!--  -->
						</div>
						<!-- /input-group -->
					</div>
					<!-- /.col-lg-6 -->
					<div class="col-sm-6">
						<input type="text" id="postTitle" name="postTitle" class="form-control" disabled required="">
						<!--  -->
					</div>
					<!-- <input type="submit" id=d"searchsubmit" value="Buscar" /> -->
					<ul id="search_result"></ul>
				</div>
			</div>
		</div>
		<hr>
		<div class="row">
			<div class="col-sm-12">
				<div class="col-sm-4">
					<h5>Coloca aquí la Imagen destacada</h5>
					<div id="media-uploader1" name="media-uploader1" class="">
						<div class="dz-message">
							<span><i class="fa fa-plus"></i></span> Añadir imagen destacada
						</div>
					</div>
					<input type="text" name="idMainImg" id="idMainImg" value="">
					<button id="add1" name="add1" class="btn btn-success">Cargar</button>
				</div>
				<div class="col-sm-8">
					<h5>Coloca aquí la galería de imagenes</h5>
					<div id="media-uploader2" name="media-uploader2" class="">
						<div class="dz-message">
							<span><i class="fa fa-plus"></i></span> Añadir las imagenes de galería.
						</div>
					</div>
					<input type="text" name="idGalleryItems" id="idGalleryItems" value="">
					<button id="add2" name="add2" class="btn btn-success">Cargar</button>
				</div>
			</div>
		</div>
		<hr/>
		<div class="row">
			<div class="col-sm-12 text-center">
				<button id="btnLimpia" name="btnLimpia" class="btn btn-primary"><i class="fa fa-trash-o"></i>&nbsp;Limpiar Campos</button>
			</div>
		</div>
		<div class="row">
				<div class="col-sm-12">
					<div class="text-center">
						<p class="max-upload-size">
							<?php printf( __('Capacidad máxima de archivos: %s.' ), esc_html( size_format( wp_max_upload_size()))); ?>
						</p>
					</div>
				</div>
			</div>
		
	</div>

	
	<?php
		require_once Javier_test_plugin_url."/funcionesJS.php";
	}
