<?php

class Auth_Model_DbTable_Roles extends Zend_Db_Table_Abstract
{

    protected $_name = 'tbl_roles';

	public function getRoles() {
		return $this->fetchAll(null, 'id');
	}
	
	public function getAllRolename() {
		$select = $this->select();
		$select->from($this, array('role', 'id'));
		$rolearr = $this->fetchAll($select)->toArray();
		$roles = array();
		foreach ($rolearr as $rolerow) {
			$roles[$rolerow['id']] = $rolerow['role'];
		}
		return $roles;
	}
	
	public function getParentRole($role) {
		$select = $this->select('id_parent');
		$select->from(array('r' => 'tbl_roles'));
		$select->where('r.id=?', $role);
		
		$result = $this->fetchRow($select);
		if (!empty($result['id_parent'])) {
			return $result['id_parent'];
		}
		return false;
	}
}

