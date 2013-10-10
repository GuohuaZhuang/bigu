<?php
require(APPLICATION_PATH . '/modules/auth/models/DbTable/Users.php');
require(APPLICATION_PATH . '/modules/auth/models/DbTable/Roles.php');
require(APPLICATION_PATH . '/modules/auth/models/DbTable/Resources.php');
require(APPLICATION_PATH . '/modules/auth/models/DbTable/Permissions.php');

class Auth_Plugin_Acladapter extends Zend_Acl
{
	public function __construct()
	{
		$this->loadRoles();
		$this->loadResources();
		$this->loadPermissions();
	}
	
	public function loadRoles() {
		$roles = new Auth_Model_DbTable_Roles();
		$result = $roles->getRoles();
		foreach ($result as $role) {
			if (!empty($role['id_parent'])) {
				$this->addRole($role['id'], $role['id_parent']);
			} else {
				$this->addRole($role['id']);
			}
		}
	}
	
	public function loadResources() {
		$resources = new Auth_Model_DbTable_Resources();
		$result = $resources->getResources();
		foreach ($result as $resource) {
			$this->addResource($resource['resource']);
		}
	}
	
	public function loadPermissions() {
		$resources = new Auth_Model_DbTable_Permissions();
		$result = $resources->getPermissions();
		foreach ($result as $permission) {
			if ($permission['permission'] == 'allow') {
				$this->allow($permission['id_role'], $permission['resource']);
				// echo '正在allow(' . $permission['id_role'] . ') ==> (' . $permission['resource'] . ')<br/>';
			} else {
				$this->deny($permission['id_role'], $permission['resource']);
				// echo '正在deny(' . $permission['id_role'] . ') ==> (' . $permission['resource'] . ')<br/>';
			}
		}
	}
	
}