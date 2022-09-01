/**
 * Registers Blocks in the text editor as QuickTag Buttons.
 *
 * @since   1.9.6
 *
 * @package ConvertKit
 * @author ConvertKit
 */

for ( const block in convertkit_quicktags ) {

	convertKitQuickTagRegister( convertkit_quicktags[ block ] );

}

/**
 * Registers the given block as a Quick Tag, with a button in
 * the Text Editor toolbar.
 *
 * @since 	1.9.6
 *
 * @param 	object 	block 	Block
 */
function convertKitQuickTagRegister( block ) {

	( function( $ ) {

		QTags.addButton(
			'convertkit-' + block.name,
			block.title,
			function() {

				// Perform an AJAX call to load the modal's view.
				$.post(
					ajaxurl,
					{
						'action': 		'convertkit_admin_tinymce_output_modal',
						'nonce':  		convertkit_admin_tinymce.nonce,
						'editor_type':  'quicktags',
						'shortcode': 	block.name

					},
					function( response ) {

						// Show Modal.
						convertKitQuickTagsModal.open();

						// Set Title.
						$( '#convertkit-quicktags-modal .media-frame-title h1' ).text( block.title );

						// Inject HTML into modal.
						$( '#convertkit-quicktags-modal .media-frame-content' ).html( response );

						console.log( $( 'div.convertkit-quicktags-modal div.media-frame-title h1' ).outerHeight() );
						console.log( $( 'div.convertkit-quicktags-modal form.convertkit-tinymce-popup' ).height() );

						// Resize Modal height to prevent whitespace below form.
						$( 'div.convertkit-quicktags-modal div.media-modal.wp-core-ui' ).css(
							{
								height: ( $( 'div.convertkit-quicktags-modal div.media-frame-title h1' ).outerHeight() + $( 'div.convertkit-quicktags-modal form.convertkit-tinymce-popup' ).height() + 6 ) + 'px' // Prevents a vertical scroll bar.
							}
						);

					}
				);

			}
		);

	} )( jQuery );

}
