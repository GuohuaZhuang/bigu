<?php

class Post_Model_DbTable_Post extends Zend_Db_Table_Abstract
{

    protected $_name = 'tbl_post';
    
    public function get_index_thumb($id) {
    	if (empty($id)) return false;
    	$select = $this->getAdapter()->select();
    	$select->from(array('P' => 'tbl_post'), array('index_thumb'));
		$select->where('id=?', $id);
    	$stmt = $this->getAdapter()->query($select);
    	$result = $stmt->fetch();
    	if (!empty($result)) {
    		return $result['index_thumb'];
    	}
    	return null;
    }
    
    public function fetch_list($page = 1, $count = 10, $author = null, $category = null, $sub_category = null) {
    	if (empty($page) || $page <= 0) $page = 1;
    	if (empty($count) || $count <= 0) $count = 10;
    	$select = $this->getAdapter()->select();
    	$select->from(array('P' => 'tbl_post'), 
    		array('id', 'title', 'abstract', 'pub_datetime', 'author', 
    			'category', 'sub_category', 'source', 'view_count', 
    			'comment_count', 'index_thumb'));
    	if (!empty($author)) $select->where('author=?', $author);
    	if (!empty($category)) $select->where('category=?', $category);
    	if (!empty($sub_category)) $select->where('sub_category=?', $sub_category);
    	$stmt = $this->getAdapter()->query($select);
    	$select->order('pub_datetime DESC');
    	$select->limitPage($page, $count);
    	$stmt = $this->getAdapter()->query($select);
    	return $stmt->fetchAll();
    }
    
    public function fetch_count($author = null, $category = null, $sub_category = null) {
    	$select = $this->getAdapter()->select();
    	$select->from(array('P' => 'tbl_post'), array('COUNT(*) as count'));
    	if (!empty($author)) $select->where('author=?', $author);
    	if (!empty($category)) $select->where('category=?', $category);
    	if (!empty($sub_category)) $select->where('sub_category=?', $sub_category);
		// ->where('bug_status = ?', 'NEW')
		// ->group('reported_by');
    	$stmt = $this->getAdapter()->query($select);
    	$result = $stmt->fetch();
    	return $result['count'];
    }
}
