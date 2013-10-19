<?php

require_once(APPLICATION_PATH . '/modules/profile/models/DbTable/Profile.php');
require_once(APPLICATION_PATH . '/modules/auth/models/DbTable/Users.php');
require_once(APPLICATION_PATH . '/util/Global.php');
require_once(APPLICATION_PATH . '/modules/auth/plugins/Authadapter.php');

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
    	
    	$username = Util_Global::getUsername();
    	if (empty($username)) {
    		return false;
    	}
    	
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
    
    public function pwdmodAction()
    {
    	$this->view->profile = true;
    	$profiles = array();
    	$request = $this->getRequest();
    	$old_pwd = $request->getParam('profile_old_pwd');
    	$new_pwd = $request->getParam('profile_new_pwd');
    	$new_pwd_again = $request->getParam('profile_new_pwd_again');
    	if (empty($old_pwd) || empty($new_pwd) || empty($new_pwd_again) ) return true;
    	if ($new_pwd != $new_pwd_again) {
    		$this->view->error = '两次填写新密码不一致';
    		return false;
    	} else if ($old_pwd == $new_pwd) {
    		$this->view->error = '旧密码和新密码一样，你到底要闹哪样呢';
    		return false;
    	}
    	// 保证当前用户登录
    	$username = Util_Global::getUsername();
    	if (empty($username)) {
    		$this->view->error = '当前用户没有登录';
    		return false;
    	}
    	// 验证旧密码
    	$auth_adapter = new Auth_Plugin_Authadapter();
    	$auth_adapter->setIdentity($username);
    	$auth_adapter->setCredential($old_pwd);
    	$result = $auth_adapter->authenticate();
    	if ($result->isValid()) {
    		$data = array();
    		$data['password_salt'] = Util_Global::generateSalt();
    		$data['password'] = sha1($data['password_salt'] . $new_pwd);
    		$db_user= new Auth_Model_DbTable_Users();
    		$where = $db_user->getAdapter()->quoteInto('username=?', $username);
    		$db_user->update($data, $where);
    		$this->view->success = '密码修改成功！';
    		return true;
    	} else {
    		$this->view->error = '旧密码输入错误';
    		return false;
    	}
    }
    
}
