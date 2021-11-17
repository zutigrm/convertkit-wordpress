<?php
/**
 * ConvertKit Settings class
 *
 * @package ConvertKit
 * @author ConvertKit
 */
abstract class ConvertKit_Settings_Base {

	/**
	 * Section name
	 *
	 * @var string
	 */
	public $name;

	/**
	 * Section title
	 *
	 * @var string
	 */
	public $title;

	/**
	 * Section tab text
	 *
	 * @var string
	 */
	public $tab_text;

	/**
	 * Options table key
	 *
	 * @var string
	 */
	public $settings_key;

	/**
	 * Holds the settings class for the section.
	 *
	 * @since   1.9.6
	 *
	 * @var     mixed   ConvertKit_Settings
	 */
	public $settings;

	/**
	 * Constructor
	 */
	public function __construct() {

		// If tab text is not defined, use the title for the tab's text.
		if ( empty( $this->tab_text ) ) {
			$this->tab_text = $this->title;
		}

		// Register the settings section.
		$this->register_section();

	}

	/**
	 * Register settings section.
	 */
	public function register_section() {

		add_settings_section(
			$this->name,
			$this->title,
			array( $this, 'print_section_info' ),
			$this->settings_key
		);

		$this->register_fields();

		register_setting(
			$this->settings_key,
			$this->settings_key,
			array( $this, 'sanitize_settings' )
		);

	}

	/**
	 * Register fields for this section
	 */
	abstract public function register_fields();

	/**
	 * Prints help info for this section
	 */
	abstract public function print_section_info();

	/**
	 * Renders the section
	 */
	public function render() {

		do_action( 'convertkit_settings_base_render_before' );

		do_settings_sections( $this->settings_key );

		settings_fields( $this->settings_key );

		submit_button();

		do_action( 'convertkit_settings_base_render_after' );

	}

	/**
	 * Outputs the given error message in an inline notice.
	 *
	 * @since   1.9.6
	 *
	 * @param   string $error_message  Error Message.
	 */
	public function output_error( $error_message ) {

		?>
		<div class="inline notice notice-error">
			<p>
				<?php echo $error_message; ?>
			</p>
		</div>
		<?php

	}

	/**
	 * Returns a masked value.
	 *
	 * @since   1.9.6
	 *
	 * @param   string $value          Value
	 * @param   mixed  $description    Description (false|string)
	 */
	public function get_masked_value( $value, $description = false ) {

		$html = sprintf(
			'<code>%s</code>',
			str_repeat( '*', strlen( $value ) - 4 ) . substr( $value, - 4 )
		);

		if ( $description ) {
			$html .= $this->get_description( $description );
		}

		return $html;

	}

	/**
	 * Returns a text field.
	 *
	 * @since   1.9.6
	 *
	 * @param   string $name           Name
	 * @param   string $value          Value
	 * @param   mixed  $description    Description (false|string)
	 */
	public function get_text_field( $name, $value = '', $description = false ) {

		$html = sprintf(
			'<input type="text" class="regular-text code" id="%s" name="%s[%s]" value="%s" />',
			$name,
			$this->settings_key,
			$name,
			$value
		);

		if ( $description ) {
			$html .= $this->get_description( $description );
		}

		return $html;

	}

	/**
	 * Returns a select dropdown field.
	 *
	 * @since   1.9.6
	 *
	 * @param   string $name           Name
	 * @param   string $value          Value
	 * @param   array  $options        Options / Choices
	 * @param   mixed  $description    Description (false|string)
	 */
	public function get_select_field( $name, $value = '', $options = array(), $description = '' ) {

		$html = sprintf(
			'<select id="%s" name="%s[%s]" size="1">',
			$this->settings_key . '_' . $name,
			$this->settings_key,
			$name
		);

		foreach ( $options as $option => $label ) {
			$html .= sprintf(
				'<option value="%s"%s>%s</option>',
				$option,
				selected( $value, $option, false ),
				$label
			);
		}

		$html .= '</select>';

		if ( $description ) {
			$html .= $this->get_description( $description );
		}

		return $html;

	}

	/**
	 * Returns a checkbox field.
	 *
	 * @since   1.9.6
	 *
	 * @param   string $name           Name
	 * @param   string $value          Value
	 * @param   bool   $checked        Should checkbox be checked/ticked
	 * @param   mixed  $label          Label (false|string)
	 * @param   mixed  $description    Description (false|string)
	 */
	public function get_checkbox_field( $name, $value, $checked = false, $label = '', $description = '' ) {

		$html = '';

		if ( $label ) {
			$html .= sprintf(
				'<label for="%s">',
				$name
			);
		}

		$html .= sprintf(
			'<input type="checkbox" id="%s" name="%s[%s]" value="%s" %s />',
			$name,
			$this->settings_key,
			$name,
			$value,
			( $checked ? ' checked' : '' )
		);

		if ( $label ) {
			$html .= sprintf(
				'%s</label>',
				$label
			);
		}

		if ( $description ) {
			$html .= $this->get_description( $description );
		}

		return $html;

	}

	/**
	 * Returns the given text wrapped in a paragraph with the description class.
	 *
	 * @since   1.9.6
	 *
	 * @param   string $description    Description
	 * @return  string                  HTML Description
	 */
	private function get_description( $description ) {

		return '<p class="description">' . $description . '</p>';

	}

	/**
	 * Sanitizes the settings prior to being saved.
	 *
	 * @since   1.9.6
	 *
	 * @param   array $settings   Submitted Settings Fields
	 * @return  array               Sanitized Settings with Defaults
	 */
	public function sanitize_settings( $settings ) {

		return wp_parse_args( $settings, $this->settings->get_defaults() );

	}

}
