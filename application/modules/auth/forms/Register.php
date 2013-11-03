<?php

class Auth_Form_Register extends Zend_Form
{
	public $real_name;
	public $email;
	public $emailAgain;
	public $username;
	public $password;
	public $passwordAgain;
	public function getValues() {
		return array(
				'real_name' => $this->real_name,
				'email' => $this->email,
				'emailAgain' => $this->emailAgain,
				'username' => $this->username,
				'password' => $this->password,
				'passwordAgain' => $this->passwordAgain
			);
	}
	public static function GlobalOutputMessage($s) {
		echo "<label class=\"label_auth\"></label><span class=\"div_auth_message\" style=\"color:red\">＊".$s."</span><br/>\n";
	}
	public function outputMessage($s) {
		echo "<label class=\"label_auth\"></label><span class=\"div_auth_message\" style=\"color:red\">＊".$s."</span><br/>\n";
	}
	/*
	 * reference links: 
	 *   http://www.phpjabbers.com/php-validation-and-verification-php27.html
	 *   http://stackoverflow.com/questions/11873990/create-preg-match-for-password-validation-allowing
	 *   http://outofmemory.cn/code-snippet/1008/php-changyong-regular-expression-yanzheng-yonghuming-password-Email-Url-kind
	 * 首先判断各个是否为空
	 * 再判断email/password的二次输入是否相同
	 * 再单个特殊验证
	 * 最后转出来服务器验证
	 */
	public function isValid($args)
	{
		if (!isset($args['real_name'])) return false; // 还没提交
		
		$this->real_name = isset($args['real_name']) ? $args['real_name']: '';
		$this->email = isset($args['email']) ? $args['email']: '';
		$this->emailAgain = isset($args['emailAgain']) ? $args['emailAgain']: '';
		$this->username = isset($args['username']) ? $args['username']: '';
		$this->password = isset($args['password']) ? $args['password']: '';
		$this->passwordAgain = isset($args['passwordAgain']) ? $args['passwordAgain']: '';
		
		// 首先判断各个是否为空
		if (empty($this->real_name)) {
			$this->outputMessage('名字不能为空'); return false;
		}
		if (empty($this->email)) {
			$this->outputMessage('邮箱不能为空'); return false;
		}
		if (empty($this->username)) {
			$this->outputMessage('用户名不能为空'); return false;
		}
		if (empty($this->password)) {
			$this->outputMessage('密码不能为空'); return false;
		}
		
		// 再判断email/password的二次输入是否相同
		if (!($this->email == $this->emailAgain)) {
			$this->outputMessage('两次填写邮箱不一致'); return false;
		}
		if (!($this->password == $this->passwordAgain)) {
			$this->outputMessage('两次填写密码不一致'); return false;
		}
		
		// 再单个特殊验证
		
		// Email mask
		// if(preg_match("/^[a-zA-Z]w+(.w+)*@[0-9a-zA-Z_-]+(.[0-9a-zA-Z]+)*.[a-zA-Z]{2,4}$/", $this->email) === 0)
		// if(preg_match("/^[a-zA-Z]w+(.w+)*@w+(.[0-9a-zA-Z]+)*.[a-zA-Z]{2,4}$/", $_POST["email"]) === 0)
		// if(preg_match("/^[a-zA-Z0-9][a-zA-Z0-9_.]+@[a-zA-Z0-9_-]+(.[0-9a-zA-Z]+)*.[a-zA-Z]{2,4}$/", $this->email) === 0)
		if (preg_match('/^[\w\d]+[\w\d-.]*@[\w\d-.]+\.[\w\d]{2,10}$/i', $this->email) === 0)
		{
			$this->outputMessage('邮箱格式不正确'); return false;
		}
		
		// User must be digits and letters
		if (preg_match("/^[0-9a-zA-Z_]{5,}$/", $this->username) === 0)
		{
			$this->outputMessage('用户名必须由长度至少为5个的数字、英文字母、下划线组成'); return false;
		}
		
		// Password must be strong
		// if(preg_match("/^.*(?=.{8,})(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z]).*$/", $this->password) === 0)
		if (preg_match('/^[\\~!@#$%^&*()-_=+|{}\[\],.?\/:`; \'\"\d\w]{3,}$/', $this->password) === 0)
		{
			// $this->outputMessage('密码必须由长度至少为8个数字、英文字母组成，且必须包含至少一个小写字母、一个大写字母和一个数字'); return false;
			$this->outputMessage('密码必须由长度至少为3个由数字、英文字母或字符组成'); return false;
		}
		
		return true;
	}
	
	public static function IsValidPassword($password)
	{
		if (preg_match('/^[\\~!@#$%^&*()-_=+|{}\[\],.?\/:`; \'\"\d\w]{3,}$/', $password) === 0)
		{
			return '密码必须由长度至少为3个由数字、英文字母或字符组成';
		}
		return 'OK';
	}
	
// 	private $elementDecorators = array(
// 			'ViewHelper',
// 			array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'element')),
// 			'Label',
// 			array(array('row' => 'HtmlTag'), array('tag' => 'li')),
// 	);

// 	private $buttonDecorators = array(
// 			'ViewHelper',
// 			array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'button')),
// 			array(array('row' => 'HtmlTag'), array('tag' => 'li')),
// 	);
	
//     public function init()
//     {
//     	$this->setMethod('POST');
    	
//     	$real_name = new Zend_Form_Element_Text('real_name', 
//     			array(	'decorators' => $this->elementDecorators,
//     					'label' => '名字',
//     					'required' => true,
//     					'filters' => array('StringTrim'),
//     					'validators' => array(array('StringLength', false, array(2, 50))),
//     					'class' => 'input-text'
//     					));
//     	$email = new Zend_Form_Element_Text('email', 
//     			array(	'decorators' => $this->elementDecorators,
//     					'label' => 'Email邮箱',
//     					'required' => true,
//     					'filters' => array('StringTrim'),
//     					'validators' => array('EmailAddress'),
//     					'class' => 'input-text'
//     					));
//     	$emailAgain = new Zend_Form_Element_Text('emailAgain', 
//     			array(	'decorators' => $this->elementDecorators,
//     					'label' => '再次确认邮箱',
//     					'required' => true,
//     					'filters' => array('StringTrim'),
//     					'validators' => array('EmailAddress'),
//     					'class' => 'input-text'
//     					));
//     	$username = new Zend_Form_Element_Text('username', 
//     			array(	'decorators' => $this->elementDecorators,
//     					'label' => '用户名',
//     					'required' => true,
//     					'filters' => array('StringTrim'),
//     					'validators' => array(array('StringLength', false, array(3, 50))),
//     					'class' => 'input-text'
//     					));
//     	$password = new Zend_Form_Element_Password('password', 
//     			array(	'decorators' => $this->elementDecorators,
//     					'label' => '密码',
//     					'required' => true,
//     					'filters' => array('StringTrim'),
//     					'validators' => array(array('StringLength', false, array(6, 50))),
//     					'class' => 'input-password'
//     					));
//     	$passwordAgain = new Zend_Form_Element_Password('passwordAgain', 
//     			array(	'decorators' => $this->elementDecorators,
//     					'label' => '密码',
//     					'required' => true,
//     					'filters' => array('StringTrim'),
//     					'validators' => array(array('StringLength', false, array(6, 50))),
//     					'class' => 'input-password'
//     					));
//     	$submit = new Zend_Form_Element_Submit('register', 
//     			array(	'decorators' => $this->buttonDecorators,
//     					'label' => '注册',
//     					'class' => 'input-submit'
//     					));
//     	$this->addElements(array(
//     			$real_name,
//     			$email,
//     			$emailAgain,
//     			$username,
//     			$password,
//     			$passwordAgain,
//     			$submit
//     			));
//     }

}
