<?php

class Auth_Model_DbTable_Users extends Zend_Db_Table_Abstract
{
    protected $_name = 'tbl_users';
    protected $_primary = 'username';

// 	public static function save_record() {
// 		echo '这里是save_record area<br/>';
// 	}
	public function insert($data) {
		$new_data = array();
		if (isset($data['username'])) $new_data['username'] = $data['username'];
		if (isset($data['password'])) $new_data['password'] = $data['password'];
		if (isset($data['password_salt'])) $new_data['password_salt'] = $data['password_salt'];
		if (isset($data['real_name'])) $new_data['real_name'] = $data['real_name'];
		if (isset($data['email'])) $new_data['email'] = $data['email'];
		if (isset($data['status'])) $new_data['status'] = $data['status'];
		if (isset($data['id_role'])) $new_data['id_role'] = $data['id_role'];
		parent::insert($new_data);
	}
	public function getRoleId($username) {
		if (empty($username)) {
			return false;
		}
		$result= $this->find($username);
		if (!empty($result)) {
			return $result[0]['id_role'];
		}
		return false;
	}
	public function getRealname($username) {
		if (empty($username)) {
			return false;
		}
		$result= $this->find($username);
		if (!empty($result)) {
			return $result[0]['real_name'];
		}
		return false;
	}
	public function hasEmail($email) {
		if (empty($email)) {
			return false;
		}
		
		$where = $this->select()->where('email=?', $email);
		$result= $this->fetchRow($where);
		if (!empty($result)) {
			return true;
		}
		return false;
	}
	public function hasUsername($username) {
		if (empty($username)) {
			return false;
		}
		
		$where = $this->select()->where('username=?', $username);
		$result= $this->fetchRow($where);
		if (!empty($result)) {
			return true;
		}
		return false;
	}
	public function getSingleWithEmailHash($email, $str) {
		if (empty($email)) {
			return null;
		}
		$where = $this->select()->where('email=?', $email);
		$result= $this->fetchRow($where);
		if (!empty($result) && sha1($email) == $str) {
			return $result;
		}
		return null;
	}
	public function getSingleWithEmail($email) {
		if (empty($email)) {
			return null;
		}
		$where = $this->select()->where('email=?', $email);
		$result= $this->fetchRow($where);
		if (!empty($result)) {
			return $result;
		}
		return null;
	}
	public function edit($email, $data) {
		if (empty($email)) {
			return false;
		}
		
// 		$where = $this->select()->where('email=?', $email);
		$where = $this->getAdapter()->quoteInto('email=?', $email);
		$num = $this->update($data, $where);
		if (!empty($num) && $num > 0) {
			return true;
		}
		return false;
	}
	public function fetchAllR() {
		$select = $this->getAdapter()->select();
		$select->from(array('U' => 'tbl_users'), array('username', 'real_name', 'email', 'status', 'id_role'));
		$select->join(array('R' => 'tbl_roles'), 'U.id_role=R.id', array('role'));
		
		$stmt = $this->getAdapter()->query($select);
		return $stmt->fetchAll();
	}
	public function fetchAvatar($username) {
		if (empty($username)) return null;
		$select = $this->select();
		$select->from(array('U' => 'tbl_users'), array('avatar'));
		$select->where('U.username=?', $username);
		$result = $this->fetchRow($select);
    	if (!empty($result) && isset($result['avatar'])) {
    		return $result['avatar'];
    	}
    	return null;
	}
}
