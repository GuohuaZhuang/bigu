<?php

require(APPLICATION_PATH . '/modules/auth/models/DbTable/Roles.php');
require(APPLICATION_PATH . '/modules/auth/models/DbTable/Resources.php');
require(APPLICATION_PATH . '/modules/auth/models/DbTable/Permissions.php');

class Auth_PermissionsController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $this->view->manage = true;
        $permissions = new Auth_Model_DbTable_Permissions();
        $result = $permissions->fetchAllUR();
        $this->view->result = $result;
        
        $roles = new Auth_Model_DbTable_Roles();
        $rolenames = $roles->getAllRolename();
        $this->view->rolenames = $rolenames;
        
        $resources = new Auth_Model_DbTable_Resources();
        $resourcenames = $resources->getAllResourcename();
        $this->view->resourcenames = $resourcenames;
    }
    
    public function addAction()
    {
    	$request = $this->getRequest();
    	$id_role = $request->getParam('id_role');
    	$id_resource = $request->getParam('id_resource');
    	$permission = $request->getParam('permission');
    	if (empty($id_role) || empty($id_resource) || empty($permission)) {
    		echo '{"err": "添加失败，参数不足"}';
    	} else {
    		$permissions = new Auth_Model_DbTable_Permissions();
    		$data = array('id_role' => $id_role, 'id_resource' => $id_resource, 'permission' => $permission);
    		$where = array();
    		$where[] = $permissions->getAdapter()->quoteInto('id_role=? ', $id_role);
    		$where[] = $permissions->getAdapter()->quoteInto('id_resource=?', $id_resource);
    		if (!$permissions->fetchRow($where)) {
    			$result = $permissions->insert($data);
    			echo '{"success": "添加成功", "id": "'.$result.'"}';
    		} else {
    			echo '{"err": "该权限控制已经存在"}';
    		}
    	}
    	// stop layout and render
    	$this->_helper->layout->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(TRUE);
    }
    
    public function deleteAction()
    {
    	$request = $this->getRequest();
    	$id = $request->getParam('id');
    	if (empty($id)) {
    		echo '{"err": "删除失败，参数不足"}';
    	} else {
    		$permissions = new Auth_Model_DbTable_Permissions();
    		$where = $permissions->getAdapter()->quoteInto('id=?', $id);
    		if ($permissions->delete($where) == 1) {
    			echo '{"success": "删除成功"}';
    		} else {
    			echo '{"err": "删除失败，没有这个角色"}';
    		}
    	}
    	// stop layout and render
    	$this->_helper->layout->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(TRUE);
    }
    
    public function updateAction()
    {
    	$request = $this->getRequest();
    	$d = $request->getParam('d');
    	$permissions = new Auth_Model_DbTable_Permissions();
    	foreach ($d as $dline) {
    		$data = array('id_role' => $dline['id_role'], 'id_resource' => $dline['id_resource'], 'permission' => $dline['permission']);
    		$where = $permissions->getAdapter()->quoteInto('id = ?', $dline['id']);
    		$permissions->update($data, $where);
    	}
    	echo '{"success": "OK"}';
    	// stop layout and render
    	$this->_helper->layout->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(TRUE);
    }
}
