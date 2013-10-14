<?php

class Auth_Model_DbTable_Permissions extends Zend_Db_Table_Abstract
{

    protected $_name = 'tbl_permissions';

	public function getPermissions($role) {
		$select = $this->getAdapter()->select();
		$select->from(array('p' => 'tbl_permissions'));
		$select->join(array('r' => 'tbl_resources'), 'r.id=p.id_resource');
		if (!empty($role)) {
			$select->where('p.id_role=?', $role);
		}
		
		$stmt = $this->getAdapter()->query($select);
		return $stmt->fetchAll();
	}
	
// 	public function getPermissions() {
// 		return $this->fetchAll(null,'id');
// 	}
	
	public function fetchAllUR() {
		$select = $this->getAdapter()->select();
		$select->from(array('P' => 'tbl_permissions'), array('id', 'permission'));
		$select->join(array('R' => 'tbl_roles'), 'P.id_role=R.id', array('role'));
		$select->join(array('RE' => 'tbl_resources'), 'P.id_resource=RE.id', array('resource'));
		$select->order('id');
		
		$stmt = $this->getAdapter()->query($select);
		return $stmt->fetchAll(null, 'id');
	}
}
