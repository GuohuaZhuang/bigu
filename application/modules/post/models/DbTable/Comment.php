<?php

class Post_Model_DbTable_Comment extends Zend_Db_Table_Abstract
{

    protected $_name = 'tbl_comment';
    
    public function fetchAllByPostid($id_post) {
    	$select = $this->getAdapter()->select();
    	$select->from(array('C' => 'tbl_comment'), array('id', 'id_post', 'content', 'pub_datetime', 'author'));
    	$select->joinLeft(array('U' => 'tbl_users'), 'C.author=U.username', array('avatar'));
    	$select->where('C.id_post=?', $id_post);
    	$select->order('pub_datetime ASC');
    	$stmt = $this->getAdapter()->query($select);
    	return $stmt->fetchAll();
    }
    
    public function fetchAllByAuthor($author, $page = 1, $count = 10) {
    	if (empty($page) || $page <= 0) $page = 1;
    	if (empty($count) || $count <= 0) $count = 10;
    	$select = $this->getAdapter()->select();
    	$select->from(array('C' => 'tbl_comment'), array('id', 'id_post', 'content', 'pub_datetime', 'author'));
    	$select->joinLeft(array('U' => 'tbl_users'), 'C.author=U.username', array('avatar'));
    	$select->joinLeft(array('P' => 'tbl_post'), 'C.id_post=P.id', array('title'));
    	if (!empty($author)) $select->where('C.author=?', $author);
    	$select->order('pub_datetime DESC');
    	$select->limitPage($page, $count);
    	$stmt = $this->getAdapter()->query($select);
    	return $stmt->fetchAll();
    }
    
    public function fetch_count($author = null) {
    	$select = $this->getAdapter()->select();
    	$select->from(array('C' => 'tbl_comment'), array('COUNT(*) as count'));
    	if (!empty($author)) $select->where('author=?', $author);
    	$stmt = $this->getAdapter()->query($select);
    	$result = $stmt->fetch();
    	return $result['count'];
    }
}

