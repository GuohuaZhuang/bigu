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
    
    public function removeuploadAction()
    {
    	$json = '';
    	try {
    		$request = $this->getRequest();
    		$data = $request->getParams();
    		// remove image file
    		if (isset($data['profile_image']) && !empty($data['profile_image'])) {
    			$profile_image = str_replace("./upload/",
    					APPLICATION_PATH . "/../public/util/jquery_php_avatar/upload/",
    					$data['profile_image']);
    			unlink($profile_image);
    			$json = '{"success": "删除上传头像图片完成"}';
    		} else {
    			$json = '{"err": "删除上传头像图片失败"}';
    		}
    	} catch (Exception $e) {
    		$json = '{"err": "删除上传头像图片失败"}';
    	}
    	echo $json;
    	// stop layout and render
    	$this->_helper->layout->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(TRUE);
    }
    
    public function removethumbAction()
    {
    	$json = '';
    	try {
	    	$request = $this->getRequest();
	    	$data = $request->getParams();
	    	// remove image file
	    	if (isset($data['profile_avatar']) && !empty($data['profile_avatar'])) {
	    		$profile_avatar = str_replace("./thumb/",
	    				APPLICATION_PATH . "/../public/util/jquery_php_avatar/thumb/",
	    				$data['profile_avatar']);
	    		unlink($profile_avatar);
	    		$json = '{"success": "删除旧头像图片完成"}';
	    	} else {
	    		$json = '{"err": "删除旧头像图片失败"}';
	    	}
    	} catch (Exception $e) {
    		$json = '{"err": "删除上传头像图片失败"}';
    	}
    	echo $json;
    	// stop layout and render
    	$this->_helper->layout->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(TRUE);
    }
    
    public function saveavatarAction()
    {
    	$request = $this->getRequest();
    	$data = $request->getParams();
    	
    	// 保证当前用户登录
    	$username = Util_Global::getUsername();
    	if (empty($username) || empty($data['profile_avatar']) 
    			|| strpos($data['profile_avatar'], '/thumb/') == FALSE) {
    		echo '{"err": "更新图像数据失败"}';
    	} else {
	    	$data['profile_avatar'] = str_replace("./thumb/","/util/jquery_php_avatar/thumb/",
	    			$data['profile_avatar']);
	    	
	    	$db_user= new Auth_Model_DbTable_Users();
	    	$where = $db_user->getAdapter()->quoteInto('username=?', $username);
	    	$db_user->update(array('avatar' => $data['profile_avatar']), $where);
	    	echo '{"success": "更新图像数据成功"}';
	    	// remove image file
	    	if (isset($data['profile_image']) && !empty($data['profile_image'])) {
	    		$profile_image = str_replace("./upload/", 
	    			APPLICATION_PATH . "/../public/util/jquery_php_avatar/upload/",
	    			$data['profile_image']);
	    		@unlink($profile_image);
	    	}
    	}
    	// stop layout and render
    	$this->_helper->layout->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(TRUE);
    }
    
    public function saveAction()
    {
    	$request = $this->getRequest();
    	$data = $request->getParams();
    	
    	// 保证当前用户登录
    	$username = Util_Global::getUsername();
    	if (empty($username)) {
    		$this->view->error = '当前用户没有登录';
    		return false;
    	}
    	
    	$db_user= new Auth_Model_DbTable_Users();
    	$where = $db_user->getAdapter()->quoteInto('username=?', $username);
    	$db_user->update(array('real_name' => $data['profile_real_name'], 
    			'email' => $data['profile_email']), $where);
    	
    	$pdata = array(
    			'gender' => $data['profile_gender'], 
    			'address' => $data['profile_address'], 
    			'intro' => $data['profile_intro'], 
    			'city' => $data['profile_city'], 
    			'weibo' => $data['profile_weibo'], 
    			'qq' => $data['profile_qq'], 
    			'phone' => $data['profile_phone'], 
    			'company' => $data['profile_company'], 
    			'title' => $data['profile_title'], 
    			'industry' => $data['profile_industry'], 
    			'homepage' => $data['profile_homepage']);
    	$db = new Profile_Model_DbTable_Profile();
    	foreach ($pdata as $key => $value) {
	    	$where = array();
	    	$where[] = $db->getAdapter()->quoteInto('username=?', $username);
	    	$where[] = $db->getAdapter()->quoteInto('pname=?', $key);
	    	$isexits = $db->fetchRow($where);
	    	if (!empty($isexits)) {
	    		$db->update(array('pvalue' => $value), $where);
	    	} else {
	    		$db->insert(array(
    				'username' => $username, 
    				'pname' => $key, 
    				'pvalue' => $value));
	    	}
    	}
    	$this->redirect('/profile/index/index');
    }
    
    public function avatarAction()
    {
    	$this->view->profile = true;
    	$profiles = array();
    	
    	$username = Util_Global::getUsername();
    	if (empty($username)) {
    		return false;
    	}
    	
    	$db_users = new Auth_Model_DbTable_Users();
    	$select = $db_users->getAdapter()->select();
    	$select->where('username=?', $username);
    	$select->from(array('U' => 'tbl_users'),
    			array('username', 'avatar'));
    	$stmt = $db_users->getAdapter()->query($select);
    	$result_user = $stmt->fetchAll();
    	if (!empty($result_user)) $this->view->profile_users = $result_user[0];
    	else $this->view->profile_users = null;
    }
    
    public function indexAction()
    {
    	$this->view->profile = true;
    	
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
    	$view_profiles = array(
    			'gender' => isset($profiles['gender'])?$profiles['gender']:'',
    			'address' => isset($profiles['address'])?$profiles['address']:'',
    			'intro' => isset($profiles['intro'])?$profiles['intro']:'',
    			'city' => isset($profiles['city'])?$profiles['city']:'',
    			'weibo' => isset($profiles['weibo'])?$profiles['weibo']:'',
    			'qq' => isset($profiles['qq'])?$profiles['qq']:'',
    			'phone' => isset($profiles['phone'])?$profiles['phone']:'',
    			'company' => isset($profiles['company'])?$profiles['company']:'',
    			'title' => isset($profiles['title'])?$profiles['title']:'',
    			'industry' => isset($profiles['industry'])?$profiles['industry']:'',
    			'homepage' => isset($profiles['homepage'])?$profiles['homepage']:'');
    	$this->view->profile_profiles = $view_profiles;
    	
    	$db_users = new Auth_Model_DbTable_Users();
    	$select = $db_users->getAdapter()->select();
    	$select->where('username=?', $username);
    	$select->from(array('U' => 'tbl_users'), 
    			array('username', 'real_name', 'email', 'avatar'));
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
