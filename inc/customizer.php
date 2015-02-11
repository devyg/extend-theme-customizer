<?php
if (!defined('WPINC'))
	exit;

class ETC_Theme_Customizer {
	
	/**
	 * Plugin Slug
	 * @var string
	 */
	private $plugin_slug = 'extend-theme-customizer';
	
	/**
	 * Theme Customizer Setting ID
	 * @var array
	 */
	private $setting_id = array();
	
	/**
	 * Instance
	 * @var [type]
	 */
	private static $instance;

	/**
	 * Customizer
	 * @var ETC_WP_Theme_Customizer_From_Json
	 */
	private $customizer;

	/**
	 * Settings
	 * @var object
	 */
	private $settings;
	
	/**
	 * construct
	 */
	public function __construct() {
		
		// Require Fields File
		$support_fields = array(
			'date' => array(
				'date-picker'
			),
			'image' => array(
				'multi-image'
			),
			'layout' => array(
				'layout-picker'
			),
			'select' => array(
				'category-dropdown',
				'google-font-dropdown',
				'menu-dropdown',
				'post-dropdown',
				'post-type-dropdown',
				'tags-dropdown',
				'taxonomy-dropdown',
				'user-dropdown'
			),
			'text' => array(
				'text-editor',
				'textarea'
			)
		);
		
		$support_fields = apply_filters('etc_support_files', $support_fields);
		
		foreach ($support_fields as $dir => $field_slugs) {
			foreach ($field_slugs as $slug) {
				require_once(ETC_BASE_DIR . '/fields/' . $dir . '/' . 'class-' . $slug . '-custom-control.php');
			}
		}
		
		add_action('init', array( $this, 'initialize_customizer' ));
		
	}
	
	/**
	 * Get Instance
	 *
	 * @return object
	 */
	public static function get_instance() {
		if (null == self::$instance) {
			self::$instance = new self;
		}
		return self::$instance;
	}
	
	/**
	 * Get Plugin Slug
	 *
	 * @return string
	 */
	public function get_plugin_slug() {
		
		return $this->plugin_slug;
		
	}

	/**
	 * Get customizer instance
	 *
	 * @return string
	 */
	public function get_customizer() {
		
		return $this->customizer;
		
	}
	
	/**
	 * Initialize customizer
	 * @return array $setting_id :
	 */
	
	public function initialize_customizer() {
		
		$setting_id     = array();
		$this->customizer  = new ETC_WP_Theme_Customizer_From_Json();
		
		foreach ($this->customizer->get_settings()->sections as $section_key => $section) {
			
			foreach ($section->setting as $setting_key => $setting) {
				$setting_id[] = $setting_key;
			}
			
		}
		
		return $setting_id;
		
	}
	
}
