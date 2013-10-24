<?php

class Auth_Form_Login extends Zend_Form
{
	public function isValid($args)
	{
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

			return true;
		}
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
