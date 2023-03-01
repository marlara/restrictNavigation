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
		cy.get('input[name=username]').eq(0).type('admin')
		cy.get('input[name=password]').eq(0).type('admin')
		cy.get('button[type="submit"').contains('Login').click();

		cy.get('.app__navItem').contains('Website').click();
		cy.get('button[id="plugins-button"]').click();

		// Find and enable the plugin
		cy.get('input[id^="select-cell-restrictnavigationplugin-enabled"]').click();
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
		cy.get('input[name=username]').eq(0).type('rvaca')
		cy.get('input[name=password]').eq(0).type('rvacarvaca')
		cy.get('button[type="submit"').contains('Login').click();

        // Check the redirect to work
		cy.visit('http://localhost/index.php/publicknowledge/management/tools', {failOnStatusCode: false}); //need to change the localhost with the right baseurl
		cy.location('/index.php/publicknowledge/management/tools').should('eq', '/index.php/publicknowledge/management/settings/announcements')
	});
})
