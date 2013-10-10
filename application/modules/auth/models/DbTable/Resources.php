<?php

class Auth_Model_DbTable_Resources extends Zend_Db_Table_Abstract
{

    protected $_name = 'tbl_resources';

// 	public function getResources($role) {
// 		$select = $this->getAdapter()->select();
// 		$select->from(array('r' => 'tbl_resources'));
// 		$select->join(array('p' => 'tbl_permissions'), 'r.id=p.id_resource');
// 		if (!empty($role)) {
// 			$select->where('p.id_role=?', $role);
// 		}
		
// 		$stmt = $this->getAdapter()->query($select);
// 		return $stmt->fetchAll();
// 	}
	public function getResources() {
		return $this->fetchAll(null,'id');
	}
}
