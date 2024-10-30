<?php
/*
*
* Plugin Name: Mona HERE map Embed
* Plugin URI: https://mona-media.com/project/mona-map-embed-wordpress-plugin/
* Description: This simple but powerful plugin will help you sharing information about your favorite places and to display maps.
* Author: Mona Media
* Author URI: https://mona-media.com/
* Version: 1.0
* Text Domain: mona-here-map-embed
*
*/
defined('ABSPATH') or die('No script kiddies please!');

if (!class_exists('Mona_Heremap_Embed')){
	class Mona_Heremap_Embed {	
		protected $domain = 'mona-here-map-embed',
		$api_core = 'https://js.api.here.com/v3/3.0/mapsjs-core.js',
		$api_service = 'https://js.api.here.com/v3/3.0/mapsjs-service.js',
		$api_events = 'http://js.api.here.com/v3/3.0/mapsjs-mapevents.js',
		$api_places = 'http://js.api.here.com/v3/3.0/mapsjs-places.js',
		$api_ui = 'http://js.api.here.com/v3/3.0/mapsjs-ui.js',
		$api_style_ui = 'http://js.api.here.com/v3/3.0/mapsjs-ui.css';
	
		/**
		 * Class Construct
		 */
		public function __construct(){	
			$this->url = trailingslashit(plugin_dir_url(__FILE__));
			$this->dir = trailingslashit(plugin_dir_path(__FILE__));
			
			// header	
			add_action('wp_head', array($this, 'mona_render_heremap_api'));
			
			// settings page
			add_action('admin_menu', array($this, 'plugin_settings_page'));
			
			//shortcode
			add_shortcode('mona_heremap', array($this, 'mona_shortcode_heremap'));	
		}
		
		/**
		 * Function settings
	 	 */		
		function get_plugin_meta(){
			return get_plugin_data($this->dir.$this->domain.'.php', false);
		}
		
		function get_plugin_attr($attr = ''){
			$meta = $this->get_plugin_meta();
			
			return (isset($meta[$attr])) ? $meta[$attr] : '';
		}
		
		function get_settings(){
			$default = $this->get_settings_default();
			$option = get_option($this->domain, $default);
			
			return array_merge($default, $option);
		}
		
		function get_settings_default(){
			return array(
				'api_key' => '',
				'api_secret' => '',
				'is_ssl' => 0,
			);
		}		
		function set_settings($data){			
			return update_option($this->domain, $data);
		}
		
		/**
		 * Shortcode
	  	 */
		function mona_shortcode_heremap($atts = array()){
			ob_start();
			
			extract( 
				shortcode_atts( 
					array(
						'lat' => 10.77707,
						'lng' => 106.65482,
						'address' => '',
						'zoom' => 15,
						'height' => '300px',
						'text' => '',
						'icon' => '',
                        'draggable' => true,
					), 
					$atts, 
					'mona_heremap'
				)			
			);
			
			$this->mona_render_heremap(
				array(
					'lat' => $lat,
					'lng' => $lng,
					'address' => $address,
					'zoom' => $zoom,
					'height' => $height,
					'text' => $text,
					'icon' => $icon,
					'draggable' => $draggable,
				)
			);
			
			return ob_get_clean();
		}
		
		/**
		 * Function pages
		 */	
		function plugin_settings_page(){
			$plugin_settings = $this->get_settings();
			
			add_menu_page(__('Mona HERE map'), __('Mona HERE map'), 'manage_options', $this->domain, array($this, 'plugin_layout_options'), 'dashicons-admin-site');
		}
		
		function plugin_layout($name){
			$template_file = $this->dir.'templates/'.$name.'.php';
			
			if (file_exists($template_file))
				load_template($template_file);
		}
		
		function plugin_layout_options(){
			global $plugin_meta, $plugin_settings;
			
			$plugin_meta = $this->get_plugin_meta();
			$plugin_settings = $this->get_settings();
			
			if($_SERVER['REQUEST_METHOD'] == 'POST'){
				$action = @$_POST['action'];
				
				if($action == 'api'){
					$plugin_settings['api_key'] = @$_POST['api_key']; 
					$plugin_settings['api_secret'] = @$_POST['api_secret'];
					$plugin_settings['is_ssl'] = @$_POST['is_ssl'];
					
					$this->set_settings($plugin_settings);					
				}
			}
			
			$this->plugin_layout_header();
			
			$this->plugin_layout('home');		
			
			$this->plugin_layout_footer();
		}
		
		function plugin_layout_header(){
			global $plugin_meta, $plugin_settings;		
			
			// css
			wp_enqueue_style($this->domain.'-bootstrap', $this->url.'assets/css/bootstrap.css', array(), '3.1.1', 'all');
			wp_enqueue_style($this->domain.'-style', $this->url.'assets/css/style.css', array(), $plugin_meta['Version'], 'all');
			
			// js
			wp_enqueue_script($this->domain.'-bootstrap', $this->url.'assets/js/bootstrap.min.js', array(), '3.1.1', true);
		
			$this->plugin_layout('header');
		}
		
		function plugin_layout_footer(){
			global $plugin_domain;
			
			$this->plugin_layout('footer');
		}
		
		/**
		 * Render
		 */
		function mona_render_heremap($args = array()){
			// css
			wp_enqueue_style($this->domain.'-here-ui', $this->api_style_ui, array(), '', 'all');
			
			// js
			wp_enqueue_script($this->domain.'-here-core', $this->api_core, array(), '', true);
			wp_enqueue_script($this->domain.'-here-service', $this->api_service, array(), '', true);
			wp_enqueue_script($this->domain.'-here-ui', $this->api_ui, array(), '', true);
			wp_enqueue_script($this->domain.'-here-places', $this->api_places, array(), '', true);
			wp_enqueue_script($this->domain.'-here-events', $this->api_events, array(), '', true);
			wp_enqueue_script($this->domain.'-script', $this->url.'assets/js/script.js', array(), $plugin_meta['Version'], true);
			
			$style = 'height: '.$args['height'].';';
			
			if(@$args['address'] != ''){
				echo '<div class="mona-here-map mona-here-map-address" data-address="'.esc_attr($args['address']).'" data-zoom="'.$args['zoom'].'" data-text="'.esc_attr($args['text']).'" data-icon="'.$args['icon'].'" data-draggable="'.$args['draggable'].'" style="'.$style.'"></div>';
			}else{
				echo '<div class="mona-here-map" data-lat="'.$args['lat'].'" data-lng="'.$args['lng'].'" data-zoom="'.$args['zoom'].'" data-text="'.esc_attr($args['text']).'" data-icon="'.$args['icon'].'" data-draggable="'.$args['draggable'].'" style="'.$style.'"></div>';
			}
		}
		function mona_render_heremap_api(){
			$plugin_settings = $this->get_settings(); ?>
			<script type="text/javascript">
			var here_api = {
				app_id: '<?php echo $plugin_settings['api_key']; ?>',
				app_code: '<?php echo $plugin_settings['api_secret']; ?>',
				useHTTPS: <?php echo ($plugin_settings['is_ssl'] == 1); ?>
			};
			</script>
		<?php }
	}
	new Mona_Heremap_Embed();
}