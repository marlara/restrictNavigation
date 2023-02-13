/**
 * @file cypress/tests/functional/RestrictNavigation.cy.js
 *
 * Copyright Lara Marziali
 * Distributed under the GNU GPL v3. For full terms see the file LICENSE.
 *
 */

describe('Restrict Navigation plugin tests', function() {
	it('It restricts the navigation to some specific users', function() {
		cy.login('admin', 'admin', 'publicknowledge');

		cy.get('.app__nav a').contains('Website').click();
		cy.get('button[id="plugins-button"]').click();

		// Find and enable the plugin
		cy.get('input[id^="select-cell-restrictnavigationplugin-enabled"]').click();
		cy.get('div:contains(\'The plugin "Restrict Navigation Plugin" has been enabled.\')');
        cy.waitJQuery();

        //Set the restriction
		cy.get('tr[id*="restrictnavigationplugin"] a.show_extras').click();
        cy.get('a[id*="restrictnavigationplugin-settings"]').click();
		cy.waitJQuery(2000); // Wait for form to settle
        cy.get('form[id="restrictNavigationSettings"] input[name="tools"]').click();
        cy.get('form[id="restrictNavigationSettings"] button[id^="submitFormButton-"]').click({force: true});
        cy.waitJQuery();

        //Logout as admin and login as Journal Manager (see https://github.com/pkp/ojs/blob/main/cypress/tests/data/10-ApplicationSetup/40-CreateUsers.cy.js)
        cy.logout();
        cy.login('rvaca', 'rvacarvaca', 'publicknowledge');

        // Check the redirect to work
		cy.visit('/index.php/publicknowledge/management/tools', {failOnStatusCode: false});
		cy.location('/index.php/publicknowledge/management/tools').should('eq', '/index.php/publicknowledge/settings/announcements')
	});
})
