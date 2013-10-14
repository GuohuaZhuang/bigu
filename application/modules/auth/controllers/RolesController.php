<?php

require_once(APPLICATION_PATH . '/modules/auth/models/DbTable/Roles.php');

class Auth_RolesController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $this->view->manage = true;
        $roles = new Auth_Model_DbTable_Roles();
        $result = $roles->fetchAll(null, 'id')->toArray();
        $this->view->result = $result;
    }
    
    public function addAction()
    {
    	$request = $this->getRequest();
    	$role = $request->getParam('role');
    	$id_parent = $request->getParam('id_parent');
    	if (empty($role) || ($id_parent == null)) {
    		echo '{"err": "添加失败，参数不足"}';
    	} else {
	    	$roles = new Auth_Model_DbTable_Roles();
	    	$data = array('role' => $role, 'id_parent' => $id_parent);
	    	$where = $roles->getAdapter()->quoteInto('role=?', $role);
	    	if (!$roles->fetchRow($where)) {
	    		$result = $roles->insert($data);
	    		echo '{"success": "添加成功", "id": "'.$result.'"}';
	    	} else {
	    		echo '{"err": "该角色已经存在"}';
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
    		$roles = new Auth_Model_DbTable_Roles();
	    	$where = $roles->getAdapter()->quoteInto('id=?', $id);
	    	if ($roles->delete($where) == 1) {
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
    	$roles = new Auth_Model_DbTable_Roles();
    	foreach ($d as $dline) {
    		$data = array('role' => $dline['role'], 'id_parent' => $dline['id_parent']);
    		$where = $roles->getAdapter()->quoteInto('id = ?', $dline['id']);
    		$roles->update($data, $where);
    	}
    	echo '{"success": "OK"}';
    	// stop layout and render
    	$this->_helper->layout->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(TRUE);
    }
    
}
