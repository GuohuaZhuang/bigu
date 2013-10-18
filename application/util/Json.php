<?php

class Util_Json
{
	public static function jsonReadable($json, $html=FALSE)
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