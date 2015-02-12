<?php
if ( ! class_exists( 'WP_Customize_Control' ) ){
	return NULL;
}

/**
 * Class to create a custom layout control with 3 columns
 */
class Layout_Picker_Three_Custom_Control extends WP_Customize_Control {
	public function __construct($manager, $id, $args) {
		$args['choices'] = array(
			'left' => __( 'Left', 'extend-theme-customizer' ),
			'right' => __( 'Right', 'extend-theme-customizer' ),
			'full' => __( 'Full', 'extend-theme-customizer' ),
		);

		parent::__construct($manager, $id, $args);
	}

	/**
	* Render the content on the theme customizer page
	*/
	public function render_content() {
		$name = 'ci-theme-customize-radio-' . $this->id;
		?>
		<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
		<ul>
		<?php foreach ( $this->choices as $value => $alt ) : ?>
			<li>
				<label>
					<input type="radio" value="<?php echo esc_attr( $value ); ?>" name="<?php echo esc_attr( $name ); ?>" <?php $this->link(); checked( $this->value(), $value ); ?> />
					<img src="<?php echo plugins_url( '/img/'.$value.'.gif', __FILE__ ); ?>" alt="<?php echo esc_attr( $alt ); ?>" />
				</label>
			</li>
		<?php endforeach; ?>
		</ul>
		<?php
	}

}
