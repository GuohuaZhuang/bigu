<?php
class Auth_Plugin_Authadapter extends Zend_Auth_Adapter_DbTable
{
	public function __construct()
	{
		$this->_zendDb = Zend_Db_Table::getDefaultAdapter ();
		$this->setTableName('tbl_users');
		$this->setIdentityColumn('username');
		$this->setCredentialColumn ('password');
		$this->setCredentialTreatment ('SHA1(CONCAT(password_salt,?))');
	}
}
