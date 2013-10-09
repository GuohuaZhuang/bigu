<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	protected function _initAuthvalid() {
		$fc = Zend_Controller_Front::getInstance ();
		require(APPLICATION_PATH . '/modules/auth/plugins/Valid.php');
		$fc->registerPlugin ( new Auth_Plugin_Valid() );
	}
}

