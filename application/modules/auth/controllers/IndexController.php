<?php

require_once(APPLICATION_PATH . '/modules/auth/models/DbTable/Users.php');
require_once(APPLICATION_PATH . '/modules/auth/forms/Login.php');
require_once(APPLICATION_PATH . '/modules/auth/plugins/Authadapter.php');
require_once(APPLICATION_PATH . '/modules/auth/forms/Register.php');
require_once(APPLICATION_PATH . '/util/Global.php');
require_once(APPLICATION_PATH . '/modules/auth/plugins/Acladapter.php');

class Auth_IndexController extends Zend_Controller_Action
{

    private $_tbl_user = null;

    private function _getTableUser()
    {
		return $this->_tbl_user;
    }

    private function _setTableUser($user)
    {
		$this->_tbl_user = $user;
    }

    public function init()
    {
    }

    public function indexAction()
    {
    }

    public function loginAction()
    {
    	// 如果用户已经登录就跳转到用户中心页面
    	$auth = Zend_Auth::getInstance();
    	if ($auth->hasIdentity()) {
    		$redirect = '/post/post/list';
    		$this->redirect($redirect);
    		// $this->_forward('index');
    		return;
    	}
    	
    	// 确定一下登录完成后应该跳转的页面
    	$request = $this->getRequest();
    	$redirect = $request->getPost('redirect');
    	if (strlen($redirect) == 0)
    		$redirect = $request->getServer('REQUEST_URI');
    	if (strlen($redirect) == 0)
    		$redirect = '/post/post/list';
//     		$redirect = '/auth/index/index';
    	
    	// 读取表单
    	$form = new Auth_Form_Login();
    	if (!$form->isValid($_POST)) {
    		$this->view->form = $form;
    		return;
    	}
    	$username = $this->getRequest()->getParam('username');
    	$password = $this->getRequest()->getParam('password');
    	
    	// 使用Auth_Adapter来实现验证
        $auth_adapter = new Auth_Plugin_Authadapter();
        $auth_adapter->setIdentity($username);
        $auth_adapter->setCredential($password);
        // $auth = Zend_Auth::getInstance();
        $result = $auth->authenticate($auth_adapter);

        if ($result->isValid()) {
        	// 验证成功的话把用户身份存储到auth的storage中
        	$user= new Auth_Model_DbTable_Users();
        	$role_id = $user->getRoleId($username);
        	$real_name = $user->getRealname($username);
        	$data= array(
        		'username' => $username,
        		'id_role'  => $role_id,
        		'real_name' => $real_name
        	);
        	$isremember = $request->getPost('isremember');
        	if (empty($isremember)) {
        		Zend_Session::forgetMe(); // 下次不自动登录
        	} else {
        		Zend_Session::rememberMe(1209600); // 下次自动登录(默认rememberMeSeconds = 1209600; // 2 weeks)
        	}
        	$auth->getStorage()->write($data);
        	
        	// 清除register [Note because registry is just live in a request, not global]
        	// $registry = Zend_Registry::getInstance();
        	// if ($registry->isRegistered('acl')) $registry->offsetUnset('acl');
        	// 不用每次都初始化acl权限对照表
			$defaultNamespace = new Zend_Session_Namespace();
			Zend_Session::regenerateId();
			$acl = new Auth_Plugin_Acladapter($role_id);
			$defaultNamespace->acl = serialize($acl);
        	
        	$this->redirect($redirect);
        } else {
        	// 验证失败不跳转
        	$this->view->form = $form;
        	echo "<label class=\"label_auth\"></label><span class=\"div_auth_message\" style=\"color:red\">＊登录失败</span><br/>\n";
        	// $this->view->error = '登录失败';
        }
        $this->view->redirect = $redirect;
    }

    public function logoutAction()
    {
        $auth = Zend_Auth::getInstance();
        Zend_Session::expireSessionCookie();
        $auth->clearIdentity();
        $this->redirect('/auth/index/login');
    }
    
    private function _send_email($subject, $html, $toemail, $toemailname)
    {
    	$tr = new Zend_Mail_Transport_Smtp('smtp.163.com', array(
    			'auth'     => 'login',
    			'username' => 'guohua_zhuang@163.com',
    			'password' => 'GuoHua0513',
    			'port'     => 25,
    	));
    	Zend_Mail::setDefaultTransport($tr);

    	$mail = new Zend_Mail('UTF-8');
    	$mail->setFrom('guohua_zhuang@163.com', 'Bigu Adminstrator');
    	$mail->setSubject($subject);
    	$mail->setEncodingOfHeaders(Zend_Mime::ENCODING_BASE64);
    	$mail->setBodyHtml($html, 'UTF-8');
    	$mail->addTo($toemail, $toemailname);
    	
    	return $mail;
    }

    public function registerAction()
    {
    	// 如果用户已经登录就跳转到用户中心页面
    	$auth = Zend_Auth::getInstance();
    	if ($auth->hasIdentity()) {
    		$this->_forward('index');
    	}
    	
        // 读取表单
    	$form = new Auth_Form_Register();
    	if (!$form->isValid($_POST)) {
    		$this->view->form = $form;
    		return;
    	}
    	
    	$data = $form->getValues();
    	
    	// 查验后存库
        $user= new Auth_Model_DbTable_Users();
        if ($user->hasEmail($data['email'])) { /// email 是否已有
        	$form->outputMessage('Email已经注册过了');
        	$this->view->form = $form;
        	return;
        } else if ($user->hasUsername($data['username'])) { /// username 是否已有
        	$form->outputMessage('用户名已经被注册过了');
        	$this->view->form = $form;
        	return;
        }
    	
    	///如果查验合法发邮件
    	///如果上述都ＯＫ，存库
        if (empty($this->view->error)) {
        	$subject = '[比咕网] 感谢您的注册(Thank you for registering)';
        	$ev_url = 'http://'.$_SERVER['SERVER_NAME'].'/auth/index/emailverification?email='
        			. $data['email'] . '&str=' . sha1($data['email']);
        	$html = "<h1>Bigu注册邮件</h1>您好".$data['real_name']."，感谢您注册Bigu，请点击链接完成激活: <a href=\"$ev_url\">激活账号</a><br/>"
        			."如果无法直接跳转到链接，请手动复制以下链接到浏览器地址栏并访问以完成激活：$ev_url<br/>";
        	
        	$mail = $this->_send_email($subject, $html, $data['email'], $data['real_name']);
        	
        	try {
	        	if ($mail->send()) {
	        		$data['password_salt'] = Util_Global::generateSalt();//'xcNsdaAd73328aDs73oQw223hd';
	        		$data['id_role'] = 2;
	        		$data['password'] = sha1($data['password_salt'] . $data['password']);
	        		// INSERT TO DB
	        		$user->insert($data);
	        		$this->view->success = '注册成功！请登录邮箱激活帐号，谢谢！';
	        		$this->_setTableUser($user);
	        	} else {
	        		$form->outputMessage('向你提供的邮箱发送注册激活信息失败');
	        		$this->view->form = $form;
	        		return;
	        	}
        	} catch (Exception $e) {
        		$form->outputMessage('向你提供的邮箱发送注册激活信息失败，可能由于网络原因导致。<br/>'.$e->getMessage());
        		$this->view->form = $form;
        		return;
        	}
        }
    }

    public function denyaccessAction()
    {
        // action body
    }

    public function emailverificationAction()
    {
        $request = $this->getRequest();
	    $users = $this->_getTableUser();
	    if (empty($users) || !($users instanceof Auth_Model_DbTable_Users)) {
	    	$users = new Auth_Model_DbTable_Users();
	    }
	 
	    // GET THE VERIFICATION STRING FROM THE URI
	    $str = $request->getParam('str');
	    $email = $request->getParam('email');
	 
	    // CHECK IF THE USER CORRESPONDING TO THE STRING EXISTS
	    $user = $users->getSingleWithEmailHash($email, $str);
	    if ($user == null) {
	        $this->view->error = '非法的激活验证';
	    } else {
	        if ($user->status == 'approved') {
	            // USER ACCOUNT HAS ALREADY BEEN ACTIVATED
	            $this->view->error = '账号已经激活过了';
	        } else {
	            // THE USER EXISTS AND THE VERIFICATION STRING IS CORRECT
	            // LET'S APPROVE THE USER
	            if ($users->edit($email, array('status' => 'approved')) ) {
	                $this->view->success = '您好，'. $user->username . '! 你的账号已经成功激活啦！<br/>您现在可以登录喽！';
	            }
	        }
	    }
    }

    public function resetpasswordAction()
    {
        $request = $this->getRequest();
	    $users = $this->_getTableUser();
	    $new_pwd = $request->getParam('profile_new_pwd');
	    $new_pwd_again = $request->getParam('profile_new_pwd_again');
	    if (empty($users) || !($users instanceof Auth_Model_DbTable_Users)) {
	    	$users = new Auth_Model_DbTable_Users();
	    }
	 	
	    // GET THE VERIFICATION STRING FROM THE URI
	    $str = $request->getParam('str');
	    $email = $request->getParam('email');
	 	
	    // CHECK IF THE USER CORRESPONDING TO THE STRING EXISTS
	    $user = $users->getSingleWithEmailHash($email, $str);
	    if ($user == null) {
	        $this->view->error = '非法的密码重置验证';
	        return false;
	    }
	    $this->view->str = $str;
	    $this->view->email = $email;
	    
	    // 显示正在修改密码的操作
	    if (!empty($new_pwd) || !empty($new_pwd_again)) {
	    	$this->view->profile_new_pwd = $new_pwd;
	    	$this->view->profile_new_pwd_again = $new_pwd_again;
	    }
	    if (empty($new_pwd) || empty($new_pwd_again)) return true;
	    if ($new_pwd != $new_pwd_again) {
	    	Auth_Form_Register::GlobalOutputMessage('两次填写新密码不一致');
	    	return false;
	    }
	    $pw_valid_ret = Auth_Form_Register::IsValidPassword($new_pwd);
	    if ('OK' != $pw_valid_ret) {
	    	Auth_Form_Register::GlobalOutputMessage($pw_valid_ret);
	    	return false;
	    }
	    
	    $data = array();
	    $data['password_salt'] = Util_Global::generateSalt();
	    $data['password'] = sha1($data['password_salt'] . $new_pwd);
	    if ($users->edit($email, $data) ) {
	    	$this->view->success = '您好，'. $user->username . '! 你的账号密码已经修改成功！<br/>'
	    		. "<br/><a href=\"/auth/index/login\">点击登录</a><br/>";
	    }
    }
    
    public function forgetpasswordAction()
    {
    	$request = $this->getRequest();
    	$profile_email = $request->getParam('profile_email');
    	// 显示填写帐号邮箱view
    	if (empty($profile_email)) return false;
    	
    	// 如果查实数据库中有这个邮箱，则发送邮件，邮件中的链接连至resetpasswordAction处理
    	$users = $this->_getTableUser();
    	if (empty($users) || !($users instanceof Auth_Model_DbTable_Users)) {
    		$users = new Auth_Model_DbTable_Users();
    	}
    	$data = $users->getSingleWithEmail($profile_email);
    	if (empty($data)) {
    		$this->view->error = '该邮箱未注册';
    		return false;
    	}
    	
    	$subject = '[比咕网] 重置密码(Reset Password)';
    	$ev_url = 'http://'.$_SERVER['SERVER_NAME'].'/auth/index/resetpassword?email='
    		. $data['email'] . '&str=' . sha1($data['email']);
    	$html = "<h1>Bigu重置密码</h1>您好".$data['real_name']
    		. "，请点击链接设置新密码: <a href=\"$ev_url\">设置新密码</a><br/>"
    		. "如果无法直接跳转到链接，请手动复制以下链接到浏览器地址栏并访问：$ev_url<br/>"
    		. "如果您并未请求重置密码，请忽略本邮件。<br/>";
    	
    	$mail = $this->_send_email($subject, $html, $data['email'], $data['real_name']);
    	
    	try {
	    	if ($mail->send()) {
		    	$this->view->success = '重置密码邮件已发送至注册邮箱，请收取邮件进一步重置密码';
		    	return true;
		    } else {
		    	$this->view->error = '向你提供的邮箱发送注册激活信息失败';
		    	return false;
	    	}
    	} catch (Exception $e) {
	    	$this->view->error = '向你提供的邮箱发送注册激活信息失败，可能由于网络原因导致';
	    	return false;
    	}
    }

}
