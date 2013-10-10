<?php

class Auth_Form_Register extends Zend_Form
{
	private $elementDecorators = array(
			'ViewHelper',
			array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'element')),
			'Label',
			array(array('row' => 'HtmlTag'), array('tag' => 'li')),
	);

	private $buttonDecorators = array(
			'ViewHelper',
			array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'button')),
			array(array('row' => 'HtmlTag'), array('tag' => 'li')),
	);
	
    public function init()
    {
    	$this->setMethod('POST');
    	
    	$real_name = new Zend_Form_Element_Text('real_name', 
    			array(	'decorators' => $this->elementDecorators,
    					'label' => '名字',
    					'required' => true,
    					'filters' => array('StringTrim'),
    					'validators' => array(array('StringLength', false, array(2, 50))),
    					'class' => 'input-text'
    					));
    	$email = new Zend_Form_Element_Text('email', 
    			array(	'decorators' => $this->elementDecorators,
    					'label' => 'Email邮箱',
    					'required' => true,
    					'filters' => array('StringTrim'),
    					'validators' => array('EmailAddress'),
    					'class' => 'input-text'
    					));
    	$emailAgain = new Zend_Form_Element_Text('emailAgain', 
    			array(	'decorators' => $this->elementDecorators,
    					'label' => '再次确认邮箱',
    					'required' => true,
    					'filters' => array('StringTrim'),
    					'validators' => array('EmailAddress'),
    					'class' => 'input-text'
    					));
    	$username = new Zend_Form_Element_Text('username', 
    			array(	'decorators' => $this->elementDecorators,
    					'label' => '用户名',
    					'required' => true,
    					'filters' => array('StringTrim'),
    					'validators' => array(array('StringLength', false, array(3, 50))),
    					'class' => 'input-text'
    					));
    	$password = new Zend_Form_Element_Password('password', 
    			array(	'decorators' => $this->elementDecorators,
    					'label' => '密码',
    					'required' => true,
    					'filters' => array('StringTrim'),
    					'validators' => array(array('StringLength', false, array(6, 50))),
    					'class' => 'input-password'
    					));
    	$passwordAgain = new Zend_Form_Element_Password('passwordAgain', 
    			array(	'decorators' => $this->elementDecorators,
    					'label' => '密码',
    					'required' => true,
    					'filters' => array('StringTrim'),
    					'validators' => array(array('StringLength', false, array(6, 50))),
    					'class' => 'input-password'
    					));
    	$submit = new Zend_Form_Element_Submit('register', 
    			array(	'decorators' => $this->buttonDecorators,
    					'label' => '注册',
    					'class' => 'input-submit'
    					));
    	$this->addElements(array(
    			$real_name,
    			$email,
    			$emailAgain,
    			$username,
    			$password,
    			$passwordAgain,
    			$submit
    			));
    }

}
