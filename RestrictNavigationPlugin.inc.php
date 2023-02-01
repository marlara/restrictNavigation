<?php

/**
* @file plugins/generic/restrictNavigation/RestrictNavigationPlugin.inc.php
 *
 * Copyright Lara Marziali
 * Distributed under the GNU GPL v3. For full terms see the file LICENSE.
 *
 * @class RestrictNavigationPlugin
 * @ingroup plugins_generic_RestrictNavigation
 *
 * @brief Restric the Navigation of the backend by user roles.
 */

import('lib.pkp.classes.template.PKPTemplateManager');
import('lib.pkp.classes.plugins.GenericPlugin');

class RestrictNavigationPlugin extends GenericPlugin {
    /**
	 * @copydoc GenericPlugin::register()
	 */
	public function register($category, $path, $mainContextId = NULL) {
		$success = parent::register($category, $path, $mainContextId);
		if ($success && $this->getEnabled($mainContextId)) {
			HookRegistry::register('TemplateManager::setupBackendPage', [$this, 'restrictBackendPage']);
        }
		return $success;
	}

    /**
     * Settings: add a getActions() method to your plugin to add a settings action in the plugin list.
     * 
     * 
     */

    public function getActions($request, $actionArgs) {

        // Get the existing actions
            $actions = parent::getActions($request, $actionArgs);
            // Only add the settings action when the plugin is enabled
            if (!$this->getEnabled()) {
                return $actions;
            }
    
        // Create a LinkAction that will call the plugin's
        // `manage` method with the `settings` verb.
            $router = $request->getRouter();
            import('lib.pkp.classes.linkAction.request.AjaxModal');
            $linkAction = new LinkAction(
                'settings',
                new AjaxModal(
                    $router->url(
                        $request,
                        null,
                        null,
                        'manage',
                        null,
                        array(
                            'verb' => 'settings',
                            'plugin' => $this->getName(),
                            'category' => 'generic'
                        )
                    ),
                    $this->getDisplayName()
                ),
                __('manager.plugins.settings'),
                null
            );
    
        // Add the LinkAction to the existing actions.
        // Make it the first action to be consistent with
        // other plugins.
            array_unshift($actions, $linkAction);
    
            return $actions;
        }

        /**
         * Settings: add a manage() method to load a settings form when the LinkAction is clicked.
         */

        public function manage($args, $request) {
            switch ($request->getUserVar('verb')) {
    
            // Return a JSON response containing the
            // settings form
            case 'settings':
                // Load the custom form
                $this->import('RestrictNavigationSettingsForm');
                $form = new RestrictNavigationSettingsForm($this);

                // Fetch the form the first time it loads, before
                // the user has tried to save it
                if (!$request->getUserVar('save')) {
                        $form->initData();
                        return new JSONMessage(true, $form->fetch($request));
                    }

                // Validate and execute the form
                $form->readInputData();
                if ($form->validate()) {
                    $form->execute();
                    return new JSONMessage(true);
                }
		}
		return parent::manage($args, $request);
	}
    

    /**
     * Main function
     * 
     */

    public function restrictBackendPage($hookName, $args)
    {
        /**
         * @copydoc TemplateManager::setupBackendPage()
         * 
         */

        $request = Application::get()->getRequest();
        
        $context = $request->getContext(); #https://docs.pkp.sfu.ca/dev/documentation/en/architecture
        $templateManager = TemplateManager::getManager($request);

        $router = $request->getRouter(); 
        $handler = $router->getHandler(); 
        $userRoles = (array) $handler->getAuthorizedContextObject(ASSOC_TYPE_USER_ROLES); #from https://github.com/pkp/ojs/blob/main/classes/template/TemplateManager.php

        $menu = (array) $templateManager->getState('menu'); #https://github.com/pkp/ops/blob/7a4563933cb965ddad2e2ac2cfab4da9f20ac7a2/pages/authorDashboard/AuthorDashboardHandler.php
        
        $generalSettings = $this->getSetting($context->getId(), 'generalSettings'); #if generalSettings is checked in the Settings form
        $tools = $this->getSetting($context->getId(), 'tools'); #if tools is checked in the Settings form
        $workflow = $this->getSetting($context->getId(), 'workflow'); #if workflow is checked in the Settings form


        if ($context){
            if ($tools){
                if (!$this->isUserAdmin($userRoles)) {
                    unset($menu['tools']);
                }
            }
            if ($workflow){
                if (!$this->isUserAdmin($userRoles)) {
                    unset($menu['settings']['submenu']['workflow']);
                    $request->redirect(null, 'manage', 'tools', 'submissions');
                }
            }
            if ($generalSettings){
                if (!$this->isUserAdmin($userRoles)) {
                    unset($menu['settings']['submenu']['context']);
                    unset($menu['settings']['submenu']['website']);
                    unset($menu['settings']['submenu']['distribution']);
                    unset($menu['settings']['submenu']['access']);
                }
            }
            $templateManager->setState(['menu' => $menu]);
        }
    }
        
    

    

    public function isUserAdmin($userRoles){
        if (in_array(ROLE_ID_SITE_ADMIN, $userRoles)) {
            return true;
        }
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
        return __('plugins.generic.restrictNavigation.displayName');
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
        return __('plugins.generic.restrictNavigation.description');
    }



}

