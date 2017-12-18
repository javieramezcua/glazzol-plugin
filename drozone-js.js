jQuery.noConflict();
			// iniciamos el jQuery
			jQuery( function($) {
				// Script del dropzone js
				Dropzone.autoDiscover = false;
				var ids ='';
				$("#media-uploader1").dropzone({
					url: 'admin-ajax.php?action=handle_dropped_media',
					acceptedFiles: 'image/*',
					parallelUploads: 10,
					success: function (file, response) {
						file.previewElement.classList.add("dz-success");
						file['attachment_id'] = response; // push the id for future reference
						console.log(response);
						ids = jQuery('#media-ids').val() + ',' + response.trim();
						jQuery('#media-ids').val(ids);
						alert(ids);
					},
					error: function (file, response) {
						file.previewElement.classList.add("dz-error");
					},
					complete:function(){
						if(ids!=""){
							jQuery.ajax({
								type: 'POST',
								url: dropParam.delete,
								data: {
									media_id : attachment_id
								}
							});
						}
					},
					autoProcessQueue: false,
				  init: function() {
						var submitButton = document.querySelector("#add")
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
					addRemoveLinks: false,
					removedfile: function(file) {
						var attachment_id = file.attachment_id;        
						jQuery.ajax({
							type: 'POST',
							url: dropParam.delete,
							data: {
								media_id : attachment_id
							}
						});
						var _ref;
						return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;        
					},
					completed:function(){
						
					}
					
				});
				/* inicializamos otro dropzone, este será para las imagenes de galería */
				$("#media-uploader2").dropzone({
					url: 'admin-ajax.php?action=handle_dropped_media',
					acceptedFiles: 'image/*',
					parallelUploads: 10,
					success: function (file, response) {
						file.previewElement.classList.add("dz-success");
						file['attachment_id'] = response; // push the id for future reference
						console.log(response);
						ids = jQuery('#media-ids').val() + ',' + response.trim();
						jQuery('#media-ids').val(ids);
						alert(ids);
					},
					error: function (file, response) {
						file.previewElement.classList.add("dz-error");
					},
					complete:function(){
						if(ids!=""){
							jQuery.ajax({
								type: 'POST',
								url: dropParam.delete,
								data: {
									media_id : attachment_id
								}
							});
						}
					},
					autoProcessQueue: false,
				  init: function() {
						var submitButton = document.querySelector("#add")
						myDropzone = this; // closure
						submitButton.addEventListener("click", function() {
							myDropzone.processQueue(); // Le decimos a la librería Dropzone que procese la cola de archivos
						});
						// You might want to show the submit button only when 
						// files are dropped here:
						this.on("addedfile", function() {
							
							// Show submit button here and/or inform user to click it.
						});     
				  },
					// update the following section is for removing image from library
					addRemoveLinks: false,
					removedfile: function(file) {
						var attachment_id = file.attachment_id;        
						jQuery.ajax({
							type: 'POST',
							url: dropParam.delete,
							data: {
								media_id : attachment_id
							}
						});
						var _ref;
						return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
					},
					completed:function(){
						
					}
					
				});
				/* del dropdown dinámico, extrae el ID del producto, de esta manera retenemos el ID durante el proceso de carga de imagenes */
				$(document).on('click','.datoslente',function(){
					var datos = $(this).data("prod"); // extrae los valores del href
					var arrayDatos = datos.split("|");
					$("#txtNombreFoto").val(arrayDatos[0]);
					$("#txtPostId").val(arrayDatos[1]);
				});
				// Esconde el cuadro de resultados cuando no hay nada ingresado en la caja de texto
				$(document).click(function () { 
					$('#search_result').hide();
				});
				/* función que hace el auto-completado */
				$('#s').keyup(function () { 
					$(this).addClass('load');
					var rs = $(this).val();
					var ruta =  "<?php echo plugin_dir_url(__FILE__); ?>";
					$.ajax({
						type:"POST",
						url: ruta+"busqueda.php",
						data:"rs="+rs,
						success:function (r) {
							$('#s').removeClass('load'); 
							$('#search_result').show().html(r);
						}     
					});
				});
				
			});