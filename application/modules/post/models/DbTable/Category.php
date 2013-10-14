<?php

class Post_Model_DbTable_Category extends Zend_Db_Table_Abstract
{

    protected $_name = 'tbl_category';
    
    public function getAllBigCategory() {
    	$select = $this->select();
    	$select->from($this, array('category'));
    	$select->where('parent_category=""');
    	$retarr = $this->fetchAll($select)->toArray();
    	$rets = array();
    	foreach ($retarr as $onerow) {
    		$rets[] = $onerow['category'];
    	}
    	return $rets;
    }

    public function getAllSubCategory($category) {
    	$select = $this->select();
    	$select->from($this, array('category'));
    	$select->where('parent_category=?', $category);
    	$retarr = $this->fetchAll($select)->toArray();
    	$rets = array();
    	foreach ($retarr as $onerow) {
    		$rets[] = $onerow['category'];
    	}
    	return $rets;
    }
}

