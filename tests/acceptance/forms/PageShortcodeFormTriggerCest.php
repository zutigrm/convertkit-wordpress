<?php
/**
 * Tests for the ConvertKit Form Trigger shortcode.
 *
 * @since   2.2.0
 */
class PageShortcodeFormTriggerCest
{
	/**
	 * Run common actions before running the test functions in this class.
	 *
	 * @since   2.2.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _before(AcceptanceTester $I)
	{
		$I->activateConvertKitPlugin($I);

		// Setup ConvertKit Plugin with no default form specified.
		$I->setupConvertKitPlugin($I, $_ENV['CONVERTKIT_API_KEY'], $_ENV['CONVERTKIT_API_SECRET'], '', '', '');
		$I->setupConvertKitPluginResources($I);
	}

	/**
	 * Test the [convertkit_formtrigger] shortcode works when a valid Form ID is specified,
	 * using the Classic Editor (TinyMCE / Visual).
	 *
	 * @since   2.2.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testFormTriggerShortcodeInVisualEditorWithValidFormParameter(AcceptanceTester $I)
	{
		// Add a Page using the Classic Editor.
		$I->addClassicEditorPage($I, 'page', 'ConvertKit: Page: Form Trigger: Shortcode: Visual Editor');

		// Add shortcode to Page, setting the Form setting to the value specified in the .env file.
		$I->addVisualEditorShortcode(
			$I,
			'ConvertKit Form Trigger',
			[
				'form' => [ 'select', $_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_NAME'] ],
			],
			'[convertkit_formtrigger form="' . $_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_ID'] . '" text="Subscribe"]'
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewClassicEditorPage($I);

		// Confirm that the ConvertKit Form Trigger is displayed.
		$I->seeFormTriggerOutput($I, $_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_URL'], 'Subscribe');
	}

	/**
	 * Test the [convertkit_formtrigger] shortcode works when a valid Form ID is specified,
	 * using the Text Editor.
	 *
	 * @since   2.2.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testFormTriggerShortcodeInTextEditorWithValidFormTriggerParameter(AcceptanceTester $I)
	{
		// Add a Page using the Classic Editor.
		$I->addClassicEditorPage($I, 'page', 'ConvertKit: Page: Form Trigger: Shortcode: Text Editor');

		// Add shortcode to Page, setting the Form setting to the value specified in the .env file.
		$I->addTextEditorShortcode(
			$I,
			'convertkit-formtrigger',
			[
				'form' => [ 'select', $_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_NAME'] ],
			],
			'[convertkit_formtrigger form="' . $_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_ID'] . '" text="Subscribe"]'
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewClassicEditorPage($I);

		// Confirm that the ConvertKit Form Trigger is displayed.
		$I->seeFormTriggerOutput($I, $_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_URL'], 'Subscribe');
	}

	/**
	 * Test the [convertkit_formtrigger] shortcode does not output errors when an invalid Form ID is specified.
	 *
	 * @since   2.2.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testFormTriggerShortcodeWithInvalidFormParameter(AcceptanceTester $I)
	{
		// Create Page with Shortcode.
		$I->havePageInDatabase(
			[
				'post_name'    => 'convertkit-form-trigger-shortcode-invalid-form-param',
				'post_content' => '[convertkit_formtrigger=1]',
			]
		);

		// Load the Page on the frontend site.
		$I->amOnPage('/convertkit-form-trigger-shortcode-invalid-form-param');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that no ConvertKit Form Trigger button is displayed.
		$I->dontSeeFormTriggerOutput($I);
	}

	/**
	 * Test the Form Trigger shortcode's text parameter works.
	 *
	 * @since   2.2.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testFormTriggerShortcodeInVisualEditorWithTextParameter(AcceptanceTester $I)
	{
		// Add a Page using the Classic Editor.
		$I->addClassicEditorPage($I, 'page', 'ConvertKit: Page: Form Trigger: Shortcode: Text Param');

		// Add shortcode to Page, setting the Form setting to the value specified in the .env file.
		$I->addVisualEditorShortcode(
			$I,
			'ConvertKit Form Trigger',
			[
				'form' 	=> [ 'select', $_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_NAME'] ],
				'text'  => [ 'input', 'Sign up' ],
			],
			'[convertkit_formtrigger form="' . $_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_ID'] . '" text="Sign up"]'
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewClassicEditorPage($I);

		// Confirm that the ConvertKit Form Trigger is displayed.
		$I->seeFormTriggerOutput($I, $_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_URL'], 'Sign up');
	}

	/**
	 * Test the Form Trigger shortcode's default text value is output when the text parameter is blank.
	 *
	 * @since   2.2.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testFormTriggerShortcodeInVisualEditorWithBlankTextParameter(AcceptanceTester $I)
	{
		// Add a Page using the Classic Editor.
		$I->addClassicEditorPage($I, 'page', 'ConvertKit: Page: Form Trigger: Shortcode: Blank Text Param');

		// Add shortcode to Page, setting the Form setting to the value specified in the .env file.
		$I->addVisualEditorShortcode(
			$I,
			'ConvertKit Form Trigger',
			[
				'form' 	=> [ 'select', $_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_NAME'] ],
				'text'  => [ 'input', '' ],
			],
			'[convertkit_formtrigger form="' . $_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_ID'] . '"]'
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewClassicEditorPage($I);

		// Confirm that the ConvertKit Form Trigger is displayed.
		$I->seeFormTriggerOutput($I, $_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_URL'], 'Subscribe');
	}

	/**
	 * Test the [convertkit_formtrigger] shortcode hex colors works when defined.
	 *
	 * @since   2.2.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testFormTriggerShortcodeWithHexColorParameters(AcceptanceTester $I)
	{
		// Define colors.
		$backgroundColor = '#ee1616';
		$textColor       = '#1212c0';

		// It's tricky to interact with WordPress's color picker, so we programmatically create the Page
		// instead to then confirm the color settings apply on the output.
		// We don't need to test the color picker itself, as it's a WordPress supplied component, and our
		// other Acceptance tests confirm that the shortcode can be added in the Classic Editor.
		$I->havePageInDatabase(
			[
				'post_name'    => 'convertkit-page-form-trigger-shortcode-hex-color-params',
				'post_content' => '[convertkit_formtrigger form="' . $_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_ID'] . '" text="Subscribe" background_color="' . $backgroundColor . '" text_color="' . $textColor . '"]',
			]
		);

		// Load the Page on the frontend site.
		$I->amOnPage('/convertkit-page-form-trigger-shortcode-hex-color-params');

		// Wait for frontend web site to load.
		$I->waitForElementVisible('body.page-template-default');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the ConvertKit Form Trigger is displayed.
		$I->seeFormTriggerOutput($I, $_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_URL'], 'Subscribe', $textColor, $backgroundColor);
	}

	/**
	 * Test the [convertkit_formtrigger] shortcode parameters are correctly escaped on output,
	 * to prevent XSS.
	 *
	 * @since   2.0.5
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testFormTriggerShortcodeParameterEscaping(AcceptanceTester $I)
	{
		// Define a 'bad' shortcode.
		$I->havePageInDatabase(
			[
				'post_name'    => 'convertkit-page-form-trigger-shortcode-parameter-escaping',
				'post_content' => '[convertkit_formtrigger form="' . $_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_ID'] . '" text=\'Subscribe\' text_color=\'red" onmouseover="alert(1)"\']',
			]
		);

		// Load the Page on the frontend site.
		$I->amOnPage('/convertkit-page-form-trigger-shortcode-parameter-escaping');

		// Wait for frontend web site to load.
		$I->waitForElementVisible('body.page-template-default');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the output is escaped.
		$I->seeInSource('style="color:red&quot; onmouseover=&quot;alert(1)&quot;"');
		$I->dontSeeInSource('style="color:red" onmouseover="alert(1)""');

		// Confirm that the ConvertKit Form Trigger is displayed.
		$I->seeFormTriggerOutput($I, $_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_URL'], 'Subscribe');
	}

	/**
	 * Deactivate and reset Plugin(s) after each test, if the test passes.
	 * We don't use _after, as this would provide a screenshot of the Plugin
	 * deactivation and not the true test error.
	 *
	 * @since   2.2.0.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _passed(AcceptanceTester $I)
	{
		$I->deactivateThirdPartyPlugin($I, 'classic-editor');
		$I->deactivateConvertKitPlugin($I);
		$I->resetConvertKitPlugin($I);
	}
}
