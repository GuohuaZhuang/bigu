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
    
}

