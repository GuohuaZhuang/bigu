<?php

require_once(APPLICATION_PATH . '/modules/profile/models/DbTable/Profile.php');
require_once(APPLICATION_PATH . '/modules/auth/models/DbTable/Users.php');

class Profile_IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
    	$this->view->profile = true;
    	$profiles = array();
    	$request = $this->getRequest();
    	$username = $request->getParam('username');
    	
    	$db = new Profile_Model_DbTable_Profile();
    	$where = $db->getAdapter()->quoteInto('username=?', $username);
    	$result = $db->fetchAll($where)->toArray();
    	foreach ($result as $item) {
    		$profiles[$item['pname']] = $item['pvalue'];
    	}
    	$this->view->profile_profiles = $profiles;
    	
    	$db_users = new Auth_Model_DbTable_Users();
    	$select = $db_users->getAdapter()->select();
    	$select->where('username=?', $username);
    	$select->from(array('U' => 'tbl_users'), array('username', 'real_name', 'email', 'avatar'));
    	$stmt = $db_users->getAdapter()->query($select);
    	$result_user = $stmt->fetchAll();
    	if (!empty($result_user)) $this->view->profile_users = $result_user[0];
    	else $this->view->profile_users = null;
    }
    
}

