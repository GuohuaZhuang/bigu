<?php

class Auth_Form_Login extends Zend_Form
{
	public $username;
	public $password;
	public $redirect;
	public $isremember;
	public function isValid($args)
	{
		if (!isset($args['username'])) return false; // 还没提交
		
		if (isset($args['username']) && isset($args['password'])
			&& !empty($args['username']) && !empty($args['password']))
		{
// 			// do validation in register
// 			$username = $args['username'];
// 			$password = $args['password'];
// 			if (preg_match("/^[a-z0-9_]*$/i", $username))
// 			{
// 				if(preg_match('/^(?=.*\d)(?=.*[A-Za-z])[0-9A-Za-z!@#$%]{8,12}$/', $password)) {
// 					// username and password is all valid ok.
// 				}
// 			}
			$this->username = $args['username'];
			$this->password = $args['password'];
			return true;
		}
		$this->redirect = isset($args['redirect']) ? $args['redirect']: '';
		$this->isremember = isset($args['isremember']) ? $args['isremember']: '';
		
		echo "<label class=\"label_auth\"></label><span class=\"div_auth_message\" style=\"color:red\">＊用户名和密码不能为空</span><br/>\n";
		
		return false;
	}
	
// 	// just implement invalid is ok
//     public function init()
//     {
//     	$this->addElement('hidden', 'redirect');
//         $this->addElement('text', 'username',
//         	array('label' => '用户名',
//         		'required' => true,
//         		'validators' => array('Alnum')
//         		));
//         $this->addElement('password', 'password',
//         		array('label' => '密码',
//         				'required' => true,
//         				'validators' => array('Alnum')
//         		));
// //         $isremeber = new Zend_Form_Element_Checkbox('isremember',
// //         		array('label' => '下次自动登录',
// //         				'required' => true, 
// //         				'checkedValue' => '1',
// //         				'uncheckedValue' => '0'
// //         		));
// //         $isremeber->setChecked(true);
// //         $this->addElement($isremeber);
//         $this->addElement('submit', 'submit',
//         		array('label' => '登录'
//         		));
//     }
    
}
