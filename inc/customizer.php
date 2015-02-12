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
	 * Instance
	 * @var ETC_Theme_Customizer
	 */

	private static $instance;

	/**
	 * Customizer
	 * @var ETC_WP_Theme_Customizer_From_Json
	 */

	private $customizer;

	/**
	 * Suppoerted fields list
	 * @var array
	 */

	private $support_fields;
	
	/**
	 * construct
	 */

	public function __construct() {
		
		// Require Fields File
		$this->support_fields = array(
			'date' => array(
				'date-picker' => 'Date_Picker_Custom_Control'
			),
			'image' => array(
				'multi-image' => 'Multi_Image_Custom_Control'
			),
			'layout' => array(
				'layout-picker-3' => 'Layout_Picker_Three_Custom_Control',
				'layout-picker-4' => 'Layout_Picker_Four_Custom_Control'
			),
			'select' => array(
				'category-dropdown' => 'Category_Dropdown_Custom_Control',
				'google-font-dropdown' => 'Google_Font_Dropdown_Custom_Control',
				'menu-dropdown' => 'Menu_Dropdown_Custom_Control',
				'post-dropdown' => 'Post_Dropdown_Custom_Control',
				'post-type-dropdown' => 'Post_Type_Dropdown_Custom_Control',
				'tags-dropdown' => 'Tags_Dropdown_Custom_Control',
				'taxonomy-dropdown' => 'Taxonomy_Dropdown_Custom_Control',
				'user-dropdown' => 'User_Dropdown_Custom_Control'
			),
			'text' => array(
				'text-editor' => 'Text_Editor_Custom_Control',
			)
		);
		
		$this->support_fields = apply_filters('etc_support_files', $this->support_fields);
		
		foreach ($this->support_fields as $fields_familly => $fields_names) {
			foreach ($fields_names as $field_slug => $field_class) {
				require_once(ETC_BASE_DIR . '/fields/' . $fields_familly . '/' . 'class-' . $field_slug . '-custom-control.php');
			}
		}
		
		// Runs after WordPress has finished loading but before any headers are sent.
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
	 * Get fields with their associated clases
	 *
	 * @return string
	 */

	private function get_fields() {

		$fields = array();

		foreach ($this->support_fields as $fields_familly => $fields_names) {
			$fields = array_merge($fields, $fields_names);
		}

		return $fields;
		
	}
	
	/**
	 * Initialize customizer
	 *
	 * @return nothing
	 */

	public function initialize_customizer() {

		// Init customizer
		$this->customizer  = new ETC_WP_Theme_Customizer_From_Json($this->get_fields());
	
	}

}