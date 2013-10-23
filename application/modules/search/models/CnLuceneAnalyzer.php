<?php

require_once 'Zend/Search/Lucene/Analysis/Analyzer.php';
require_once 'Zend/Search/Lucene/Analysis/Analyzer/Common.php';
require_once 'Zend/Search/Lucene/Analysis/TokenFilter/LowerCaseUtf8.php';

class CN_Lucene_Analyzer extends Zend_Search_Lucene_Analysis_Analyzer_Common
{
	private $_position;
	private $_cnStopWords = array( );

	public function __construct()
	{
		$this->addFilter(new Zend_Search_Lucene_Analysis_TokenFilter_LowerCaseUtf8());
	}
	
	public function setCnStopWords( $cnStopWords )
	{
		$this->_cnStopWords = $cnStopWords;
	}

	/**
	 * Reset token stream
	 */
	public function reset()
	{
		$this->_position = 0;
		$search = array(",", "/", "\\", ".", ";", ":", "\"", "!", "~", "`", "^", "(", ")", "?", "-", "'", "<", ">", "$", "&", "%", "#", "@", "+", "=", "{", "}", "[","]", "：", "）", "（", "．", "。", "，", "！", "；", "“", "”", "‘", "’", "［", "］", "、", "—", "　", "《", "》", "－", "…", "【", "】", "？", "￥" );

		$this->_input = str_replace( $search, '', $this->_input );
		$this->_input = str_replace( $this->_cnStopWords, ' ', $this->_input );
	}

	/**
	 * Tokenization stream API
	 * Get next token
	 * Returns null at the end of stream
	 *
	 * @return Zend_Search_Lucene_Analysis_Token|null
	 */
	public function nextToken()
	{
		if ($this->_input === null)
		{
			return null;
		}
		$len = strlen($this->_input);
		// echo "Old string：".$this->_input."<br />";
		while ($this->_position < $len)
		{
			// Delete space at the begining
			while ($this->_position < $len &&$this->_input[$this->_position]==' ' )
			{
				$this->_position++;
			}
			$termStartPosition = $this->_position;
			
			// For single-word segmentation was I forced add
			if ($this->_position >= $len) break;
			
			$temp_char = $this->_input[$this->_position];
			$isCnWord = false;
			if(ord($temp_char)>127)
			{
				$i = 0;
				while( $this->_position < $len && ord( $this->_input[$this->_position] )>127 )
				{
					$this->_position = $this->_position + 3;
					$i ++;
					// For single-word segmentation was I forced modify 
					// if($i==2)
					if($i==1)
					{
						$isCnWord = true;
						break;
					}
				}
// For single-word segmentation was I forced commented
// 				if($i==1) continue;
			}
			else
			{
				while ($this->_position < $len && ctype_alnum( $this->_input[$this->_position] ))
				{
					$this->_position++;
				}
				// echo $this->_position.":".$this->_input[$this->_position-1]."\n";
			}
// For single-word segmentation was I forced commented, and then not commented.
			if ($this->_position == $termStartPosition)
			{
				$this->_position++;
				continue;
			}

			$tmp_str = substr($this->_input, $termStartPosition, $this->_position - $termStartPosition);

			// print_r(array($tmp_str, $termStartPosition,$this->_position));
			$token = new Zend_Search_Lucene_Analysis_Token( $tmp_str, $termStartPosition,$this->_position );

			$token = $this->normalize($token);

			if($isCnWord)
			{
// For single-word segmentation was I forced commented
// 				$this->_position = $this->_position - 3;
			}

			if ($token !== null)
			{
				return $token;
			}
		}

		return null;
	}
}
