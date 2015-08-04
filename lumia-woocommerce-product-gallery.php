<?php
/**
 * Plugin Name: Lumia Woocommerce Product Gallery
 * Plugin URI: http://www.weblumia.com/lumia-woocommerce-product-gallery/
 * Description: Lumia woocommerce product gallery modified the normal woocommerce product gallery with Elevate Zoom Jquery
 * Version: 1.0
 * Author: Weblumia Infomatics
 * Author URI: http://weblumia.com/
 * Requires at least: 3.8
 * Tested up to: 4.2.3
 * WC requires at least: 2.0
 * WC tested up to: 2.3.11
**/

if ( ! defined( 'ABSPATH' ) ) :
	exit; // Exit if accessed directly
endif;

/**
 * Check if WooCommerce is active
 **/
 
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) :

	if ( ! class_exists( 'LWooProductGallery' ) ) :
	
	/**
	 * Main LWooProductGallery Class
	 *
	 * @class LWooProductGallery
	 * @version	1.0
	 */
	final class LWooProductGallery {
		
		/**
		 * @var string
		 */
		public $version = '1.0', $i;
	
		/**
		 * @var LWooProductGallery The single instance of the class
		 * @since 1.0
		 */
		 
		protected static $_instance = null;
	
		/**
		 * Main LWooProductGallery Instance
		 *
		 * Ensures only one instance of LWooProductGallery is loaded or can be loaded.
		 *
		 * @since 1.0
		 * @static
		 * @see LWPG()
		 * @return LWooProductGallery - Main instance
		 */
		 
		public static function init_instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}
	
		/**
		 * Cloning is forbidden.
		 *
		 * @since 1.0
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'LWooProductGallery' ), '1.0' );
		}
	
		/**
		 * Unserializing instances of this class is forbidden.
		 *
		 * @since 1.0
		 */
		 
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'LWooProductGallery' ), '1.0' );
		}
	
		/**
		 * LWooProductGallery Constructor.
		 * @access public
		 * @return LWooProductGallery
		 */
		 
		public function __construct() {
			
			$this->i = 0;
			// Define constants
			self::lumia_product_gallery_constants();
			
			// Action Hooks
			add_action( 'init', array( $this, 'lumia_product_gallery_init' ), 0 );
			add_action( 'admin_head',       array( $this, 'lumia_product_gallery_admin_scripts' ) );
			add_action( 'admin_menu', array( &$this, 'lumia_product_gallery_admin_submenu' ) );
			add_action( 'wp_enqueue_scripts', array( &$this, 'lumia_product_gallery_frontend_scripts' ) );
			add_action( 'wp_footer', array( &$this, 'lumia_product_gallery_inline_scripts' ), 20 );
			
			// Filter Hooks
			add_filter( 'woocommerce_single_product_image_html', array( $this, 'lumia_single_product_thumbnails' ), 10, 2 );
			add_filter( 'woocommerce_single_product_image_thumbnail_html', array( $this, 'lumia_single_product_image_thumbnails' ), 10, 2 );
		}
	
		/**
		 * Define LWooProductGallery Constants
		 */
		 
		private function lumia_product_gallery_constants() {
			
			define( 'LWPG_PLUGIN_FILE', __FILE__ );
			define( 'LWPG_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
			define( 'LWPG_VERSION', $this->version );
			define( 'LWPG_TEXT_DOMAIN', 'lumia_product_gallery' );
		}
	
		/**
		 * Init LWooProductGallery when WordPress Initialises.
		 */
		 
		public function lumia_product_gallery_init() {
			
			// Set up localisation
			self::load_lumia_product_gallery_textdomain();
			
			// Set up localisation
			self::lumia_product_gallery_styles();
		}
		
		
		/**
		 * Front end style hook for LWooProductGallery
		 *
		 * @return @void
		 */
		 
		public function lumia_product_gallery_styles() {
			
			wp_enqueue_style( 'style', plugins_url( 'css/style.css', __FILE__ ) );
		}
		/**
		 * Admin script hook for LWooProductGallery
		 *
		 * @return @void
		 */
		 
		public function lumia_product_gallery_admin_scripts() {	
		
			wp_enqueue_style( 'colorpicker', plugins_url( 'css/admin/colorpicker.css', __FILE__ ) );
			
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'cpicker', plugins_url( '/js/admin/cpicker.js', __FILE__ ), '1.2' );
			wp_enqueue_script( 'eye', plugins_url( '/js/admin/eye.js', __FILE__ ), '2.0' );
			wp_enqueue_script( 'bound', plugins_url( '/js/admin/bound.js', __FILE__ ), '1.8.5' );
			wp_enqueue_script( 'layout', plugins_url( '/js/admin/layout.js', __FILE__ ), '1.0.2' );
		}
		
		/**
		 * Front end script hook for LWooProductGallery
		 *
		 * @return @void
		 */
		 
		public function lumia_product_gallery_frontend_scripts() {						
				
			wp_enqueue_script( 'elevatezoom', plugins_url( 'plugins/elevatezoom/jquery.elevateZoom-3.0.8.min.js', __FILE__ ), array(), '3.0.8', true );
		}
		
		/**
		 * Front end inline script hook for LWooProductGallery
		 *
		 * @return @void
		 */
		 
		public function lumia_product_gallery_inline_scripts() {	
		
			$gallery_settings = '';
			$gallery_settings = get_option( 'lumia_gallery_settings_options' );
			
			$gallery_type = isset( $gallery_settings['elevate']['gallery_type'] ) ? $gallery_settings['elevate']['gallery_type'] : '';
			$zoom_type = isset( $gallery_settings['elevate']['zoom_type'] ) ? $gallery_settings['elevate']['zoom_type'] : 'window';
			$width = isset( $gallery_settings['elevate']['width'] ) ? $gallery_settings['elevate']['width'] : '200';
			$height = isset( $gallery_settings['elevate']['height'] ) ? $gallery_settings['elevate']['height'] : '200';
			$lens_shape = isset( $gallery_settings['elevate']['lens_shape'] ) ? $gallery_settings['elevate']['lens_shape'] : 'square';
			$bg_color = isset( $gallery_settings['elevate']['bg_color'] ) ? $gallery_settings['elevate']['bg_color'] : '';
			$border_width = isset( $gallery_settings['elevate']['border_width'] ) ? $gallery_settings['elevate']['border_width'] : '4';
			$border_color = isset( $gallery_settings['elevate']['border_color'] ) ? $gallery_settings['elevate']['border_color'] : '#888';
			?>
			<script type="text/javascript">
			jQuery(function( $ ){
				$( '.lumia_product_slider img' ).elevateZoom({ 
					<?php if( $gallery_type == 'basic' ) { ?>
					zoomType : "<?php echo $zoom_type;?>",
					<?php } elseif( $gallery_type == 'tints' ) { ?>
					zoomType : "<?php echo $zoom_type;?>",
					tint: true, 
					tintColour: '<?php echo $bg_color;?>', 
					tintOpacity: 0.5,
					<?php } elseif( $gallery_type == 'inner' ) { ?>
					zoomType : "inner", 
					cursor: "crosshair",
					<?php } elseif( $gallery_type == 'fadein_fadeout' ) { ?>
					zoomWindowFadeIn: 500,
					zoomWindowFadeOut: 500,
					lensFadeIn: 500,
					lensFadeOut: 500,
					<?php } elseif( $gallery_type == 'easing' ) { ?>
					easing : true,
					<?php } elseif( $gallery_type == 'mousewheel' ) { ?>
					scrollZoom : true,
					<?php } elseif( $gallery_type == 'image_constrain' ) { ?>
					constrainType: "height", 
					constrainSize: <?php echo $height;?>, 
					zoomType: "lens", 
					containLensZoom: true,
					<?php } elseif( $gallery_type == 'lens_zoom' ) { ?>
					zoomType : "lens", 
					lensShape : "round", 
					lensSize : <?php echo $width;?>,
					<?php } ?>								
					zoomWindowWidth : "<?php echo $width;?>",
					zoomWindowHeight : "<?php echo $height;?>", 
					lensShape: "<?php echo $lens_shape;?>",
					zoomWindowBgColour: "<?php echo $bg_color;?>",
					borderSize: "<?php echo $border_width;?>",
					borderColour: "<?php echo $border_color;?>"					
				});
				
				$( '.thumbnails a' ).click( function( e ) {
					$( '.spinner' ).show();
					var $catalog = $( this ).attr( 'data-catalog' ),
					$full = $( this ).attr( 'data-full' );			
					
					$( '.lumia_product_slider' ).html( '<span class="spinner"></span><img data-zoom-image="' + $full + '" src="' + $catalog + '">' );
					$( '.lumia_product_slider img' ).elevateZoom({ 
						<?php if( $gallery_type == 'basic' ) { ?>
						zoomType : "<?php echo $zoom_type;?>",
						<?php } elseif( $gallery_type == 'tints' ) { ?>
						zoomType : "<?php echo $zoom_type;?>",
						tint: true, 
						tintColour: '<?php echo $bg_color;?>', 
						tintOpacity: 0.5,
						<?php } elseif( $gallery_type == 'inner' ) { ?>
						zoomType : "inner", 
						cursor: "crosshair",
						<?php } elseif( $gallery_type == 'fadein_fadeout' ) { ?>
						zoomWindowFadeIn: 500,
						zoomWindowFadeOut: 500,
						lensFadeIn: 500,
						lensFadeOut: 500,
						<?php } elseif( $gallery_type == 'easing' ) { ?>
						easing : true,
						<?php } elseif( $gallery_type == 'mousewheel' ) { ?>
						scrollZoom : true,
						<?php } elseif( $gallery_type == 'image_constrain' ) { ?>
						constrainType: "height", 
						constrainSize: <?php echo $height;?>, 
						zoomType: "lens", 
						containLensZoom: true,
						<?php } elseif( $gallery_type == 'lens_zoom' ) { ?>
						zoomType : "lens", 
						lensShape : "round", 
						lensSize : <?php echo $width;?>,
						<?php } ?>								
						zoomWindowWidth : "<?php echo $width;?>",
						zoomWindowHeight : "<?php echo $height;?>", 
						lensShape: "<?php echo $lens_shape;?>",
						zoomWindowBgColour: "<?php echo $bg_color;?>",
						borderSize: "<?php echo $border_width;?>",
						borderColour: "<?php echo $border_color;?>"					
					});
					setTimeout( function(){ $( '.spinner' ).hide(); }, 500 );		
				});
			});
			</script>
			<?php
		}
		
		/**
		 * Get the plugin url.
		 *
		 * @return string
		 */
		public function plugin_url() {
			return untrailingslashit( plugins_url( '/', __FILE__ ) );
		}
	
		/**
		 * Get the plugin path.
		 *
		 * @return string
		 */
		public function plugin_path() {
			return untrailingslashit( plugin_dir_path( __FILE__ ) );
		}
	
		/**
		 * Get Ajax URL.
		 *
		 * @return string
		 */
		public function ajax_url() {
			return admin_url( 'admin-ajax.php', 'relative' );
		}
	
		/**
		 * Load Localisation files.
		 */
		  
		public function load_lumia_product_gallery_textdomain() {
			load_plugin_textdomain( LWPG_TEXT_DOMAIN, false, plugin_basename( dirname( __FILE__ ) ) . "/i18n/languages" );
		}
		
		/**
		 * lumia product admin submenu.
		 */
		 
		public function lumia_product_gallery_admin_submenu() {
			
			add_submenu_page( 'edit.php?post_type=product', __( 'Lumia Product Gallery Settings', LWPG_TEXT_DOMAIN ), __( 'Gallery Settings', LWPG_TEXT_DOMAIN ), 'manage_product_terms', 'lumia-product-gallery-settings',  array( &$this, 'lumia_product_gallery_settings' ) );
		}
		
		/**
		 * lumia product gallery settings in admin side.
		 */
		 
		public function lumia_product_gallery_settings () {
			$gallery_settings = '';
			$gallery_settings = get_option( 'lumia_gallery_settings_options' ); 
			
			// saving settings options
			if( $_POST ) {
				
				$data['elevate']['gallery_type'] = $_POST['elevatezoom_gallery_type'];				
				$data['elevate']['zoom_type'] = $_POST['elevatezoom_zoom_type'];
				$data['elevate']['width'] = $_POST['elevatezoom_width'];
				$data['elevate']['height'] = $_POST['elevatezoom_height'];
				$data['elevate']['lens_shape'] = $_POST['elevatezoom_lens_shape'];
				$data['elevate']['bg_color'] = $_POST['elevatezoom_bg_color'];
				$data['elevate']['border_width'] = $_POST['elevatezoom_border_width'];
				$data['elevate']['border_color'] = $_POST['elevatezoom_border_color'];
				
				update_option( 'lumia_gallery_settings_options' , $data );
			}
			
			// varibale for elevateszoom
			$egallery_type = isset( $gallery_settings['elevate']['gallery_type'] ) ? $gallery_settings['elevate']['gallery_type'] : '';
			$ezoom_type = isset( $gallery_settings['elevate']['zoom_type'] ) ? $gallery_settings['elevate']['zoom_type'] : '';
			$ewidth = isset( $gallery_settings['elevate']['width'] ) ? $gallery_settings['elevate']['width'] : '';
			$eheight = isset( $gallery_settings['elevate']['height'] ) ? $gallery_settings['elevate']['height'] : '';
			$elens_shape = isset( $gallery_settings['elevate']['lens_shape'] ) ? $gallery_settings['elevate']['lens_shape'] : '';
			$ebg_color = isset( $gallery_settings['elevate']['bg_color'] ) ? $gallery_settings['elevate']['bg_color'] : '';
			$eborder_width = isset( $gallery_settings['elevate']['border_width'] ) ? $gallery_settings['elevate']['border_width'] : '';
			$eborder_color = isset( $gallery_settings['elevate']['border_color'] ) ? $gallery_settings['elevate']['border_color'] : '';			
			?> 
			<div class="wrap">
				<h2><?php _e( 'Lumia Product Gallery Settings', LWPG_TEXT_DOMAIN );?></h2>
				<form name="product_gallery_form" action="<?php echo admin_url( 'edit.php?post_type=product&page=lumia-product-gallery-settings' ); ?>"  method="post">
					<style>
					.show {
						display:table
					}
					.hide {
						display:none
					} 
					.wp-core-ui input[name="btn-submit"].button-primary { 
						margin-top:20px;
					}
					.widefat input,
					.widefat select {
						width:30%;
						display:inline-block;
						height: 30px;
    					line-height: 30px;
					}
                    </style>					
					<table class="form-table widefa" id="elevatezoom-table">
						<tbody style="width:100%;">
                        	<tr valign="top" style="width:100%;">
								<td scope="row" style="width:20%"><label><?php _e( 'Gallery Type :', LWPG_TEXT_DOMAIN );?></label></td>
								<td scope="row">
									<select name="elevatezoom_gallery_type">
										<option>select gallery type</option>
										<option value="basic" <?php selected( $egallery_type, 'basic' ); ?>>Basic Zoom</option>
										<option value="tints" <?php selected( $egallery_type, 'tints' ); ?>>Tints</option>
                                        <option value="inner" <?php selected( $egallery_type, 'inner' ); ?>>Inner Zoom</option>
                                        <option value="fadein_fadeout" <?php selected( $egallery_type, 'fadein_fadeout' ); ?>>Fade in / Fade Out</option>
                                        <option value="easing" <?php selected( $egallery_type, 'easing' ); ?>>Easing</option>
                                        <option value="mousewheel" <?php selected( $egallery_type, 'mousewheel' ); ?>>Mousewheel Zoom</option>
                                        <option value="image_constrain" <?php selected( $egallery_type, 'image_constrain' ); ?>>Image Constrain</option>
                                        <option value="lens_zoom" <?php selected( $egallery_type, 'lens_zoom' ); ?>>Lens Zoom</option>
									</select>
								</td>
							</tr>
							<tr valign="top" style="width:100%;">
								<td scope="row" style="width:20%"><label><?php _e( 'Zoom Type :', LWPG_TEXT_DOMAIN );?></label></td>
								<td scope="row">
									<select name="elevatezoom_zoom_type">
										<option>select zoom type</option>
										<option value="window" <?php selected( $ezoom_type, 'window' ); ?>>Window</option>
										<option value="lens" <?php selected( $ezoom_type, 'lens' ); ?>>Lens</option>
									</select>
								</td>
							</tr>
                            <tr valign="top">
								<td scope="row" style="width:20%"><label><?php _e( 'Lens Shape :', LWPG_TEXT_DOMAIN );?></label></td>
								<td scope="row">
									<select name="elevatezoom_lens_shape">
										<option>select zoom type</option>
										<option value="square" <?php selected( $elens_shape, 'square' ); ?>>Square</option>
										<option value="round" <?php selected( $elens_shape, 'round' ); ?>>Round</option>
									</select>
								</td>
							</tr>
                            <tr valign="top" style="width:100%;">
								<td scope="row" style="width:20%"><label><?php _e( 'Width :', LWPG_TEXT_DOMAIN );?></label></td>
								<td scope="row">
									<input type="text" name="elevatezoom_width" value="<?php echo $ewidth; ?>" /><?php _e( 'px', LWPG_TEXT_DOMAIN );?>
								</td>
							</tr>
							<tr valign="top">
								<td scope="row" style="width:20%"><label><?php _e( 'Height :', LWPG_TEXT_DOMAIN );?></label></td>
								<td scope="row">
									<input type="text" name="elevatezoom_height" value="<?php echo $eheight; ?>" /><?php _e( 'px', LWPG_TEXT_DOMAIN );?>
								</td>
							</tr>
                            <tr valign="top">
								<td scope="row" style="width:20%"><label><?php _e( 'Background Color :', LWPG_TEXT_DOMAIN );?></label></td>
								<td scope="row">
									<input type="text" name="elevatezoom_bg_color" id="elevatezoom_bg_color" class="color_picker" value="<?php echo $ebg_color; ?>" />
								</td>
							</tr>
                            <tr valign="top">
								<td scope="row" style="width:20%"><label><?php _e( 'Border Colour :', LWPG_TEXT_DOMAIN );?></label></td>
								<td scope="row">
									<input type="text" name="elevatezoom_border_color" id="elevatezoom_border_color" class="color_picker" value="<?php echo $eborder_color; ?>" />
								</td>
							</tr>
							<tr valign="top">
								<td scope="row" style="width:20%"><label><?php _e( 'Border Width :', LWPG_TEXT_DOMAIN );?></label></td>
								<td scope="row">
									<select name="elevatezoom_border_width">
										<option>select width</option>
										<?php for( $i = 0; $i <= 5; $i++ ){ ?>
										<option value="<?php echo $i; ?>" <?php selected( $eborder_width, $i ); ?>><?php echo $i; ?></option>
										<?php } ?>
									</select><?php _e( 'px', LWPG_TEXT_DOMAIN );?>
								</td>
							</tr>
						</tbody>
					</table>
					<input type="submit" value="<?php _e( 'Save Changes', LWPG_TEXT_DOMAIN );?>" class="button button-primary" id="submit" name="btn-submit" />
				</form>
			</div>
			<?php
		}
		
		/**
		 * Remove link wrapping main product image in single product view.
		 * @param $html
		 * @param $post_id
		 * @return string
		 */
		 
		public function lumia_single_product_thumbnails ( $html, $post_id ) {
			global $post, $woocommerce, $product;
			
			$attachment_ids = $product->get_gallery_attachment_ids();

			$html = '<div class="lumia_product_slider"><span class="spinner"></span>';
			$attachment_id = $attachment_ids[0];
	
			$image_catalog_attr = wp_get_attachment_image_src( $attachment_id, apply_filters( 'single_product_small_thumbnail_size', 'shop_catalog' ) );
			$image_full_attr = wp_get_attachment_image_src( $attachment_id, apply_filters( 'single_product_small_thumbnail_size', 'full' ) );
			$html .= '<img src="' . $image_catalog_attr[0] . '" data-zoom-image="' . $image_full_attr[0] . '" />';
			
			$html .= '<div class="clear"></div></div>';
			
			return $html;
		}
		
		/**
		 * get all product thumbnails of main product in single product view.
		 * @param $html
		 * @param $post_id
		 * @return string
		 */
		 
		public function lumia_single_product_image_thumbnails ( $html, $post_id ) {
			
			$html = '';
			
			$image_thumb = wp_get_attachment_image( $post_id, apply_filters( 'single_product_small_thumbnail_size', 'shop_thumbnail' ) );
			$image_catalog = wp_get_attachment_image_src( $post_id, apply_filters( 'single_product_small_thumbnail_size', 'shop_catalog' ) );
			$image_full = wp_get_attachment_image_src( $post_id, apply_filters( 'single_product_small_thumbnail_size', 'full' ) );
			
			$html = '<a data-full="' . $image_full[0] . '" data-catalog="' . $image_catalog[0] . '" href="javascript:;">' . $image_thumb . '</a>';
			
			echo $html;
		}
	}
	
	endif;
	
	
	/**
	 * Returns the main instance of LWooProductGallery to prevent the need to use globals.
	 *
	 * @since  1.0
	 * @return LWooProductGallery
	 */
	 
	function LWPG() {
		return LWooProductGallery::init_instance();
	}
	//return new LWooProductGallery;
	
	// Global for backwards compatibility.
	$GLOBALS['lumia_product_gallery'] = LWPG();
else:
endif;