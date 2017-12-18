<script>
	jQuery.noConflict();
	// iniciamos el jQuery
	jQuery(function($) {
		// Script del dropzone js
		Dropzone.autoDiscover = false;
		var idMainImg = "";
		var idGalleryItems = '';
		$("#media-uploader1").dropzone({
			url: 'admin-ajax.php?action=handle_dropped_media',
			acceptedFiles: 'image/*',
			maxFiles: 1,
			maxfilesexceeded: function(file) {
				this.removeFile(file);
				return false;
			},
			dictRemoveFile: "Quitar Archivo",
			parallelUploads: 1,
			success: function(file, response) {
				file.previewElement.classList.add("dz-success");
				file['attachment_id'] = response; // push the id for future reference
				console.log(response);
				idMainImg = jQuery('#idMainImg').val() + ',' + response.trim();
				jQuery('#idMainImg').val(idMainImg);
				var tipo = "";
				($('#rbtTipo:checked').val() == "product_image_gallery") ? tipo = "thumbnail_id": tipo = $('#rbtTipo:checked').val();
				//alert(idMainImg);
				var ruta = "<?php echo plugin_dir_url(__FILE__); ?>";
				$.ajax({
					type: "POST",
					url: ruta + "addimgfunct.php",
					data: {
						idimg: $('#idMainImg').val(),
						post_id: $('#txtPostId').val(),
						accion: 'thumbnail_id',
						tipo: tipo
					},
					success: function(r) {
						
						$('#s').removeClass('load'); 
						$('#search_result').show().html(r);
						/* */
					}
				});

			},
			error: function(file, response) {
				file.previewElement.classList.add("dz-error");
			},
			autoProcessQueue: false,
			init: function() {
				var submitButton = document.querySelector("#add1")
				myDropzone = this; // closure
				submitButton.addEventListener("click", function() {
					myDropzone.processQueue(); // Tell Dropzone to process all queued files.
				});
				// You might want to show the submit button only when 
				// files are dropped here:
				this.on("addedfile", function() {
					// Show submit button here and/or inform user to click it.
				});
			},
			// update the following section is for removing image from library
			addRemoveLinks: true,
			removedfile: function(file) {
				var attachment_id = file.attachment_id;
				jQuery.ajax({
					type: 'POST',
					url: 'admin-ajax.php?action=handle_deleted_media',
					data: {
						media_id: attachment_id
					}
				});
				var _ref;
				return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
			}
		});
		/* inicializamos otro dropzone, este será para las imagenes de galería */
		$("#media-uploader2").dropzone({
			url: 'admin-ajax.php?action=handle_dropped_media',
			acceptedFiles: 'image/*',
			parallelUploads: 10,
			success: function(file, response) {
				console.log(file, response);
				file.previewElement.classList.add("dz-success");
				file['attachment_id'] = response; // push the id for future reference
				idGalleryItems = jQuery('#idGalleryItems').val() + ',' + response.trim();
				jQuery('#idGalleryItems').val(idGalleryItems);
				var ruta = "<?php echo plugin_dir_url(__FILE__); ?>";
				$.ajax({
					type: "POST",
					url: ruta + "addimgfunct.php",
					data: {
						idimg: response.trim(),
						post_id: $('#txtPostId').val(),
						accion: 'ligargaleria',
						tipo: $('#rbtTipo:checked').val()
					},
					success: function(r) {
						/*
						$('#s').removeClass('load'); 
						$('#search_result').show().html(r);
						*/
					}
				});

			},
			error: function(file) {
				file.previewElement.classList.add("dz-error");
			},
			autoProcessQueue: false,
			init: function() {
				var submitButton = document.querySelector("#add2")
				myDropzone2 = this; // closure
				submitButton.addEventListener("click", function() {
					myDropzone2.processQueue(); // Le decimos a la librería Dropzone que procese la cola de archivos
				});
				// You might want to show the submit button only when 
				// files are dropped here:
				this.on("addedfile", function() {

					// Show submit button here and/or inform user to click it.
				});
			},
			// update the following section is for removing image from library
			addRemoveLinks: true,
			dictRemoveFile: "Quitar Archivo",
			removedfile: function(file) {
				var attachment_id = file.attachment_id;
				jQuery.ajax({
					type: 'POST',
					url: 'admin-ajax.php?action=handle_deleted_media',
					data: {
						media_id: attachment_id
					}
				});
				var _ref;
				return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
			}
		});
		/* del dropdown dinámico, extrae el ID del producto, de esta manera retenemos el ID durante el proceso de carga de imagenes */
		$(document).on('click', '.datoslente', function() {
			var datos = $(this).data("prod"); // extrae los valores del href
			var arrayDatos = datos.split("|");
			$("#txtNombreFoto").val(arrayDatos[0]);
			$("#txtPostId").val(arrayDatos[1]);
			$("#postTitle").val(arrayDatos[2]);
		});
		// Esconde el cuadro de resultados cuando no hay nada ingresado en la caja de texto
		$(document).click(function() {
			$('#search_result').hide();
		});
		/* función que hace el auto-completado */
		$('#s').keyup(function() {
			$(this).addClass('load');
			var rs = $(this).val();
			var ruta = "<?php echo plugin_dir_url(__FILE__); ?>";
			$.ajax({
				type: "POST",
				url: ruta + "busqueda.php",
				//data:"rs="+rs,
				data: {
					rs: rs,
					tipo: $('#rbtTipo:checked').val()
				},
				success: function(r) {
					$('#s').removeClass('load');
					$('#search_result').show().html(r);
				}
			});
		});
		// se encarga de esconder/mostrar el apartado de imagen principal, para el caso de imagnes de probador
		$(document).on("click", '#rbtTipo', function() {
			if ($(this).is(':checked')) {
				if ($(this).val() == "probadorGlazzol") {
					$("#media-uploader1").hide();
				} else {
					$("#media-uploader1").show();
				}
			}
		});
		// este boton limpiará el contenido de los hidden y id's resultantes de la subida de imagenes
		$(document).on("click","#btnLimpia",function(){
			jQuery("#txtNombreFoto").val("");
			jQuery("#txtPostId").val("");
			jQuery("#postTitle").val("");
			jQuery("#idGalleryItems").val("");
			jQuery('#idMainImg').val("");
			jQuery('div.dz-success').remove();

		});
	});

</script>