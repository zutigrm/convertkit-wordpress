<?php
/**
 * ConvertKit Admin Notices class.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Add and remove persistent error messages across all
 * WordPress Administration screens.
 *
 * @package ConvertKit
 * @author ConvertKit
 */
class ConvertKit_Admin_Notices {

	/**
	 * The key prefix to use for stored notices
	 *
	 * @since   2.0.9
	 *
	 * @var     string
	 */
	private $key_prefix = 'convertkit_admin_notices';

	/**
	 * Register output function to display persistent notices
	 * in the WordPress Administration, if any exist.
	 * 
	 * @since 	2.0.9
	 */
	public function __construct() {

		add_action( 'admin_notices', array( $this, 'output' ) );

	}

	/**
	 * Output persistent notices in the WordPress Administration
	 * 
	 * @since 	2.0.9
	 */
	public function output() {

		// Don't output if we're on a settings screen.
		$screen = get_current_screen();
		if ( $screen->base === 'settings_page__wp_convertkit_settings' ) {
			return;
		}

		// Don't output if we don't have the required capabilities to fix the issue.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Bail if no notices exist.
		$notices = get_option( $this->key_prefix );
		if ( ! $notices ) {
			return;
		}

		// Output notices.
		foreach ( $notices as $notice ) {
			?>
			<div class="notice notice-error">
				<p>
					<?php 
					// Depending on the notice, output the applicable error message.
					switch ( $notice ) {
						case 'authorization_failed':
							echo sprintf(
								'%s %s',
								esc_html__( 'ConvertKit: Authorization failed. Please enter valid API credentials on the', 'convertkit' ),
								sprintf(
									'<a href="%s">%s</a>',
									esc_url( convertkit_get_settings_link() ),
									__( 'settings screen.', 'convertkit' )
								)
							);
							break;
					}
					?>
				</p>
			</div>
			<?php
		}

	}

	/**
	 * Add a persistent notice for output in the WordPress Administration.
	 * 
	 * @since 	2.0.9
	 * 
	 * @param 	string  $notice 	Notice name.
	 * @return 	bool 				Notice saved successfully
	 */
	public function add( $notice ) {

		// If no other persistent notices exist, add one now.
		if ( ! $this->exist() ) {
			return update_option( $this->key_prefix, array( $notice ) );
		}

		// Fetch existing persistent notices.
		$notices = $this->get();

		// Add notice to existing notices.
		$notices[] = $notice;

		// Remove any duplicate notices.
		$notices = array_values( array_unique( $notices ) );

		// Update and return.
		return update_option( $this->key_prefix, $notices );

	}

	/**
	 * Returns all notices stored in the options table.
	 * 
	 * @since 	2.0.9
	 * 
	 * @return 	array
	 */
	public function get() {

		// Fetch all notices from the options table.
		return get_option( $this->key_prefix );

	}

	/**
	 * Whether any persistent notices are stored in the option table.
	 * 
	 * @since 	2.0.9
	 * 
	 * @return 	bool
	 */
	public function exist() {

		if ( ! $this->get() ) {
			return false;
		}

		return true;

	}

	/**
	 * Delete all persistent notices.
	 * 
	 * @since 	2.0.9
	 * 
	 * @param 	string 	$notice 	Notice name.
	 * @return 	bool 				Success
	 */
	public function delete( $notice ) {

		// If no persistent notices exist, there's nothing to delete.
		if ( ! $this->exist() ) {
			return false;
		}

		// Fetch existing persistent notices.
		$notices = $this->get();

		// Remove notice from existing notices.
		if ( ( $index = array_search( $notice, $notices ) ) !== false ) {
    		unset( $notices[ $index ] );
		}
		
		// Update and return.
		return update_option( $this->key_prefix, $notices );

	}

}
