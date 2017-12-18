<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package storefront
 */
global $url, $urlC;
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<?php wp_head(); ?>
	
	<style type="text/css">
		<?php // include 'glazzolCSS.css'; ?>
		<?php include'glazzolCSS-bk.css'; ?>
		<?php include 'glazzolFormCSS.css'; ?>
		<?php include 'extramenu.css'; ?>
		
		<?php
			$bg = array(
				'#000000', 
				'#77777a', 
				'#3e4543', 
				'#b2b3b2'
			); // array of filenames
			
			for($x = 3; $x <= 7; $x++){
				$i = rand(0, count($bg)-1);
				$selectedBg = "$bg[$i]";
				echo ".prdctfltr_title_selected:nth-child($x){ background: $selectedBg; } ";
			}
		?>
		.prdctfltr_cat{
			display: none !important;
			visibility: hidden !important;
		}
	</style>
</head>

<body <?php body_class(); ?>>
<div class	="busqueda-header">
	<div class="busqueda-header-cont" style="">
		<div class="cerrar-busqueda-header" style=""><i class="fa fa-close"></i></div>
		<?php echo do_shortcode('[wcas-search-form]'); ?>
	</div>
</div>
<div class="container-fluid top-linkGlazzol fColorNegro" style="background: #E30212; color:#fff; ">
	<div class="col-sm-12 text-center hideOnMobil" >
		<div class="col-sm-6 text-left"><i class="fa fa-percent" aria-hidden="true"></i> Del 20% al 50% y ¡Hasta  12 Meses Sin Intereses!. <i class="fa fa-credit-card" aria-hidden="true"></i></div>
		<div class="col-sm-6 text-right"><i class="fa fa-calendar" aria-hidden="true"></i> Este 17,18,19 y 20 de Noviembre. </div>
		
	</div>
	<div class="col-sm-12 text-center showOnMobile">
		Del 20% al 50% y ¡Hasta 12 MSI. <i class="fa fa-credit-card" aria-hidden="true"></i><br/>
		Del 17 al 20 de Noviembre.<i class="fa fa-calendar" aria-hidden="true"></i>
	</div>
</div>
<div class="container-fluid top-linkGlazzol fColorNegro">
	<div class="col-sm-6 text-left hideOnMobil">
		¡Hasta 3, 6, 9 y 12 MSI Y ENVÍO GRATIS!
	</div>
	<div class="col-sm-6 text-right">
		<a href="mailto:hola@glazzol.com.mx">hola@glazzol.com</a> | 
		<a href="tel:+523310310184">(33)1-031-0184</a> | 
		<a href="https://www.facebook.com/glazzol/" target="_blank"> <i class="fa fa-facebook" aria-hidden="true"></i> </a> | 
		<a href="https://www.instagram.com/glazzol/" target="_blank"> <i class="fa fa-instagram" aria-hidden="true"></i> </a>
	</div>
</div>
<?php //do_action( 'storefront_before_site' ); ?>

<div id="page" class="hfeed site">
	<?php //do_action( 'storefront_before_header' ); ?>

	<header id="masthead" class="site-header" role="banner" style="<?php //storefront_header_styles(); ?>">
		
		<div class="container-fluid"><!-- col-full -->
			<div class="">
				<div class="site-branding">
					<div class="beta site-title">
						<a class="logo" href="<?php echo get_site_url(); ?>" rel="home">
							<img src="<?php echo get_site_url(); ?>/wp-content/themes/storefront/img/logos/logo-blanco200x101.png" alt="Glazzol">
						</a>
					</div>
				</div>
				
				<div class="wrap-lateral" style=""><!--  storefront-primary-navigation -->
					<nav id="site-navigation" class="main-navigation" role="navigation" aria-label="Primary Navigation">
						<button class="menu-toggle" aria-controls="site-navigation" aria-expanded="false"><span><i class="fa fa-bars fa-2x" aria-hidden="true"></i></span></button>
						<div class="primary-navigation">
							<ul id="menu-navigation" class="menu nav-menu" aria-expanded="false">
								<li id="menu-item-107" class="menu-item menu-item-type-post_type menu-item-object-page">
									<a href="<?php echo get_site_url(); ?>/tienda/?pa_genero-lente=dama,unisex">Mujer</a>
								</li>
								<li id="menu-item-107" class="menu-item menu-item-type-post_type menu-item-object-page">
									<a href="<?php echo get_site_url(); ?>/tienda/?pa_genero-lente=caballero,unisex">Hombre</a>
								</li>
								<li id="menu-item-7192" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-7192 smm-active menu-item-has-children"><a href="#">Marcas</a>
									<ul class="sub-menu" style="background-color: #fff;">
										<li>
											<div class="smm-mega-menu">
												<div class="smm-row">
													<div class="smm-span-2">
														<aside id="nav_menu-11" class="widget widget_nav_menu">
															<div class="menu-mega-menu-1st-container">
																<ul id="menu-mega-menu-1st" class="menu">
																	<li><a href="<?php echo get_site_url(); ?>/categoria-producto/arnette/<?php echo $url ?>"><img src="<?php echo get_template_directory_uri(); ?>/img/arnette.png" alt="arnette" width="" height="" /></a></li>
																	<li><a href="<?php echo get_site_url(); ?>/categoria-producto/carrera/<?php echo $url ?>"><img src="<?php echo get_template_directory_uri(); ?>/img/carrera.jpg" alt="carrera" width="" height="" /></a></li>
																	<li><a href="<?php echo get_site_url(); ?>/categoria-producto/tory-burch/<?php echo $url ?>"><img src="<?php echo get_template_directory_uri(); ?>/img/toryburch.png" alt="toryburch" width="" height="" /></a></li>
																	<li><a href="<?php echo get_site_url(); ?>/categoria-producto/hugo-boss/<?php echo $url ?>"><img src="<?php echo get_template_directory_uri(); ?>/img/hugoboss.png" alt="hugoboss" width="" height="" /></a></li>
																</ul>
															</div>
														</aside>
													</div>
													<div class="smm-span-2">
														<aside id="nav_menu-12" class="widget widget_nav_menu">
															<div class="menu-mega-menu-2nd-container">
																<ul id="menu-mega-menu-2nd" class="menu">
																	<li><a href="<?php echo get_site_url(); ?>/categoria-producto/dkny/<?php echo $url ?>"><img src="<?php echo get_template_directory_uri(); ?>/img/dkny.png" alt="dkny" width="" height="" /></a></li>
																	<li><a href="<?php echo get_site_url(); ?>/categoria-producto/coach/<?php echo $url ?>"><img src="<?php echo get_template_directory_uri(); ?>/img/coach.png" alt="rayban" width="" height="" /></a></li>
																	<li><a href="<?php echo get_site_url(); ?>/categoria-producto/dolce-gabbana/<?php echo $url ?>"><img src="<?php echo get_template_directory_uri(); ?>/img/dolceandagabanna.png" alt="dolceandagabanna" width="" height="" /></a></li>
																</ul>
															</div>
														</aside>
													</div>
													<div class="smm-span-2">
														<aside id="nav_menu-13" class="widget widget_nav_menu">
															<div class="menu-mega-menu-3rd-container">
																<ul id="menu-mega-menu-3rd" class="menu">
																	<li><a href="<?php echo get_site_url(); ?>/categoria-producto/michael-kors/<?php echo $url ?>"><img src="<?php echo get_template_directory_uri(); ?>/img/michaelkors.png" alt="michaelkors" width="" height="" /></a></li>
																	<li><a href="<?php echo get_site_url(); ?>/categoria-producto/oakley/<?php echo $url ?>"><img src="<?php echo get_template_directory_uri(); ?>/img/oakley.png" alt="oakley" width="" height="" /></a></li>
																	<li><a href="<?php echo get_site_url(); ?>/categoria-producto/persol/<?php echo $url ?>"><img src="<?php echo get_template_directory_uri(); ?>/img/persol.png" alt="persol" width="" height="" /></a></li>
																</ul>
															</div>
														</aside>
													</div>
													<div class="smm-span-2">
														<aside id="nav_menu-14" class="widget widget_nav_menu">
															<div class="menu-mega-menu-4th-container">
																<ul id="menu-mega-menu-4th" class="menu">
																	<li><a href="<?php echo get_site_url(); ?>/categoria-producto/polaroid/<?php echo $url ?>"><img src="<?php echo get_template_directory_uri(); ?>/img/Polaroid.jpg" alt="polaroid" width="" height="" /></a></li>
																	<li><a href="<?php echo get_site_url(); ?>/categoria-producto/prada/<?php echo $url ?>"><img src="<?php echo get_template_directory_uri(); ?>/img/prada.png" alt="prada" width="" height="" /></a></li>
																	<li><a href="<?php echo get_site_url(); ?>/categoria-producto/polo-ralph-lauren/<?php echo $url ?>"><img src="<?php echo get_template_directory_uri(); ?>/img/ralphlaurent.png" alt="ralphlaurent" width="" height="" /></a></li>
																</ul>
															</div>
														</aside>
													</div>
													<div class="smm-span-2">
														<aside id="nav_menu-14" class="widget widget_nav_menu">
															<div class="menu-mega-menu-4th-container">
																<ul id="menu-mega-menu-4th" class="menu">
																	<li><a href="<?php echo get_site_url(); ?>/categoria-producto/ray-ban/<?php echo $url ?>"><img src="<?php echo get_template_directory_uri(); ?>/img/Ray-Ban500x500.jpg" alt="rayban" width="" height="" /></a></li>
																	
																	<li><a href="<?php echo get_site_url(); ?>/categoria-producto/christian-dior/<?php echo $url ?>"><img src="<?php echo get_template_directory_uri(); ?>/img/Dior_500x500.jpg" alt="Dior" width="" height="" /></a></li>
																	<li><a href="<?php echo get_site_url(); ?>/categoria-producto/fendi/<?php echo $url ?>"><img src="<?php echo get_template_directory_uri(); ?>/img/FENDI.jpg" alt="fendi" width="" height="" /></a></li>
																</ul>
															</div>
														</aside>
													</div>
													<div class="smm-span-2 smm-last">
														<aside id="text-5" class="widget widget_text">
															<div class="menu-mega-menu-4th-container">
																<ul id="menu-mega-menu-4th" class="menu">
																	<li><a href="<?php echo get_site_url(); ?>/categoria-producto/versace/<?php echo $url ?>"><img src="<?php echo get_template_directory_uri(); ?>/img/versace.png" alt="versace" width="" height="" /></a></li>
																	<li><a href="<?php echo get_site_url(); ?>/categoria-producto/vogue/<?php echo $url ?>"><img src="<?php echo get_template_directory_uri(); ?>/img/vogue.png" alt="vogue" width="" height="" /></a></li>
																	<li><a href="<?php echo get_site_url(); ?>/categoria-producto/tommy-hilfiger/<?php echo $url ?>"><img src="<?php echo get_template_directory_uri(); ?>/img/tommy-hilfiger.jpg" alt="tommy" width="" height="" /></a></li>
																</ul>
															</div>
														</aside>
													</div>
												</div>
											</div>
										</li>
									</ul>
								</li>
								<li id="menu-item-7258" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-7258">
									<a href="<?php echo get_site_url(); ?>/etiqueta-producto/outlet/">Outlet</a>
								</li>
								<li id="menu-item-7258" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-7258">
									<a href="<?php echo get_site_url(); ?>/comunidad-glazzol/">Comunidad</a>
								</li>
							</ul>
						</div>
						<div class="menu">
							<ul>
								<h4 class="text-center fColorBco">GÉNERO</h4>
								<li class="itemhalf ">
									<a href="<?php echo get_site_url(); ?>/tienda/?pa_genero-lente=dama,unisex" class="">
										Mujer
									</a>
								</li>
								<li class="itemhalf ">
									<a href="<?php echo get_site_url(); ?>/tienda/?pa_genero-lente=caballero,unisex" class=" ">
										Hombre
									</a>
								</li>
								<li class="itemhalf ">
									<a href="<?php echo get_site_url(); ?>/garantias-glazzol/" class="">
										Garantías Glazzol
									</a>
								</li>
								<li class="itemhalf ">
									<a href="<?php echo get_site_url(); ?>/comunidad-glazzol/" class=" ">
										Comunidad
									</a>
								</li>
								<!--
								<li class="current_page_item">
									<a href="<?php echo get_site_url(); ?>/tienda/?pa_genero-lente=caballero,unisex">Mujer</a>
								</li>
								<li class="page_item page-item-103">
									<a href="<?php echo get_site_url(); ?>/tienda/?pa_genero-lente=caballero,unisex">Hombre</a>
								</li>
								-->
								<h4 class="text-center fColorBco">MARCAS</h4>
								<?php
								
									$taxonomy     = 'product_cat';
									$orderby      = 'name';  
									$show_count   = 1;      // 1 for yes, 0 for no
									$pad_counts   = 1;      // 1 for yes, 0 for no
									$hierarchical = 1;      // 1 for yes, 0 for no  
									$title        = '';  
									$empty        = 1;

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
									/*
									foreach ($all_categories as $cat) {
										if($cat->category_parent == 0) {
											$category_id = $cat->term_id;       
											echo '<a href="'. get_term_link($cat->slug, 'product_cat') .'" class="itemthird">'. $cat->name .'</a>';									
										}       
									}
									*/
									foreach ($all_categories as $cat) {
										if($cat->category_parent == 0) {
											$category_id = $cat->term_id;
											$thumb_id = get_woocommerce_term_meta( $cat->term_id, 'thumbnail_id', true );
											echo '<a href="'. get_term_link($cat->slug, 'product_cat') .'" class="itemthird text-center"><img src="'. wp_get_attachment_url(  $thumb_id ).'" class="customWimg"></a>';									
										}       
									}
								?>
								<hr/>
								<h3 class="text-center fColorBco">CONTACTO</h3>
								<li class="itemhalf">
									<a href="tel:+523310310184" class="extraClass">
										<span><i class="fa fa-phone" aria-hidden="true"></i> (33)10-31-01-84</span>
									</a>
								</li>
								<li class="itemhalf">
									<a href="https://matute.com.mx/contacto" title="Contacto Matute" class="innerIcon">
										<span><i class="fa fa-envelope-open" aria-hidden="true"></i> hola@glazzol.com</span>
									</a>
								</li>
								<li class="itemhalf ">
									<a href="https://www.facebook.com/glazzol/" target="_blank" title="Facebook Matute">
										<span><i class="fa fa-facebook-square fa-2x"></i></span>
									</a>
								</li>
								<li class="itemhalf ">
									<a href="https://www.instagram.com/glazzol/" target="_blank" title="Instagram Matute">
										<span><i class="fa fa-instagram fa-2x "></i></span>
									</a>
								</li>

							</ul>
						</div>
					</nav><!-- #site-navigation -->
					<div class="li-search hideOnMobil">
						<i class="fa fa-search open-search" aria-hidden="true"></i>
					</div>
					<ul id="site-header-cart" class="site-header-cart menu">
						<li class="text-right">
							<a class="cart-contents" href="https://demo.woothemes.com/storefront/cart/" title="View your shopping cart">
								<span class="amount">$0.00</span> <span class="count">0 productos</span>
							</a>
					
						</li>
						<li>
							<div class="widget woocommerce widget_shopping_cart">
								<div class="widget_shopping_cart_content">
									<p class="woocommerce-mini-cart__empty-message">No hay productos en el carrito.</p>
								</div>
							</div>			
						</li>
					</ul>
				</div>
			</div>
			<?php
			/**
			 * Functions hooked into storefront_header action
			 *
			 * @hooked storefront_skip_links                       - 0
			 * @hooked storefront_social_icons                     - 10
			 * @hooked storefront_site_branding                    - 20
			 * @hooked storefront_secondary_navigation             - 30
			 * @hooked storefront_product_search                   - 40
			 * @hooked storefront_primary_navigation_wrapper       - 42
			 * @hooked storefront_primary_navigation               - 50
			 * @hooked storefront_header_cart                      - 60
			 * @hooked storefront_primary_navigation_wrapper_close - 68
			 */
			// 			do_action( 'storefront_header' ); 
			?>

		</div>
	</header><!-- #masthead -->

	<?php
	/**
	 * Functions hooked in to storefront_before_content
	 *
	 * @hooked storefront_header_widget_region - 10
	 */
	//do_action( 'storefront_before_content' ); ?>

	<div id="content" class="site-content" tabindex="-1">
		<div class="container">
		<!-- <div class="col-full"> -->
		
		<?php
		/**
		 * Functions hooked in to storefront_content_top
		 *
		 * @hooked woocommerce_breadcrumb - 10
		 */
		//do_action( 'storefront_content_top' );
