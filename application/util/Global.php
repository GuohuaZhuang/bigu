<?php
class Util_Global
{
	public static function getUsername($ismandatory = false)
	{
		$auth = Zend_Auth::getInstance();
		$user = $auth->getStorage()->read();
		if (isset($user['username'])) {
			return $user['username'];
		} else if ($ismandatory) {
			return false;
		} else {
			return '';
		}
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
	
	public static function JsonReadable($json, $html=FALSE)
	{
		$tabcount = 0;
		$result = '';
		$inquote = false;
		$ignorenext = false;
		$tab = '';
		$newline = '';
		if ($html) {
			$tab = "&nbsp;&nbsp;&nbsp;&nbsp;";
			$newline = "<br/>";
		} else {
			$tab = "\t";
			$newline = "\n";
		}
		$len = strlen($json);
		for($i = 0; $i < $len; $i++) {
			$char = $json[$i];
	
			if ($ignorenext) {
				$result .= $char;
				$ignorenext = false;
			} else {
				switch($char) {
					case '{':
						$tabcount++;
						$result .= $char . $newline . str_repeat($tab, $tabcount);
						break;
					case '}':
						$tabcount--;
						$result = trim($result) . $newline . str_repeat($tab, $tabcount) . $char;
						break;
					case ',':
						$result .= $char . $newline . str_repeat($tab, $tabcount);
						break;
					case '"':
						$inquote = !$inquote;
						$result .= $char;
						break;
					case "\n":
					case "\r":
						$result .= $newline;
						break;
					case "\\":
						if ($inquote) $ignorenext = true;
						$result .= $char;
						break;
					default:
						$result .= $char;
				}
			}
		}
	
		return $result;
	}
}
?>