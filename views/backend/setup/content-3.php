<?php
/**
 * Outputs the content for Setup screen's first step.
 *
 * @package ConvertKit
 * @author ConvertKit
 */

// If no Forms exist on the ConvertKit account, show the user how to add a Form to ConvertKit,
// with an option to refresh this page so that they can then select the Form to be displayed on e.g. Posts
if ( ! $this->forms->exist() ) {
	?>
	<h1><?php esc_html_e( 'Create your first ConvertKit Form', 'convertkit' ); ?></h1>
	<p>
		<?php
		esc_html_e( 'To capture email leads, you first need to create a form in ConvertKit. Click the button below to get started.', 'convertkit' );
		?>
	</p>

	<a href="https://app.convertkit.com/forms/new?format=inline" target="_blank" class="button button-primary">
		<?php _e( 'Create form', 'convertkit' ); ?>
	</a>

	<p>
		<?php
		esc_html_e( 'Not sure how to do this? Follow the video below.', 'convertkit' );
		?>
	</p>

	<div>
		<iframe width="560" height="315" src="https://www.youtube-nocookie.com/embed/_RmI6vQhGu8" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
	</div>

	<a href="admin.php?page=convertkit-setup&step=3" class="button button-primary">
		<?php _e( 'I\'ve created a form in ConvertKit.', 'convertkit' ); ?>
	</a>
	<?php
} else {
	// Show options to configure Form to display on Posts and Pages.
	?>
	<h1><?php esc_html_e( 'Display an email capture form', 'convertkit' ); ?></h1>
	<p>
		<?php
		esc_html_e( 'To capture email leads, you need to display a ConvertKit form on your site, using the options below.', 'convertkit' );
		?>
	</p>
	<hr />
	<div>
		<label for="default_form_posts">
			<?php esc_html_e( 'Which form would you like to display on your individual Posts?', 'convertkit' ); ?>
		</label>
		<select name="default_form_posts" id="wp-convertkit-form-posts" class="convertkit-select2">			
			<?php
			foreach ( $this->forms->get() as $form ) {
				?>
				<option value="<?php echo esc_attr( $form['id'] ); ?>">
					<?php echo esc_attr( $form['name'] ); ?>
				</option>
				<?php
			}
			?>
		</select>

		<p class="description">
			<?php esc_html_e( 'The selected form will be displayed after the content of individual Posts', 'convertkit' ); ?>
		</p>
	</div>
	<?php
}
