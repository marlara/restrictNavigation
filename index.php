<?php
/**
 * @defgroup plugins_generic_restrictNavigation
 */
/**
 * @file plugins/generic/restrictNavigation/index.php
 *
 * Copyright (c) Lara Marziali
 * Distributed under the GNU GPL v3. For full terms see the file LICENSE.
 *
 * @ingroup plugins_generic_restrictNavigation
 * @brief Wrapper for the Control Public Files plugin.
 *
 */

require_once('RestrictNavigationPlugin.inc.php');
return new RestrictNavigationPlugin();
