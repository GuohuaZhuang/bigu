<?php
class Util_Global
{
	public static function getUsername()
	{
		$auth = Zend_Auth::getInstance();
		$user = $auth->getStorage()->read();
		return $user['username'];
	}
	
	public static function generateSalt($length = 32, $chars = '1234567890abcdef') {
		// LENGTH OF CHARACTER LIST
		$charsLength = (strlen($chars) - 1);
		 
		// START OUR STRING
		$string = $chars{rand(0, $charsLength)};
		 
		// GENERATE RANDOM STRING
		for ($i = 1; $i < $length; $i = strlen($string)) {
			// GRAB A RANDOM CHARACTER FROM OUR LIST
			$r = $chars{rand(0, $charsLength)};
			// MAKE SURE THE SAME TWO CHARACTERS DON'T APPEAR NEXT TO EACH OTHER
			if ($r != $string{$i - 1}) {
				$string .=  $r;
			} else {
				$i--;
			}
		}
		 
		// RETURN THE STRING
		return $string;
	}
}
?>