<?php
if (!defined('WPINC'))
	exit;

class ETC_WP_Theme_Customizer_From_Json {
	
	/**
	 * Theme Customizer Instance
	 * @var object
	 */

	private $wp_customize;
	
	/**
	 * Json File Path
	 *
	 * @var string
	 */

	private $json_path;

	/**
	 * Settings object
	 *
	 * @var objecct
	 */

	private $settings;

	/**
	 * Theme Slug
	 * @var string
	 */

	private $theme_slug;
	
	/**
	 * construct
	 *
	 * @return void
	 */

	public function __construct($fields=array()) {
		$wp_default_fields = array(
			'color' => 'WP_Customize_Color_Control',
			'file' => 'WP_Customize_Upload_Control',
			'image' => 'WP_Customize_Image_Control',
		);

		$this->fields = array_merge($fields, $wp_default_fields);
		$this->json_path = get_option('etc_json_settings', false);
		$this->settings = $this->settings_from_json(apply_filters('etc_json_file', $this->json_path));
		$this->theme_slug = 'theme_mods_' . $this->get_theme_name();
		
		/**
		 * Register action hook
		 *
		 * @see wp-includes/class-wp-theme-customizer-manager.php
		 */
		
		/*
		This action hook allows you to enqueue assets (such as javascript files) directly in the Theme Customizer only.
		To output saved settings onto your live site, you still need to output generated CSS using the wp_head hook.
		*/
		// add_action( 'customize_preview_init', array( $this, 'customize_preview' ) );
		
		/*
		This hook allows you define new Theme Customizer sections, settings, and controls.
		*/
		add_action('customize_register', array($this, 'register_customizer'), 99, 1);
		
		/*
		This hook allows you to output custom-generated CSS so that your changes show up correctly on the live website.
		*/
		if($this->get_css_hook()) add_action('wp_head', $this->get_css_hook());
	}
	
	/**
	 * Get 
	 *
	 * @param  string $json_path
	 * @return object
	 */

	public static function get_theme_settings() {
		$instance = new self;
		return $instance->get_settings();
	}
	
	/**
	 * Register Json File
	 *
	 * @param  string $json_path
	 * @return object
	 */

	public function settings_from_json($json_path) {
		
		return json_decode(file_get_contents(($json_path && $this->json_exists($json_path)) ? $json_path : ETC_DEFAULT_JSON));
		
	}
	
	/**
	 * Check file existence
	 *
	 * @param  string $json_path
	 * @return boolean
	 */

	private function json_exists($json_path) {
		if (filter_var($json_path, FILTER_VALIDATE_URL))
			return @file_get_contents($json_path);
		else
			return file_exists($json_path);
	}
	
	/**
	 * slug getter
	 *
	 * @return object
	 */

	public function get_slug() {
		
		return $this->theme_slug;
		
	}

	/**
	 * settings getter
	 *
	 * @return object
	 */

	public function get_settings() {
		
		return $this->settings;
		
	}

	/**
	 * default setting getter
	 *
	 * @return object
	 */

	public function get_default($setting) {

		return $this->get_defaults($setting);
		
	}

	/**
	 * defaults settings getter
	 *
	 * @return object
	 */

	public function get_defaults($get=false) {
		$defaults = array();
		
		foreach ( $this->settings->sections as $section_key => $settings ) :
			foreach ( $settings->setting as $setting_key => $setting ):
				if ($get && $setting_key == $get):
					return $setting->default ? $setting->default : false;
				else:
					$defaults[$setting_key] = $setting->default;
				endif;
			endforeach;
		endforeach;

		return $get ? false : $defaults;
		
	}
	
	/**
	 * Register Theme Customizer
	 *
	 * @return void
	 */

	public function register_customizer($wp_customize) {
		$this->wp_customize = $wp_customize;

		do_action('etc_customizer_before', $this->wp_customize);

		// keep or remove Wordpress defaults customizers
		// http://codex.wordpress.org/Theme_Customization_API

		$wp_defaults = array(
			'title_tagline',
			'colors',
			'header_image',
			'background_image',
			'nav',
			'static_front_page'
		);

		foreach($this->settings->wp_defaults as $def => $value):
			if (!$value)
				$this->wp_customize->remove_section($def);
		endforeach;
		
		// now add new settings
		
		foreach ($this->settings->sections as $section_name => $section):
			// register section
			if(!in_array($section_name, $wp_defaults))
				$this->add_section($section_name, $section->title, $section->priority);

			foreach ($section->setting as $setting_name => $settings):
				// register setting
				$this->add_setting($setting_name, $settings);
				
				// register control
				$this->add_control($section_name, $setting_name, $settings);
			endforeach;
		endforeach;
		
		do_action('etc_customizer_after', $this->wp_customize);
		
	}
	
	/**
	 * Enhanced Theme Slug
	 *
	 * @return string template name
	 */
	
	private function get_theme_name() {
		
		if (get_template() == get_stylesheet()) {
			$theme_name = get_template();
		} else {
			$theme_name = get_stylesheet();
		}
		
		return apply_filters('etc_theme_name', $theme_name);
		
	}

	/**
	 * Get if type is wp default
	 *
	 * @return string
	 */
	
	private function is_wp_default($type) {
		
		$wp_defaults = array(
			'text',
			'email',
			'url',
			'date',
			'checkbox',
			'radio',
			'select',
			'dropdown-pages',
			'textarea'
		);

		return in_array($type, $wp_defaults) ? $type : false;	
	}
	
	
	
	/**
	 * Set Section Value
	 *
	 * @param string $title
	 * @param int    $priority
	 * @return array
	 */
	
	private function set_section($title = '', $priority) {
		
		if (!$priority)
			$priority = 0;
		
		return array(
			'title' => $title,
			'priority' => $priority
		);
		
	}
	
	/**
	 * Set Setting Value
	 *
	 * @param array $settings
	 *
	 * @return array setting argment
	 */
	
	private function set_setting($settings) {
		
		$setting_arg               = array();
		$setting_arg['capability'] = $this->settings->general->capability;
		$setting_arg               = $this->deploy_settings($setting_arg, $settings, array(
			'label',
			'choices'
		));

		$setting_arg['type'] = 'option';

		if ('color' === $setting_arg['type']) {
			$setting_arg['sanitize_callback'] = 'sanitize_hex_color';
		}
		
		return $setting_arg;
		
	}
	
	/**
	 * Set Control Value
	 *
	 * @param string $section_name
	 * @param object $settings
	 *
	 * @return array formated array value
	 */
	
	public function set_control($section_name, $setting_name, $settings) {
		
		$setting_arg             = array();
		$setting_arg['section']  = $section_name;
		$setting_arg['settings'] = $this->theme_slug . '[' . $setting_name . ']';
		
		$setting_arg = $this->deploy_settings($setting_arg, $settings, array(
			'transport',
			'capability',
			'default',
			'type',
			'target'
		));

		if($this->is_wp_default($settings->type))
			$setting_arg['type'] = $settings->type;

		return $setting_arg;
		
	}
	
	/**
	 * Control & Setting Value formater
	 *
	 * @param  object $setting_arg
	 * @param  object $settings
	 * @param  array  $exclude_keys
	 *
	 * @return array / formated  array value
	 */
	
	private function deploy_settings($setting_arg, $settings, $exclude_keys = array()) {
		
		foreach ($settings as $setting_key => $setting) {
			
			if ('choices' == $setting_key) {
				
				foreach ($setting as $choise_key => $choise) {
					
					$setting_arg['choices'][$choise_key] = $choise;
					
				}
				
			} elseif (in_array($setting_key, $exclude_keys)) {
				
				unset($setting_arg[$setting_key]);
				
				continue;
				
			} else {
				
				$setting_arg[$setting_key] = $setting;
				
			}
			
		}
		
		return $setting_arg;
		
	}
	
	/**
	 * Add Section to WP Theme Customizer
	 *
	 * @param string  $section_name
	 * @param string  $title
	 * @param integer $priority
	 *
	 * @return void
	 */
	
	private function add_section($section_name, $title, $priority = 0) {
		
		$this->wp_customize->add_section($section_name, $this->set_section($title, $priority));
		
	}
	
	/**
	 * Add Setting to WP Theme Customizer
	 *
	 * @param string $setting_name
	 * @param object $settings
	 *
	 * @return void
	 */
	
	private function add_setting($setting_name, $settings) {
		$this->wp_customize->add_setting($this->theme_slug . '[' . $setting_name . ']', $this->set_setting($settings));

	}
	
	/**
	 * Add Control to WP Theme Customizer
	 *
	 * @param string $section_name
	 * @param string $setting_name
	 * @param object $settings
	 *
	 * @return void
	 */
	
	private function add_control($section_name, $setting_name, $settings) {
		
		// Defaults Wordpress types using simple control

		if($this->is_wp_default($settings->type))
			$this->wp_customize->add_control($setting_name, $this->set_control($section_name, $setting_name, $settings));
		
		// Types using special class

		foreach ($this->fields as $field => $class) {
			if ($field == $settings->type) {
				$this->wp_customize->add_control(new $class($this->wp_customize, $setting_name, $this->set_control($section_name, $setting_name, $settings)));
				break;
			}
		}

		do_action('etc_register_control_type', $section_name, $setting_name, $settings);
		
	}

	/**
	 * Get CSS hook if defined in JSON
	 *
	 * @return css_hook name or false
	 */

	public function get_css_hook() {

		return isset($this->settings->general->css_hook) ? $this->settings->general->css_hook : false;

	}
}
