<?php
require_once(APPLICATION_PATH . '/modules/auth/models/DbTable/Users.php');
require_once(APPLICATION_PATH . '/modules/auth/models/DbTable/Roles.php');
require_once(APPLICATION_PATH . '/modules/auth/models/DbTable/Resources.php');
require_once(APPLICATION_PATH . '/modules/auth/models/DbTable/Permissions.php');

class Auth_Plugin_Acladapter extends Zend_Acl
{
	public function __construct($role)
	{
		$this->loadRoles();
		$roles = new Auth_Model_DbTable_Roles();
		$inhRole= $role;
		while (!empty($inhRole)) {
			$this->loadResources($inhRole);
			$this->loadPermissions($inhRole);
			$inhRole= $roles->getParentRole($inhRole);
		}
	}
	
	public function loadRoles() {
		$roles = new Auth_Model_DbTable_Roles();
		$result = $roles->getRoles();
		foreach ($result as $role) {
			if (!empty($role['id_parent'])) {
				// echo '正在addRole(' . $role['id'] . ') 继承于 (' . $role['id_parent'] . ')<br/>';
				$this->addRole($role['id'], $role['id_parent']);
			} else {
				// echo '正在addRole(' . $role['id'] . ')<br/>';
				$this->addRole($role['id']);
			}
		}
	}
	
	public function loadResources($role) {
		$resources = new Auth_Model_DbTable_Resources();
		$result = $resources->getResources($role);
		foreach ($result as $resource) {
			if (!$this->has($resource['resource'])) {
				$this->addResource($resource['resource']);
			}
		}
	}
	
	public function loadPermissions($role) {
		$resources = new Auth_Model_DbTable_Permissions();
		$result = $resources->getPermissions($role);
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