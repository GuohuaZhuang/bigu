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
}
