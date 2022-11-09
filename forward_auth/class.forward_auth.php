<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2013 Uwe Steinmann <uwe@steinmann.cx>
*  All rights reserved
*
*  This script is part of the SeedDMS project. The SeedDMS project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/


/**
 * Example extension
 *
 * @author  Eweol <eweol@outlook.com>
 * @package SeedDMS
 * @subpackage  forward_auth
 */
class SeedDMS_ExtForwardAuth extends SeedDMS_ExtBase {

	/**
	 * Initialization
	 *
	 * Use this method to do some initialization like setting up the hooks
	 * You have access to the following global variables:
	 * $this->settings : current global configuration
	 * $this->settings->_extensions['example'] : configuration of this extension
	 * $GLOBALS['LANG'] : the language array with translations for all languages
	 * $GLOBALS['SEEDDMS_HOOKS'] : all hooks added so far
	 */
	function init() { /* {{{ */
		$GLOBALS['SEEDDMS_HOOKS']['initDMS'][] = new SeedDMS_ExtForwardAuth_initDMS;
	} /* }}} */

	function main() { /* {{{ */
	} /* }}} */
}

class SeedDMS_ExtForwardAuth_initDMS { /* {{{ */

	function postInitDMS($array) { /* {{{ */

		/**
		 * Function to Cache all Headers from request
		 */
		function getRequestHeaders() {
			$headers = array();
			foreach($_SERVER as $key => $value) {
				if (substr($key, 0, 5) <> 'HTTP_') {
					continue;
				}
				$header = strtolower(str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5))))));
				$headers[$header] = $value;
			}
			return $headers;
		}

        $extSettings =  $array['settings']->_extensions;
        $settings = $array['settings'];
		$dms = $array['dms'];
		$headers = getRequestHeaders();

        // We bail out if we are not configured
        if(!isset($extSettings["forward_auth"]['forward_authEnable']))
            return;

        // We bail out if we are disabled
        if($extSettings["forward_auth"]['forward_authEnable'] !== "1")
            return;
		

        // We bail out if a valid session already exists
        if(isset($_COOKIE["mydms_session"]))
            return;


		$db = $dms->getDB();
		if(!class_exists('SeedDMS_Session'))
			require_once("../inc/inc.ClassSession.php");

		/**
		 * Get Username out of Header which is set in settings
		 */
        $username = $headers[strtolower($extSettings["forward_auth"]['usernameHeader'])];

		$user = $dms->getUserByLogin($username);

        $userid = $user->getID();

        /* Clear login failures if login was successful */
        $user->clearLoginFailures();

        $lang = $user->getLanguage();
        if (strlen($lang)==0) {
	        $lang = $settings->_language;
	        $user->setLanguage($lang);
        }


        $sesstheme = $user->getTheme();
        if (strlen($sesstheme)==0) {
	        $sesstheme = $settings->_theme;
	        $user->setTheme($sesstheme);
        }

        $session = new SeedDMS_Session($db);

        // Delete all sessions that are more than 1 week or the configured
        // cookie lifetime old. Probably not the most
        // reliable place to put this check -- move to inc.Authentication.php?
        if($settings->_cookieLifetime)
	        $lifetime = intval($settings->_cookieLifetime);
        else
	        $lifetime = 7*86400;
        $session->deleteByTime($lifetime);

        if (isset($_COOKIE["mydms_session"])) {
	        /* This part will never be reached unless the session cookie is kept,
	         * but op.Logout.php deletes it. Keeping a session could be a good idea
	         * for retaining the clipboard data, but the user id in the session should
	         * be set to 0 which is not possible due to foreign key constraints.
	         * So for now op.Logout.php will delete the cookie as always
	         */
	        /* Load session */
	        $dms_session = $_COOKIE["mydms_session"];
	        if(!$resArr = $session->load($dms_session)) {
		        /* Turn off http only cookies if jumploader is enabled */
		        setcookie("mydms_session", $dms_session, time()-3600, $settings->_httpRoot, null, null, !$settings->_enableLargeFileUpload); //delete cookie
		        header("Location: " . $settings->_httpRoot . "out/out.Login.php?referuri=".$refer);
		        exit;
	        } else {
		        $session->updateAccess($dms_session);
		        $session->setUser($userid);
	        }
        } else {
	        // Create new session in database
	        $id = $session->create(array('userid'=>$userid, 'theme'=>$sesstheme, 'lang'=>$lang));

	        // Set the session cookie.
	        if($settings->_cookieLifetime)
		        $lifetime = time() + intval($settings->_cookieLifetime);
	        else
		        $lifetime = 0;
	        setcookie("mydms_session", $id, $lifetime, $settings->_httpRoot, null, null, !$settings->_enableLargeFileUpload);
			$_COOKIE["mydms_session"] = $id;
        }

	} /* }}} */
} /* }}} */
