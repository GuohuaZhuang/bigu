<?php

class Auth_Form_Login extends Zend_Form
{
    public function init()
    {
    	$this->addElement('hidden', 'redirect');
        $this->addElement('text', 'username',
        	array('label' => '用户名',
        		'required' => true,
        		'validators' => array('Alnum')
        		));
        $this->addElement('password', 'password',
        		array('label' => '密码',
        				'required' => true,
        				'validators' => array('Alnum')
        		));
        $this->addElement('submit', 'submit',
        		array('label' => '登录'
        		));
    }
    
}
