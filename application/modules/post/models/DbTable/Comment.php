<?php

class Post_Model_DbTable_Comment extends Zend_Db_Table_Abstract
{

    protected $_name = 'tbl_comment';

    
    public function fetchAllByPostid($id_post) {
    	$select = $this->select();
    	$select->from(array('r' => 'tbl_comment'));
    	$select->where('r.id_post=?', $id_post);
    	
    	$result = $this->fetchAll($select, 'pub_datetime ASC');
    	return $result;
    }
    
}

