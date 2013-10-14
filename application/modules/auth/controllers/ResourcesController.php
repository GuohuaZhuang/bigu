<?php

require(APPLICATION_PATH . '/modules/auth/models/DbTable/Resources.php');

class Auth_ResourcesController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $this->view->manage = true;
        $resources = new Auth_Model_DbTable_Resources();
        $result = $resources->fetchAll(null, 'id')->toArray();
        $this->view->result = $result;
    }
    
    public function addAction()
    {
    	$request = $this->getRequest();
    	$resource = $request->getParam('resource');
    	if (empty($resource)) {
    		echo '{"err": "添加失败，参数不足"}';
    	} else {
    		$resources = new Auth_Model_DbTable_Resources();
    		$data = array('resource' => $resource);
    		$where = $resources->getAdapter()->quoteInto('resource=?', $resource);
    		if (!$resources->fetchRow($where)) {
    			$result = $resources->insert($data);
    			echo '{"success": "添加成功", "id": "'.$result.'"}';
    		} else {
    			echo '{"err": "该资源已经存在"}';
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
    		$resources = new Auth_Model_DbTable_Resources();
    		$where = $resources->getAdapter()->quoteInto('id=?', $id);
    		if ($resources->delete($where) == 1) {
    			echo '{"success": "删除成功"}';
    		} else {
    			echo '{"err": "删除失败，没有这个资源"}';
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
    	$resources = new Auth_Model_DbTable_Resources();
    	foreach ($d as $dline) {
    		$data = array('resource' => $dline['resource']);
    		$where = $resources->getAdapter()->quoteInto('id = ?', $dline['id']);
    		$resources->update($data, $where);
    	}
    	echo '{"success": "OK"}';
    	// stop layout and render
    	$this->_helper->layout->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(TRUE);
    }
    
}