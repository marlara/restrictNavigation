<?php

/**
* @file plugins/generic/restrictNavigatoin/RestrictNavigationPlugin.inc.php
 *
 * Copyright Lara Marziali
 * Distributed under the GNU GPL v3. For full terms see the file LICENSE.
 *
 * @class RestrictNavigatoinPlugin
 * @ingroup plugins_generic_RestrictNavigation
 *
 * @brief Plugin class for the RestrictNavigation plugin.
 */

import('lib.pkp.classes.template.PKPTemplateManager');
import('lib.pkp.classes.plugins.GenericPlugin');
import('lib.pkp.classes.security.Role');
import('lib.pkp.classes.session.SessionManager');
import('lib.pkp.classes.db.DAORegistry');


class RestrictNavigationPlugin extends GenericPlugin {
    /**
	 * @copydoc GenericPlugin::register()
	 */
	public function register($category, $path, $mainContextId = NULL) {
		$success = parent::register($category, $path, $mainContextId);
		if ($success && $this->getEnabled($mainContextId)) {

			HookRegistry::register('TemplateManager::setupBackendPage', [$this, 'restrictNavigation']);
        }
		return $success;
	}

    /**
     * Main function
     * 
     */

    public function restrictNavigation($hookName, $args)
    {
        /**
         * @copydoc TemplateManager::setupBackendPage()
         * 
         */

        $request = Application::get()->getRequest();
        
        $currentUser = $request->getUser(); #https://docs.pkp.sfu.ca/dev/documentation/3.3/en/architecture-authentication
        $context = $request->getContext(); #https://docs.pkp.sfu.ca/dev/documentation/en/architecture
        $templateManager = TemplateManager::getManager($request);
        
        $router = $request->getRouter(); #?
        $handler = $router->getHandler(); #?
        $userRoles = (array) $handler->getAuthorizedContextObject(ASSOC_TYPE_USER_ROLES); #from https://github.com/pkp/ojs/blob/main/classes/template/TemplateManager.php

        $menu = (array) $templateManager->getState('menu'); #https://github.com/pkp/ops/blob/7a4563933cb965ddad2e2ac2cfab4da9f20ac7a2/pages/authorDashboard/AuthorDashboardHandler.php

        if (!in_array([ROLE_ID_SITE_ADMIN], $userRoles)) {
        #if ($this->isUserAdmin($context, $currentUser, $userRoles)) {
            unset($menu['tools']);
            #unset($menu['settings']);
        }
        $templateManager->setState(['menu' => $menu]);
    }
    
    public function isUserAdmin($context, $currentUser, $userRoles){
        if ($currentUser && !empty(in_array($userRoles, [ROLE_ID_SITE_ADMIN]))) {
            $userGroupDao = DAORegistry::getDAO('UserGroupDAO');
            $userGroup = $userGroupDao->getByUserId($currentUser->getId(), $context ->getId()); #https://github.com/pkp/jatsTemplate/blob/3778ed29edd396334a2ceb98edbffa37af621dff/JatsTemplateDownloadHandler.php
            if (in_array($userGroup->getRoleId(), [ROLE_ID_SITE_ADMIN])) { #https://github.com/pkp/citationStyleLanguage/blob/7f6233729419cf69d3d68c4e42094f4088865eea/pages/CitationStyleLanguageHandler.inc.php
                return true;
            }
        }
        #if ($currentUser && count(in_array([ROLE_ID_SITE_ADMIN], $userRoles))){
         #   return true;
        #}
        return false;
    }

    /**
     * Provide a name for this plugin
     *
     * The name will appear in the plugins list where editors can
     * enable and disable plugins.
     * 
     * @return string
     */
    public function getDisplayName() {
        return 'plugins.generic.restrictNavigation.displayName';
    }

    /**
    * Provide a description for this plugin
    *
    * The description will appear in the plugins list where editors can
    * enable and disable plugins.
    *
    * @return string
    */
    public function getDescription() {
        return 'plugins.generic.restrictNavigation.description';
    }



}

/***
change loadHandler function with this code:

//Restric user to access some pages when the menu is removed
switch ($requestedPage){
    case 'management':
        $blackListArgs = [
            'context',
            'website',
            'workflow',
            'distribution',
            'access'
        ];
        if (
            ($requestedOp == 'settings' && !empty(array_intersect($blackListArgs, $requestedArgs)))|| $requestedOp == 'tools'
        ) {
            $request -> directHome();
        }
        break;
}
 * 
 * 
 */
