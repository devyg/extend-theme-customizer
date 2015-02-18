<?php
if ( !class_exists( 'WP_Customize_Control' ) )
	return NULL;

/**
 * Class to create a custom layout control with 3 columns
 */
class Layout_Picker_Header_Custom_Control extends WP_Customize_Control {
	public function __construct($manager, $id, $args) {
		$args['choices'] = array(
			'logo-and-descr' => __( 'Logo and description', 'extend-theme-customizer' ),
			'logo-left' => __( 'Logo only (left)', 'extend-theme-customizer' ),
			'logo-center' => __( 'Logo only (center)', 'extend-theme-customizer' ),
			'descr-left' => __( 'Description only (left)', 'extend-theme-customizer' ),
			'descr-center' => __( 'Description only (center)', 'extend-theme-customizer' ),
			'none' => __( 'Nothing', 'extend-theme-customizer' ),
		);

		parent::__construct($manager, $id, $args);
	}

	/**
	* Render the content on the theme customizer page
	*/
	public function render_content() {
		$name = 'etc-radio-img-' . $this->id;
		?>
		<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
		<ul>
		<?php foreach ( $this->choices as $value => $alt ) : ?>
			<li>
				<label class="etc-radio-img">
					<input type="radio" value="<?php echo esc_attr( $value ); ?>" name="<?php echo esc_attr( $name ); ?>" <?php $this->link(); checked( $this->value(), $value ); ?> />
					<img src="<?php echo plugins_url( '/img/header-'.$value.'.png', __FILE__ ); ?>" alt="<?php echo esc_attr( $alt ); ?>" />
				</label>
			</li>
		<?php endforeach; ?>
		</ul>
		<?php
	}

}
