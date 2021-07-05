<?php
/**
 * Typography control class.
 *
 * @since  1.0.0
 * @access public
 */

class VW_Ecommerce_Store_Control_Typography extends WP_Customize_Control {

	/**
	 * The type of customize control being rendered.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $type = 'typography';

	/**
	 * Array 
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $l10n = array();

	/**
	 * Set up our control.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  object  $manager
	 * @param  string  $id
	 * @param  array   $args
	 * @return void
	 */
	public function __construct( $manager, $id, $args = array() ) {

		// Let the parent class do its thing.
		parent::__construct( $manager, $id, $args );

		// Make sure we have labels.
		$this->l10n = wp_parse_args(
			$this->l10n,
			array(
				'color'       => esc_html__( 'Font Color', 'vw-ecommerce-store' ),
				'family'      => esc_html__( 'Font Family', 'vw-ecommerce-store' ),
				'size'        => esc_html__( 'Font Size',   'vw-ecommerce-store' ),
				'weight'      => esc_html__( 'Font Weight', 'vw-ecommerce-store' ),
				'style'       => esc_html__( 'Font Style',  'vw-ecommerce-store' ),
				'line_height' => esc_html__( 'Line Height', 'vw-ecommerce-store' ),
				'letter_spacing' => esc_html__( 'Letter Spacing', 'vw-ecommerce-store' ),
			)
		);
	}

	/**
	 * Enqueue scripts/styles.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function enqueue() {
		wp_enqueue_script( 'vw-ecommerce-store-ctypo-customize-controls' );
		wp_enqueue_style(  'vw-ecommerce-store-ctypo-customize-controls' );
	}

	/**
	 * Add custom parameters to pass to the JS via JSON.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function to_json() {
		parent::to_json();

		// Loop through each of the settings and set up the data for it.
		foreach ( $this->settings as $setting_key => $setting_id ) {

			$this->json[ $setting_key ] = array(
				'link'  => $this->get_link( $setting_key ),
				'value' => $this->value( $setting_key ),
				'label' => isset( $this->l10n[ $setting_key ] ) ? $this->l10n[ $setting_key ] : ''
			);

			if ( 'family' === $setting_key )
				$this->json[ $setting_key ]['choices'] = $this->get_font_families();

			elseif ( 'weight' === $setting_key )
				$this->json[ $setting_key ]['choices'] = $this->get_font_weight_choices();

			elseif ( 'style' === $setting_key )
				$this->json[ $setting_key ]['choices'] = $this->get_font_style_choices();
		}
	}

	/**
	 * Underscore JS template to handle the control's output.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function content_template() { ?>

		<# if ( data.label ) { #>
			<span class="customize-control-title">{{ data.label }}</span>
		<# } #>

		<# if ( data.description ) { #>
			<span class="description customize-control-description">{{{ data.description }}}</span>
		<# } #>

		<ul>

		<# if ( data.family && data.family.choices ) { #>

			<li class="typography-font-family">

				<# if ( data.family.label ) { #>
					<span class="customize-control-title">{{ data.family.label }}</span>
				<# } #>

				<select {{{ data.family.link }}}>

					<# _.each( data.family.choices, function( label, choice ) { #>
						<option value="{{ choice }}" <# if ( choice === data.family.value ) { #> selected="selected" <# } #>>{{ label }}</option>
					<# } ) #>

				</select>
			</li>
		<# } #>

		<# if ( data.weight && data.weight.choices ) { #>

			<li class="typography-font-weight">

				<# if ( data.weight.label ) { #>
					<span class="customize-control-title">{{ data.weight.label }}</span>
				<# } #>

				<select {{{ data.weight.link }}}>

					<# _.each( data.weight.choices, function( label, choice ) { #>

						<option value="{{ choice }}" <# if ( choice === data.weight.value ) { #> selected="selected" <# } #>>{{ label }}</option>

					<# } ) #>

				</select>
			</li>
		<# } #>

		<# if ( data.style && data.style.choices ) { #>

			<li class="typography-font-style">

				<# if ( data.style.label ) { #>
					<span class="customize-control-title">{{ data.style.label }}</span>
				<# } #>

				<select {{{ data.style.link }}}>

					<# _.each( data.style.choices, function( label, choice ) { #>

						<option value="{{ choice }}" <# if ( choice === data.style.value ) { #> selected="selected" <# } #>>{{ label }}</option>

					<# } ) #>

				</select>
			</li>
		<# } #>

		<# if ( data.size ) { #>

			<li class="typography-font-size">

				<# if ( data.size.label ) { #>
					<span class="customize-control-title">{{ data.size.label }} (px)</span>
				<# } #>

				<input type="number" min="1" {{{ data.size.link }}} value="{{ data.size.value }}" />

			</li>
		<# } #>

		<# if ( data.line_height ) { #>

			<li class="typography-line-height">

				<# if ( data.line_height.label ) { #>
					<span class="customize-control-title">{{ data.line_height.label }} (px)</span>
				<# } #>

				<input type="number" min="1" {{{ data.line_height.link }}} value="{{ data.line_height.value }}" />

			</li>
		<# } #>

		<# if ( data.letter_spacing ) { #>

			<li class="typography-letter-spacing">

				<# if ( data.letter_spacing.label ) { #>
					<span class="customize-control-title">{{ data.letter_spacing.label }} (px)</span>
				<# } #>

				<input type="number" min="1" {{{ data.letter_spacing.link }}} value="{{ data.letter_spacing.value }}" />

			</li>
		<# } #>

		</ul>
	<?php }

	/**
	 * Returns the available fonts.  Fonts should have available weights, styles, and subsets.
	 *
	 * @todo Integrate with Google fonts.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	public function get_fonts() { return array(); }

	/**
	 * Returns the available font families.
	 *
	 * @todo Pull families from `get_fonts()`.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	function get_font_families() {

		return array(
			'' => __( 'No Fonts', 'vw-ecommerce-store' ),
        'Abril Fatface' => __( 'Abril Fatface', 'vw-ecommerce-store' ),
        'Acme' => __( 'Acme', 'vw-ecommerce-store' ),
        'Anton' => __( 'Anton', 'vw-ecommerce-store' ),
        'Architects Daughter' => __( 'Architects Daughter', 'vw-ecommerce-store' ),
        'Arimo' => __( 'Arimo', 'vw-ecommerce-store' ),
        'Arsenal' => __( 'Arsenal', 'vw-ecommerce-store' ),
        'Arvo' => __( 'Arvo', 'vw-ecommerce-store' ),
        'Alegreya' => __( 'Alegreya', 'vw-ecommerce-store' ),
        'Alfa Slab One' => __( 'Alfa Slab One', 'vw-ecommerce-store' ),
        'Averia Serif Libre' => __( 'Averia Serif Libre', 'vw-ecommerce-store' ),
        'Bangers' => __( 'Bangers', 'vw-ecommerce-store' ),
        'Boogaloo' => __( 'Boogaloo', 'vw-ecommerce-store' ),
        'Bad Script' => __( 'Bad Script', 'vw-ecommerce-store' ),
        'Bitter' => __( 'Bitter', 'vw-ecommerce-store' ),
        'Bree Serif' => __( 'Bree Serif', 'vw-ecommerce-store' ),
        'BenchNine' => __( 'BenchNine', 'vw-ecommerce-store' ),
        'Cabin' => __( 'Cabin', 'vw-ecommerce-store' ),
        'Cardo' => __( 'Cardo', 'vw-ecommerce-store' ),
        'Courgette' => __( 'Courgette', 'vw-ecommerce-store' ),
        'Cherry Swash' => __( 'Cherry Swash', 'vw-ecommerce-store' ),
        'Cormorant Garamond' => __( 'Cormorant Garamond', 'vw-ecommerce-store' ),
        'Crimson Text' => __( 'Crimson Text', 'vw-ecommerce-store' ),
        'Cuprum' => __( 'Cuprum', 'vw-ecommerce-store' ),
        'Cookie' => __( 'Cookie', 'vw-ecommerce-store' ),
        'Chewy' => __( 'Chewy', 'vw-ecommerce-store' ),
        'Days One' => __( 'Days One', 'vw-ecommerce-store' ),
        'Dosis' => __( 'Dosis', 'vw-ecommerce-store' ),
        'Droid Sans' => __( 'Droid Sans', 'vw-ecommerce-store' ),
        'Economica' => __( 'Economica', 'vw-ecommerce-store' ),
        'Fredoka One' => __( 'Fredoka One', 'vw-ecommerce-store' ),
        'Fjalla One' => __( 'Fjalla One', 'vw-ecommerce-store' ),
        'Francois One' => __( 'Francois One', 'vw-ecommerce-store' ),
        'Frank Ruhl Libre' => __( 'Frank Ruhl Libre', 'vw-ecommerce-store' ),
        'Gloria Hallelujah' => __( 'Gloria Hallelujah', 'vw-ecommerce-store' ),
        'Great Vibes' => __( 'Great Vibes', 'vw-ecommerce-store' ),
        'Handlee' => __( 'Handlee', 'vw-ecommerce-store' ),
        'Hammersmith One' => __( 'Hammersmith One', 'vw-ecommerce-store' ),
        'Inconsolata' => __( 'Inconsolata', 'vw-ecommerce-store' ),
        'Indie Flower' => __( 'Indie Flower', 'vw-ecommerce-store' ),
        'IM Fell English SC' => __( 'IM Fell English SC', 'vw-ecommerce-store' ),
        'Julius Sans One' => __( 'Julius Sans One', 'vw-ecommerce-store' ),
        'Josefin Slab' => __( 'Josefin Slab', 'vw-ecommerce-store' ),
        'Josefin Sans' => __( 'Josefin Sans', 'vw-ecommerce-store' ),
        'Kanit' => __( 'Kanit', 'vw-ecommerce-store' ),
        'Lobster' => __( 'Lobster', 'vw-ecommerce-store' ),
        'Lato' => __( 'Lato', 'vw-ecommerce-store' ),
        'Lora' => __( 'Lora', 'vw-ecommerce-store' ),
        'Libre Baskerville' => __( 'Libre Baskerville', 'vw-ecommerce-store' ),
        'Lobster Two' => __( 'Lobster Two', 'vw-ecommerce-store' ),
        'Merriweather' => __( 'Merriweather', 'vw-ecommerce-store' ),
        'Monda' => __( 'Monda', 'vw-ecommerce-store' ),
        'Montserrat' => __( 'Montserrat', 'vw-ecommerce-store' ),
        'Muli' => __( 'Muli', 'vw-ecommerce-store' ),
        'Marck Script' => __( 'Marck Script', 'vw-ecommerce-store' ),
        'Noto Serif' => __( 'Noto Serif', 'vw-ecommerce-store' ),
        'Open Sans' => __( 'Open Sans', 'vw-ecommerce-store' ),
        'Overpass' => __( 'Overpass', 'vw-ecommerce-store' ),
        'Overpass Mono' => __( 'Overpass Mono', 'vw-ecommerce-store' ),
        'Oxygen' => __( 'Oxygen', 'vw-ecommerce-store' ),
        'Orbitron' => __( 'Orbitron', 'vw-ecommerce-store' ),
        'Patua One' => __( 'Patua One', 'vw-ecommerce-store' ),
        'Pacifico' => __( 'Pacifico', 'vw-ecommerce-store' ),
        'Padauk' => __( 'Padauk', 'vw-ecommerce-store' ),
        'Playball' => __( 'Playball', 'vw-ecommerce-store' ),
        'Playfair Display' => __( 'Playfair Display', 'vw-ecommerce-store' ),
        'PT Sans' => __( 'PT Sans', 'vw-ecommerce-store' ),
        'Philosopher' => __( 'Philosopher', 'vw-ecommerce-store' ),
        'Permanent Marker' => __( 'Permanent Marker', 'vw-ecommerce-store' ),
        'Poiret One' => __( 'Poiret One', 'vw-ecommerce-store' ),
        'Quicksand' => __( 'Quicksand', 'vw-ecommerce-store' ),
        'Quattrocento Sans' => __( 'Quattrocento Sans', 'vw-ecommerce-store' ),
        'Raleway' => __( 'Raleway', 'vw-ecommerce-store' ),
        'Rubik' => __( 'Rubik', 'vw-ecommerce-store' ),
        'Rokkitt' => __( 'Rokkitt', 'vw-ecommerce-store' ),
        'Russo One' => __( 'Russo One', 'vw-ecommerce-store' ),
        'Righteous' => __( 'Righteous', 'vw-ecommerce-store' ),
        'Slabo' => __( 'Slabo', 'vw-ecommerce-store' ),
        'Source Sans Pro' => __( 'Source Sans Pro', 'vw-ecommerce-store' ),
        'Shadows Into Light Two' => __( 'Shadows Into Light Two', 'vw-ecommerce-store'),
        'Shadows Into Light' => __( 'Shadows Into Light', 'vw-ecommerce-store' ),
        'Sacramento' => __( 'Sacramento', 'vw-ecommerce-store' ),
        'Shrikhand' => __( 'Shrikhand', 'vw-ecommerce-store' ),
        'Tangerine' => __( 'Tangerine', 'vw-ecommerce-store' ),
        'Ubuntu' => __( 'Ubuntu', 'vw-ecommerce-store' ),
        'VT323' => __( 'VT323', 'vw-ecommerce-store' ),
        'Varela Round' => __( 'Varela Round', 'vw-ecommerce-store' ),
        'Vampiro One' => __( 'Vampiro One', 'vw-ecommerce-store' ),
        'Vollkorn' => __( 'Vollkorn', 'vw-ecommerce-store' ),
        'Volkhov' => __( 'Volkhov', 'vw-ecommerce-store' ),
        'Yanone Kaffeesatz' => __( 'Yanone Kaffeesatz', 'vw-ecommerce-store' )
		);
	}

	/**
	 * Returns the available font weights.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	public function get_font_weight_choices() {

		return array(
			'' => esc_html__( 'No Fonts weight', 'vw-ecommerce-store' ),
			'100' => esc_html__( 'Thin',       'vw-ecommerce-store' ),
			'300' => esc_html__( 'Light',      'vw-ecommerce-store' ),
			'400' => esc_html__( 'Normal',     'vw-ecommerce-store' ),
			'500' => esc_html__( 'Medium',     'vw-ecommerce-store' ),
			'700' => esc_html__( 'Bold',       'vw-ecommerce-store' ),
			'900' => esc_html__( 'Ultra Bold', 'vw-ecommerce-store' ),
		);
	}

	/**
	 * Returns the available font styles.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return array
	 */
	public function get_font_style_choices() {

		return array(
			'' => esc_html__( 'No Fonts Style', 'vw-ecommerce-store' ),
			'normal'  => esc_html__( 'Normal', 'vw-ecommerce-store' ),
			'italic'  => esc_html__( 'Italic', 'vw-ecommerce-store' ),
			'oblique' => esc_html__( 'Oblique', 'vw-ecommerce-store' )
		);
	}
}
