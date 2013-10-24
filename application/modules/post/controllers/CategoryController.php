<?php

require_once(APPLICATION_PATH . '/modules/post/models/DbTable/Category.php');

class Post_CategoryController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function listAction()
    {
    	$this->view->manage = true;
    	$db_category = new Post_Model_DbTable_Category();
    	$where = $db_category->getAdapter()->quoteInto('parent_category=?', '');
    	$result = $db_category->fetchAll($where, 'parent_category')->toArray();
    	$this->view->result = $result;
    	$_oi = 0;
    	$json = '{"success": "OK", "d": [';
    	foreach ($result as $record) {
    		if (0 != $_oi) $json .= ', ';
    		$subcategorys = $db_category->getAllSubCategory($record['category']);
    		$data_json = "";
    		$data_count = count($subcategorys);
    		for($i = 0; $i < $data_count; $i ++) {
    			if (0 != $i) $data_json .= ',';
    			$data_json .= '"';
    			$data_json .= $subcategorys[$i];
    			$data_json .= '"';
    		}
    		$json .= '{"category": "'.$record['category'].'", "subcategorys": [' . $data_json . '] }';
    		$_oi ++;
    	}
    	$json .= '] }';
    	echo $json;
    	
    	// stop layout and render
    	$this->_helper->layout->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(TRUE);
    }
    
    public function indexAction()
    {
        $this->view->manage = true;
        $db_category = new Post_Model_DbTable_Category();
        $result = $db_category->fetchAll(null, 'parent_category')->toArray();
        $this->view->result = $result;
    }

    
    public function addAction()
    {
    	$request = $this->getRequest();
    	$category = $request->getParam('category');
    	$parent_category = $request->getParam('parent_category');
    	if (empty($category)) {
    		echo '{"err": "添加失败，参数不足"}';
    	} else {
    		$db_category = new Post_Model_DbTable_Category();
    		$data = array('category' => $category, 'parent_category' => $parent_category);
    		$where = $db_category->getAdapter()->quoteInto('category=?', $category);
    		if (!$db_category->fetchRow($where)) {
    			$result = $db_category->insert($data);
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
    	$category = $request->getParam('category');
    	if (empty($category)) {
    		echo '{"err": "删除失败，参数不足"}';
    	} else {
    		$db_category = new Post_Model_DbTable_Category();
    		$where = $db_category->getAdapter()->quoteInto('category=?', $category);
    		if ($db_category->delete($where) == 1) {
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
    	$db_category = new Post_Model_DbTable_Category();
    	foreach ($d as $dline) {
    		$data = array('parent_category' => $dline['parent_category']);
    		$where = $db_category->getAdapter()->quoteInto('category = ?', $dline['category']);
    		$db_category->update($data, $where);
    	}
    	echo '{"success": "OK"}';
    	// stop layout and render
    	$this->_helper->layout->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(TRUE);
    }
    
    public function subcategoryAction()
    {
    	$request = $this->getRequest();
    	$category = $request->getParam('category');
    	if (empty($category)) {
    		echo '{"err": "参数不足"}';
    	} else {
	    	$db_category = new Post_Model_DbTable_Category();
	    	$subcategorys = $db_category->getAllSubCategory($category);
	    	$data_json = "";
	    	$data_count = count($subcategorys);
	    	for($i = 0; $i < $data_count; $i ++) {
	    		if (0 != $i) $data_json .= ',';
	    		$data_json .= '"';
	    		$data_json .= $subcategorys[$i];
	    		$data_json .= '"';
	    	}
	    	$json = '{"success": "OK", "subcategorys": [' . $data_json . '] }';
	    	echo $json;
    	}
    	// stop layout and render
    	$this->_helper->layout->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(TRUE);
    }
}

