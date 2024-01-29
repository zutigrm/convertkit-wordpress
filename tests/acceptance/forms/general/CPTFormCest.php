<?php
/**
 * Tests for WordPress Custom Post Types (CPTs).
 *
 * @since   2.3.5
 */
class CPTFormCest
{
	/**
	 * Run common actions before running the test functions in this class.
	 *
	 * @since   2.3.5
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _before(AcceptanceTester $I)
	{
		// Activate ConvertKit plugin.
		$I->activateConvertKitPlugin($I);

		// Setup ConvertKit plugin .
		$I->setupConvertKitPlugin($I);
		$I->setupConvertKitPluginResources($I);

		// Create a Custom Post Type called Articles, using the Custom Post Type UI Plugin.
		$I->registerCustomPostType($I, 'article', 'Articles', 'Article');
	}

	/**
	 * Test that the Articles > Add New screen has expected a11y output, such as label[for].
	 *
	 * @since   2.4.3
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testAccessibility(AcceptanceTester $I)
	{
		// Setup ConvertKit Plugin.
		$I->setupConvertKitPlugin($I);
		$I->setupConvertKitPluginResources($I);

		// Navigate to Post Type (e.g. Pages / Posts) > Add New.
		$I->amOnAdminPage('post-new.php?post_type=article');

		// Confirm that settings have label[for] attributes.
		$I->seeInSource('<label for="wp-convertkit-form">');
		$I->seeInSource('<label for="wp-convertkit-tag">');
	}

	/**
	 * Test that the 'Default' option for the Default Form setting in the Plugin Settings works when
	 * creating and viewing a new WordPress CPT, and there is no Default Form specified in the Plugin
	 * settings.
	 *
	 * @since   2.4.3
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testAddNewCPTUsingDefaultFormWithNoDefaultFormSpecifiedInPlugin(AcceptanceTester $I)
	{
		// Setup Plugin, without defining default Forms.
		$I->setupConvertKitPluginNoDefaultForms($I);
		$I->setupConvertKitPluginResources($I);

		// Add a CPT using the Gutenberg editor.
		$I->addGutenbergPage($I, 'article', 'ConvertKit: CPT: Form: Default: None');

		// Check the order of the Form resources are alphabetical, with the Default and None options prepending the Forms.
		$I->checkSelectFormOptionOrder(
			$I,
			'#wp-convertkit-form',
			[
				'Default',
				'None',
			]
		);

		// Configure metabox's Form setting = Default.
		$I->configureMetaboxSettings(
			$I,
			'wp-convertkit-meta-box',
			[
				'form' => [ 'select2', 'Default' ],
			]
		);

		// Publish and view the CPT on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that no ConvertKit Form is displayed.
		$I->dontSeeElementInDOM('form[data-sv-form]');
	}

	/**
	 * Test that the Default Form specified in the Plugin Settings works when
	 * creating and viewing a new WordPress CPT.
	 *
	 * @since   2.4.3
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testAddNewCPTUsingDefaultForm(AcceptanceTester $I)
	{
		// Setup ConvertKit Plugin.
		$I->setupConvertKitPlugin(
			$I,
			[
				'article_form' => $_ENV['CONVERTKIT_API_FORM_ID'],
			]
		);
		$I->setupConvertKitPluginResources($I);

		// Add a CPT using the Gutenberg editor.
		$I->addGutenbergPage($I, 'article', 'ConvertKit: CPT: Form: Default');

		// Configure metabox's Form setting = Default.
		$I->configureMetaboxSettings(
			$I,
			'wp-convertkit-meta-box',
			[
				'form' => [ 'select2', 'Default' ],
			]
		);

		// Publish and view the CPT on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that one ConvertKit Form is output in the DOM.
		// This confirms that there is only one script on the page for this form, which renders the form.
		$I->seeNumberOfElementsInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_FORM_ID'] . '"]', 1);
	}

	/**
	 * Test that the Default Legacy Form specified in the Plugin Settings works when
	 * creating and viewing a new WordPress CPT.
	 *
	 * @since   2.4.3
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testAddNewCPTUsingDefaultLegacyForm(AcceptanceTester $I)
	{
		// Setup Plugin, without defining default Forms.
		$I->setupConvertKitPlugin(
			$I,
			[
				'article_form' => $_ENV['CONVERTKIT_API_LEGACY_FORM_ID'],
			]
		);
		$I->setupConvertKitPluginResources($I);

		// Add a CPT using the Gutenberg editor.
		$I->addGutenbergPage($I, 'article', 'ConvertKit: CPT: Form: Legacy: Default');

		// Configure metabox's Form setting = Default.
		$I->configureMetaboxSettings(
			$I,
			'wp-convertkit-meta-box',
			[
				'form' => [ 'select2', 'Default' ],
			]
		);

		// Publish and view the CPT on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that the ConvertKit Default Legacy Form displays.
		$I->seeInSource('<form id="ck_subscribe_form" class="ck_subscribe_form" action="https://api.convertkit.com/landing_pages/' . $_ENV['CONVERTKIT_API_LEGACY_FORM_ID'] . '/subscribe" data-remote="true">');
	}

	/**
	 * Test that 'None' Form specified in the CPT Settings works when
	 * creating and viewing a new WordPress CPT.
	 *
	 * @since   2.4.3
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testAddNewCPTUsingNoForm(AcceptanceTester $I)
	{
		// Setup ConvertKit Plugin.
		$I->setupConvertKitPlugin(
			$I,
			[
				'article_form' => $_ENV['CONVERTKIT_API_FORM_ID'],
			]
		);
		$I->setupConvertKitPluginResources($I);

		// Add a CPT using the Gutenberg editor.
		$I->addGutenbergPage($I, 'article', 'ConvertKit: CPT: Form: None');

		// Configure metabox's Form setting = None.
		$I->configureMetaboxSettings(
			$I,
			'wp-convertkit-meta-box',
			[
				'form' => [ 'select2', 'None' ],
			]
		);

		// Publish and view the CPT on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that no ConvertKit Form is displayed.
		$I->dontSeeElementInDOM('form[data-sv-form]');
	}

	/**
	 * Test that the Form specified in the CPT Settings works when
	 * creating and viewing a new WordPress CPT.
	 *
	 * @since   2.4.3
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testAddNewCPTUsingDefinedForm(AcceptanceTester $I)
	{
		// Setup ConvertKit Plugin.
		$I->setupConvertKitPlugin(
			$I,
			[
				'article_form' => $_ENV['CONVERTKIT_API_FORM_ID'],
			]
		);
		$I->setupConvertKitPluginResources($I);

		// Add a CPT using the Gutenberg editor.
		$I->addGutenbergPage($I, 'article', 'ConvertKit: CPT: Form: ' . $_ENV['CONVERTKIT_API_FORM_NAME']);

		// Configure metabox's Form setting = None.
		$I->configureMetaboxSettings(
			$I,
			'wp-convertkit-meta-box',
			[
				'form' => [ 'select2', $_ENV['CONVERTKIT_API_FORM_NAME'] ],
			]
		);

		// Publish and view the CPT on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that one ConvertKit Form is output in the DOM.
		// This confirms that there is only one script on the page for this form, which renders the form.
		$I->seeNumberOfElementsInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_FORM_ID'] . '"]', 1);
	}

	/**
	 * Test that the Legacy Form specified in the CPT Settings works when
	 * creating and viewing a new WordPress CPT.
	 *
	 * @since   2.4.3
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testAddNewCPTUsingDefinedLegacyForm(AcceptanceTester $I)
	{
		// Setup ConvertKit Plugin.
		$I->setupConvertKitPlugin(
			$I,
			[
				'article_form' => $_ENV['CONVERTKIT_API_FORM_ID'],
			]
		);
		$I->setupConvertKitPluginResources($I);

		// Add a CPT using the Gutenberg editor.
		$I->addGutenbergPage($I, 'article', 'ConvertKit: CPT: Form: ' . $_ENV['CONVERTKIT_API_LEGACY_FORM_NAME']);

		// Configure metabox's Form setting = None.
		$I->configureMetaboxSettings(
			$I,
			'wp-convertkit-meta-box',
			[
				'form' => [ 'select2', $_ENV['CONVERTKIT_API_LEGACY_FORM_NAME'] ],
			]
		);

		// Publish and view the CPT on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that the ConvertKit Legacy Form displays.
		$I->seeInSource('<form id="ck_subscribe_form" class="ck_subscribe_form" action="https://api.convertkit.com/landing_pages/' . $_ENV['CONVERTKIT_API_LEGACY_FORM_ID'] . '/subscribe" data-remote="true">');
	}

	/**
	 * Test that the Default Form for CPTs displays when an invalid Form ID is specified
	 * for a CPT.
	 *
	 * Whilst the on screen options won't permit selecting an invalid Form ID, a CPT might
	 * have an invalid Form ID because:
	 * - the form belongs to another ConvertKit account (i.e. API credentials were changed in the Plugin, but this CPT's specified Form was not changed)
	 * - the form was deleted from the ConvertKit account.
	 *
	 * @since   2.4.3
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testAddNewCPTUsingInvalidDefinedForm(AcceptanceTester $I)
	{
		// Setup ConvertKit Plugin.
		$I->setupConvertKitPlugin(
			$I,
			[
				'article_form' => $_ENV['CONVERTKIT_API_FORM_ID'],
			]
		);
		$I->setupConvertKitPluginResources($I);

		// Create CPT, with an invalid Form ID, as if it were created prior to API credentials being changed and/or
		// a Form being deleted in ConvertKit.
		$postID = $I->havePostInDatabase(
			[
				'post_type'  => 'article',
				'post_title' => 'ConvertKit: CPT: Form: Specific: Invalid',
				'meta_input' => [
					'_wp_convertkit_post_meta' => [
						'form'         => '11111',
						'landing_page' => '',
						'tag'          => '',
					],
				],
			]
		);

		// Load the CPT on the frontend site.
		$I->amOnPage('/?p=' . $postID);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the invalid ConvertKit Form does not display.
		$I->dontSeeElementInDOM('form[data-sv-form="11111"]');

		// Confirm that one ConvertKit Form is output in the DOM.
		// This confirms that there is only one script on the page for this form, which renders the form.
		$I->seeNumberOfElementsInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_FORM_ID'] . '"]', 1);
	}

	/**
	 * Test that the Default Form for Pages displays when the Default option is chosen via
	 * WordPress' Quick Edit functionality.
	 *
	 * @since   2.4.3
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testQuickEditUsingDefaultForm(AcceptanceTester $I)
	{
		// Setup ConvertKit Plugin.
		$I->setupConvertKitPlugin(
			$I,
			[
				'article_form' => $_ENV['CONVERTKIT_API_FORM_ID'],
			]
		);
		$I->setupConvertKitPluginResources($I);

		// Programmatically create a CPT.
		$postID = $I->havePostInDatabase(
			[
				'post_type'  => 'article',
				'post_title' => 'ConvertKit: CPT: Form: Default: Quick Edit',
			]
		);

		// Quick Edit the CPT in the CPTs WP_List_Table.
		$I->quickEdit(
			$I,
			'article',
			$postID,
			[
				'form' => [ 'select', 'Default' ],
			]
		);

		// Load the CPT on the frontend site.
		$I->amOnPage('/?p=' . $postID);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that one ConvertKit Form is output in the DOM.
		// This confirms that there is only one script on the page for this form, which renders the form.
		$I->seeNumberOfElementsInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_FORM_ID'] . '"]', 1);
	}

	/**
	 * Test that the defined form displays when chosen via
	 * WordPress' Quick Edit functionality.
	 *
	 * @since   2.4.3
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testQuickEditUsingDefinedForm(AcceptanceTester $I)
	{
		// Setup ConvertKit Plugin.
		$I->setupConvertKitPlugin(
			$I,
			[
				'article_form' => $_ENV['CONVERTKIT_API_FORM_ID'],
			]
		);
		$I->setupConvertKitPluginResources($I);

		// Programmatically create a CPT.
		$postID = $I->havePostInDatabase(
			[
				'post_type'  => 'article',
				'post_title' => 'ConvertKit: CPT: Form: ' . $_ENV['CONVERTKIT_API_FORM_NAME'] . ': Quick Edit',
			]
		);

		// Quick Edit the CPT in the CPTs WP_List_Table.
		$I->quickEdit(
			$I,
			'article',
			$postID,
			[
				'form' => [ 'select', $_ENV['CONVERTKIT_API_FORM_NAME'] ],
			]
		);

		// Load the CPT on the frontend site.
		$I->amOnPage('/?p=' . $postID);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that one ConvertKit Form is output in the DOM.
		// This confirms that there is only one script on the page for this form, which renders the form.
		$I->seeNumberOfElementsInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_FORM_ID'] . '"]', 1);
	}

	/**
	 * Test that the Default Form for CPTs displays when the Default option is chosen via
	 * WordPress' Bulk Edit functionality.
	 *
	 * @since   2.4.3
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testBulkEditUsingDefaultForm(AcceptanceTester $I)
	{
		// Setup ConvertKit Plugin.
		$I->setupConvertKitPlugin(
			$I,
			[
				'article_form' => $_ENV['CONVERTKIT_API_FORM_ID'],
			]
		);
		$I->setupConvertKitPluginResources($I);

		// Programmatically create two CPTs.
		$postIDs = array(
			$I->havePostInDatabase(
				[
					'post_type'  => 'article',
					'post_title' => 'ConvertKit: CPT: Form: Default: Bulk Edit #1',
				]
			),
			$I->havePostInDatabase(
				[
					'post_type'  => 'article',
					'post_title' => 'ConvertKit: CPT: Form: Default: Bulk Edit #2',
				]
			),
		);

		// Bulk Edit the CPTs in the CPTs WP_List_Table.
		$I->bulkEdit(
			$I,
			'article',
			$postIDs,
			[
				'form' => [ 'select', 'Default' ],
			]
		);

		// Iterate through CPTs to run frontend tests.
		foreach ($postIDs as $postID) {
			// Load CPT on the frontend site.
			$I->amOnPage('/?p=' . $postID);

			// Check that no PHP warnings or notices were output.
			$I->checkNoWarningsAndNoticesOnScreen($I);

			// Confirm that one ConvertKit Form is output in the DOM.
			// This confirms that there is only one script on the page for this form, which renders the form.
			$I->seeNumberOfElementsInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_FORM_ID'] . '"]', 1);
		}
	}

	/**
	 * Test that the defined form displays when chosen via
	 * WordPress' Bulk Edit functionality.
	 *
	 * @since   2.4.3
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testBulkEditUsingDefinedForm(AcceptanceTester $I)
	{
		// Setup ConvertKit Plugin.
		$I->setupConvertKitPlugin(
			$I,
			[
				'article_form' => $_ENV['CONVERTKIT_API_FORM_ID'],
			]
		);
		$I->setupConvertKitPluginResources($I);

		// Programmatically create two CPTs.
		$postIDs = array(
			$I->havePostInDatabase(
				[
					'post_type'  => 'article',
					'post_title' => 'ConvertKit: CPT: Form: ' . $_ENV['CONVERTKIT_API_FORM_NAME'] . ': Bulk Edit #1',
				]
			),
			$I->havePostInDatabase(
				[
					'post_type'  => 'article',
					'post_title' => 'ConvertKit: CPT: Form: ' . $_ENV['CONVERTKIT_API_FORM_NAME'] . ': Bulk Edit #2',
				]
			),
		);

		// Bulk Edit the CPTs in the CPTs WP_List_Table.
		$I->bulkEdit(
			$I,
			'article',
			$postIDs,
			[
				'form' => [ 'select', $_ENV['CONVERTKIT_API_FORM_NAME'] ],
			]
		);

		// Iterate through CPTs to run frontend tests.
		foreach ($postIDs as $postID) {
			// Load CPT on the frontend site.
			$I->amOnPage('/?p=' . $postID);

			// Check that no PHP warnings or notices were output.
			$I->checkNoWarningsAndNoticesOnScreen($I);

			// Confirm that one ConvertKit Form is output in the DOM.
			// This confirms that there is only one script on the page for this form, which renders the form.
			$I->seeNumberOfElementsInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_FORM_ID'] . '"]', 1);
		}
	}

	/**
	 * Test that the existing settings are honored and not changed
	 * when the Bulk Edit options are set to 'No Change'.
	 *
	 * @since   2.4.3
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testBulkEditWithNoChanges(AcceptanceTester $I)
	{
		// Setup ConvertKit Plugin.
		$I->setupConvertKitPlugin(
			$I,
			[
				'article_form' => $_ENV['CONVERTKIT_API_FORM_ID'],
			]
		);
		$I->setupConvertKitPluginResources($I);

		// Programmatically create two CPTs with a defined form.
		$postIDs = array(
			$I->havePostInDatabase(
				[
					'post_type'  => 'article',
					'post_title' => 'ConvertKit: CPT: Form: ' . $_ENV['CONVERTKIT_API_FORM_NAME'] . ': Bulk Edit with No Change #1',
					'meta_input' => [
						'_wp_convertkit_post_meta' => [
							'form'         => $_ENV['CONVERTKIT_API_FORM_ID'],
							'landing_page' => '',
							'tag'          => '',
						],
					],
				]
			),
			$I->havePostInDatabase(
				[
					'post_type'  => 'article',
					'post_title' => 'ConvertKit: CPT: Form: ' . $_ENV['CONVERTKIT_API_FORM_NAME'] . ': Bulk Edit with No Change #2',
					'meta_input' => [
						'_wp_convertkit_post_meta' => [
							'form'         => $_ENV['CONVERTKIT_API_FORM_ID'],
							'landing_page' => '',
							'tag'          => '',
						],
					],
				]
			),
		);

		// Bulk Edit the CPTs in the CPTs WP_List_Table.
		$I->bulkEdit(
			$I,
			'article',
			$postIDs,
			[
				'form' => [ 'select', '— No Change —' ],
			]
		);

		// Iterate through CPTs to run frontend tests.
		foreach ($postIDs as $postID) {
			// Load CPT on the frontend site.
			$I->amOnPage('/?p=' . $postID);

			// Check that no PHP warnings or notices were output.
			$I->checkNoWarningsAndNoticesOnScreen($I);

			// Confirm that one ConvertKit Form is output in the DOM.
			// This confirms that there is only one script on the page for this form, which renders the form.
			$I->seeNumberOfElementsInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_FORM_ID'] . '"]', 1);
		}
	}

	/**
	 * Test that the Bulk Edit fields do not display when a search on a WP_List_Table
	 * returns no results.
	 *
	 * @since   2.4.3
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testBulkEditFieldsHiddenWhenNoCPTsFound(AcceptanceTester $I)
	{
		// Setup ConvertKit Plugin.
		$I->setupConvertKitPlugin(
			$I,
			[
				'article_form' => $_ENV['CONVERTKIT_API_FORM_ID'],
			]
		);
		$I->setupConvertKitPluginResources($I);

		// Emulate the user searching for CPTs with a query string that yields no results.
		$I->amOnAdminPage('edit.php?post_type=article&s=nothing');

		// Confirm that the Bulk Edit fields do not display.
		$I->dontSeeElement('#convertkit-bulk-edit');
	}

	/**
	 * Deactivate and reset Plugin(s) after each test, if the test passes.
	 * We don't use _after, as this would provide a screenshot of the Plugin
	 * deactivation and not the true test error.
	 *
	 * @since   2.4.3.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _passed(AcceptanceTester $I)
	{
		$I->unregisterCustomPostType($I, 'article');
		$I->deactivateConvertKitPlugin($I);
		$I->resetConvertKitPlugin($I);
	}
}
