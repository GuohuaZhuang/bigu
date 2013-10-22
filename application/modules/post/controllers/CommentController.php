<?php

require_once(APPLICATION_PATH . '/modules/post/models/DbTable/Comment.php');

class Post_CommentController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }
    
    public function addAction()
    {
    	$request = $this->getRequest();
    	$id_post = $request->getParam('comment_id_post');
    	$content = $request->getParam('comment_content');
    	$author = $request->getParam('comment_author');
    	if (empty($id_post) || empty($content)) {
    		echo '{"err": "添加失败，参数不足"}';
    	} else {
    		// pub_datetime
    		$pub_datetime = date('Y-m-d H:i:s');
    		// Insert to DB
    		$db = new Post_Model_DbTable_Comment();
    		$data = array('id_post' => $id_post, 'content' => trim($content), 
    			'pub_datetime' => $pub_datetime, 'author' => $author);
    		$result = $db->insert($data);
    		echo '{"success": "添加成功", "id": "'.$result.'"}';
    	}
    	// stop layout and render
    	$this->_helper->layout->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(TRUE);
    }
    
    public function deleteAction()
    {
    	$request = $this->getRequest();
    	$id = $request->getParam('id');
    	if (empty($id)) {
    		echo '{"err": "删除失败，参数不足"}';
    	} else {
	    	$db = new Post_Model_DbTable_Comment();
	    	$where = $db->getAdapter()->quoteInto('id=?', $id);
	    	$num = $db->delete($where);
	    	echo '{"success": "删除成功", "num": "'.$num.'"}';
    	}
    	// stop layout and render
    	$this->_helper->layout->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(TRUE);
	}
    
    public function listAction()
    {
    	$request = $this->getRequest();
    	$id_post = $request->getParam('comment_id_post');
    	if (empty($id_post)) {
    		echo '{"err": "加载失败，参数不足"}';
    	} else {
	    	$db = new Post_Model_DbTable_Comment();
	    	$result = $db->fetchAllByPostid($id_post);
	    	if (empty($result)) {
	    		echo '{"success": "加载完成", "num": "'.'0'.'"}';
	    	} else {
	    		$rstr = '{"success": "加载完成", "num": "'.count($result).'", "d": [';
	    		$i = 0;
	    		foreach ($result as $item) {
	    			if (0 != $i) $rstr .= ', ';
	    			// {"id": "XXX", "content": "XXX", "pub_datetime": "XXX"}
	    			$rstr .= '{"id": "';
	    			$rstr .= $item['id'];
	    			$rstr .= '", "content": "';
	    			$rstr .= $this->_jsonReadable(trim($item['content']), TRUE);
	    			$rstr .= '", "author": "';
	    			$rstr .= $item['author'];
	    			$rstr .= '", "avatar": "';
	    			$rstr .= $item['avatar'];
	    			$rstr .= '", "pub_datetime": "';
	    			$rstr .= $item['pub_datetime'];
	    			$rstr .= '"}';
	    			$i ++;
	    		}
	    		$rstr .= ']}';
	    		echo $rstr;
	    	}
    	}
    	// stop layout and render
    	$this->_helper->layout->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(TRUE);
    }
    
    private function _jsonReadable($json, $html=FALSE)
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
