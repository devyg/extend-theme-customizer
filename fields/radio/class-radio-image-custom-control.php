<?php
if ( !class_exists( 'WP_Customize_Control' ) )
	return NULL;

/**
 * Class to create a custom layout control with 3 columns
 */
class Radio_Image_Custom_Control extends WP_Customize_Control {
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
					<img src="<?php echo get_template_directory_uri() . '/' . $alt; ?>" alt="img" />
				</label>
			</li>
		<?php endforeach; ?>
		</ul>
		<?php
	}
}
