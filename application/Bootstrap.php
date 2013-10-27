<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	protected function _initAuthvalid() {
		$fc = Zend_Controller_Front::getInstance ();
		require(APPLICATION_PATH . '/modules/auth/plugins/Valid.php');
		$fc->registerPlugin ( new Auth_Plugin_Valid() );
	}
	protected function __initSession() {
		ini_set('session.gc_maxlifetime', 1209600);  // set session max lifetime seconds
		ini_set('session.gc_divisor', 1);       // 100% chance of running GC
		Zend_Session::start();
		// So, I can have in my applicatin.ini
		// phpSettings.session.gc_maxlifetime = 1209600
	}
}
