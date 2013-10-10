<?php
class Auth_Plugin_Valid extends Zend_Controller_Plugin_Abstract
{
	private $_module;
	private $_controller;
	private $_action;
	private $_role;
	
	/**
	 * Called before an action is dispatched by Zend_Controller_Dispatcher.
	 *
	 * @param  Zend_Controller_Request_Abstract $request
	 * @return void
	 */
	public function preDispatch(Zend_Controller_Request_Abstract $request)/*OK*/
	{
		// 更新module/controller/action/role
		$this->_module = $request->getModuleName();
		$this->_controller = $request->getControllerName();
		$this->_action = $request->getActionName();
		
		// 如果是auth/index/login的请求则不做验证
		if ($this->_module == 'auth' 
			&& $this->_controller == 'index' 
			&& ($this->_action == 'login' || $this->_action == 'register')) {
			return;
		}
		
		// JUST FOR DEBUG
// 		echo '[INFO] -- _module = ' . $this->_module . 
// 			  ', _controller = ' . $this->_controller . 
// 			  ', _action = ' . $this->_action . '<br/>';
		
		$redirect = true; // 验证无法通过的都需要重定向到登录页面
		
		// 判断Authentication身份验证并拿到_role
		$auth = Zend_Auth::getInstance();
		
		if ($this->_isAuth($auth)) {
			
			$user = $auth->getStorage()->read();
			$this->_role = $user['id_role'];
			
			// JUST FOR DEBUG
			// echo '认证通过(_role='.$this->_role.')<br/>';
			
			// 执行Authorization权限判断
			require_once(APPLICATION_PATH . '/modules/auth/plugins/Acladapter.php');
			
			// 不用每次都初始化acl权限对照表
			$registry = Zend_Registry::getInstance();
			if (!$registry->isRegistered('acl')) {
				$acl = new Auth_Plugin_Acladapter();
				$registry->set('acl', $acl);
			} else {
				$acl = $registry->get('acl');
			}
			
			if ($this->_isAllowed($auth, $acl)) {
				$redirect = false;
				// JUST FOR DEBUG
				// echo '权限通过<br/>';
			} else {
				// JUST FOR DEBUG
				// echo '权限没通过<br/>';
				$request->setModuleName('auth');
				$request->setControllerName('index');
				$request->setActionName('denyaccess');
				return;
			}
		} else {
			// JUST FOR DEBUG
			// echo '认证没通过<br/>';
		}
		
		// 认证没通过或者权限没通过的都跳转到auth/index/login
		if ($redirect) {
// 			$request->setModuleName('auth');
// 			$request->setControllerName('index');
// 			$request->setActionName('login');
		}
	}
	
	private function _isAuth(Zend_Auth $auth)/*OK*/
	{
		if (!empty($auth) && ($auth instanceof Zend_Auth)) {
			return $auth->hasIdentity();
		}
		// 默认是没有验证
		return false;
	}
	
	private function _isAllowed(Zend_Auth $auth, Zend_Acl $acl)/*OK*/
	{
		if (empty($auth) 
				|| empty($acl)
				|| !($auth instanceof Zend_Auth)
				|| !($acl instanceof Zend_Acl)) {
			return false;
		}
		
		$resources = array(
				'*/*/*',
				$this->_module . '/*/*',
				$this->_module . '/' . $this->_controller . '/*',
				$this->_module . '/' . $this->_controller . '/' . $this->_action
			);
		// 最后里层权限设置为最终设置
		$result = false;
		foreach ($resources as $one_res) {
			if ($acl->has($one_res)) {
				$result = $acl->isAllowed($this->_role, $one_res);
			}
		}
		
		return $result;
	}
}