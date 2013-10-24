<?php

require_once(APPLICATION_PATH . '/modules/auth/models/DbTable/Users.php');
require_once(APPLICATION_PATH . '/modules/auth/forms/Login.php');
require_once(APPLICATION_PATH . '/modules/auth/plugins/Authadapter.php');
require_once(APPLICATION_PATH . '/modules/auth/forms/Register.php');
require_once(APPLICATION_PATH . '/util/Global.php');

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
    		$this->_forward('index');
    		return;
    	}
    	
    	// 确定一下登录完成后应该跳转的页面
    	$request = $this->getRequest();
    	$redirect = $request->getPost('redirect');
    	if (strlen($redirect) == 0)
    		$redirect = $request->getServer('REQUEST_URI');
    	if (strlen($redirect) == 0)
    		$redirect = '/auth/index/index';
    	
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
        	$data= array(
        		'username' => $username,
        		'id_role'  => $role_id
        	);
        	$isremember = $request->getPost('isremember');
        	if (empty($isremember)) {
        		Zend_Session::forgetMe(); // 下次不自动登录
        	} else {
        		Zend_Session::rememberMe(); // 下次自动登录(默认rememberMeSeconds = 1209600; // 2 weeks)
        	}
        	$auth->getStorage()->write($data);
        	
        	// 清除register
        	$registry = Zend_Registry::getInstance();
        	if ($registry->isRegistered('acl')) $registry->offsetUnset('acl');
        	
        	$this->redirect($redirect);
        } else {
        	// 验证失败不跳转
        	$this->view->form = $form;
        	$this->view->error = '登录失败';
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
        	$this->view->error = 'Email已经注册过了';
        } else if ($user->hasUsername($data['username'])) { /// username 是否已有
        	$this->view->error = '用户名已经注册过了';
        } else if (!$data['email'] == $data['emailAgain']) { /// email == emailAgain？
        	$this->view->error = '两次填写邮箱不一致';
        } else if (!$data['password'] == $data['passwordAgain']) { /// password == passwordAgain？
        	$this->view->error = '两次填写密码不一致';
        }
    	
    	///如果查验合法发邮件
    	///如果上述都ＯＫ，存库
        if (empty($this->view->error)) {
        	$tr = new Zend_Mail_Transport_Smtp('smtp.163.com', array(
        			'auth'     => 'login',
        			'username' => 'guohua_zhuang@163.com',
        			'password' => 'GuoHua0513',
        			'port'     => 25,
        	));
        	Zend_Mail::setDefaultTransport($tr);
        	
        	$mail = new Zend_Mail('UTF-8');
        	$mail->setFrom('guohua_zhuang@163.com', 'Bigu Adminstrator');
        	$mail->setSubject('Thank you for registering');
        	$mail->setEncodingOfHeaders(Zend_Mime::ENCODING_BASE64);
        	$ev_url = 'http://bigu1.local/auth/index/emailverification?email='
        		 . $data['email'] . '&str=' . sha1($data['email']);
        	$html = "<h1>Bigu注册邮件</h1>感谢您注册Bigu，请点击链接完成激活: <a href=\"$ev_url\">激活账号</a><br/>". 
        		"如果无法直接跳转到链接，请手动复制以下链接到浏览器地址栏并访问以完成激活：$ev_url<br/>";
        	$mail->setBodyHtml($html, 'UTF-8');
        	$mail->addTo($data['email'], $data['real_name']);
        	if ($mail->send()) {
        		$data['password_salt'] = Util_Global::generateSalt();//'xcNsdaAd73328aDs73oQw223hd';
        		$data['id_role'] = 2;
        		$data['password'] = sha1($data['password_salt'] . $data['password']);
        		// JUST FOR DEBUG 
        		$user->insert($data);
        		$this->view->success = '注册成功';
        		$this->_setTableUser($user);
        	} else {
        		$this->view->error = '向你提供的邮箱发送注册激活信息失败';
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
	                $this->view->success = '您好，'. $user->username . '! 你的账号已经成功激活啦！';
	            }
	        }
	    }
    }

    
}

