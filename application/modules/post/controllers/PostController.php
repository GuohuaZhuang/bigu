<?php

require_once(APPLICATION_PATH . '/modules/post/models/DbTable/Post.php');
require_once(APPLICATION_PATH . '/modules/post/models/DbTable/Comment.php');
require_once(APPLICATION_PATH . '/modules/post/models/DbTable/Category.php');
require_once(APPLICATION_PATH . '/modules/auth/models/DbTable/Users.php');
require_once(APPLICATION_PATH . '/modules/search/models/SearchAdapter.php');
require_once(APPLICATION_PATH . '/util/Global.php');

class Post_PostController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    private function _transform_content($html)
    {
    	$value = '';
    	if ( get_magic_quotes_gpc() )
    		$value = stripslashes((string)$html);
    	else
    		$value = (string)$html;
    	return $value;
    }
    
    private function _extract_abstract($html)
    {
    	$value = strip_tags($html);
    	return mb_substr($value, 0, 200, 'UTF-8');
    }
    
    private function _remove_imgandthumb($id)
    {
    	$dbpost = new Post_Model_DbTable_Post();
    	$html = $dbpost->get_one_value($id, 'content');
    	$imagepaths = $this->_find_all_imagepath($html);
    	// 去除表情符号，可以是上传的，也可以是网络上图片
    	foreach ($imagepaths as $imagepath) {
	    	@unlink($_SERVER['DOCUMENT_ROOT'].$imagepath);
    	}
    }
    
    private function _find_all_imagepath($html)
    {
    	$imagepaths = array();
    	$rule = '#(<img[^>]*?>)#';
    	if (preg_match_all($rule, $html, $match)) {
    		$match = $match[0];
    		foreach ($match as $m) {
    			if (false != strstr($m, 'height:20px; width:20px')) continue;
    			$rule_src = "#src=\"([^\"]*)\"#";
    			if (preg_match($rule_src, $m, $match_src)) {
    				if ('/upload/img/' == substr($match_src[1], 0, 12)) {
    					$imagepaths[] = $match_src[1];
    				}
    			}
    		}
    	}
    	return $imagepaths;
    }
    
    private function _find_thumbpath($html)
    {
//     	$rule = "#src=\"/upload/img/(20[0-9]{6}/[0-9a-fA-F]{32}\.(png|jpg|jpeg|gif))#x";
//     	if (preg_match($rule, $html, $match)) {
//     		return '/upload/thumb/' . $match[1];
//     	}
//     	$rule = "#(<img alt=\"[^(smiley)].*?\" style)#";
//     	if (preg_match($rule, $html, $match)) {
//     		$html = $match[0];
//     		$rule_src = "#src=\"([^\"]*)\"#";
//     		if (preg_match($rule_src, $html, $match)) {
//     			return $match[1];
//     		}
//     	}
		// 去除表情符号，可以是上传的，也可以是网络上图片
    	$rule = '#(<img[^>]*?>)#';
    	if (preg_match_all($rule, $html, $match)) {
    		$match = $match[0];
    		foreach ($match as $m) {
    			if (false != strstr($m, 'height:20px; width:20px')) continue;
    			$rule_src = "#src=\"([^\"]*)\"#";
    			if (preg_match($rule_src, $m, $match_src)) {
    				if ('/upload/img/' == substr($match_src[1], 0, 12)) {
    					return $this->_generatethumb($_SERVER['DOCUMENT_ROOT'].$match_src[1], substr($match_src[1], 12));
    				} else {
    					return $this->_generatethumb($match_src[1]);
    					// return $match_src[1];
    				}
    			}
    		}
    	}
    	return null;
    }
    
    function _generatethumb($filetmp, $filename = null)
    {
    	// thumb_dir
    	$thumb_dir = '/upload/thumb/' . date("Ymd");
    	// filetype
    	$_tmp_arr_v5 = explode('.', $filetmp);
    	$filetype = strtolower(end($_tmp_arr_v5));
    	if (false!=strstr($filetype,'latex')) $filetype = 'gif';
    	// filename
    	if (null == $filename) {
    		$filename = md5(microtime().rand(0, 100)) . '.' . $filetype;
    	} else {
    		// 调整日期文件夹名称
    		$thumb_dir = '/upload/thumb/'.substr($filename, 0, 8);
    		$filename = substr($filename, 8);
    	}
    	
    	$thumb_path = $_SERVER['DOCUMENT_ROOT'].$thumb_dir;
    	if (!file_exists($thumb_path))
    	{
    		mkdir($thumb_path, 0777);
    	}
    	// Save thumb image file to thumb_path
    	if (!is_writeable($thumb_path)) {
    		return false;
    	} else {
    		// 缩略图大小为210px*140px
    		$tw = 210;
    		$th = 140;
    		// Load
    		if ($filetype == 'jpg') $filetype = 'jpeg';
    		$thumb = imagecreatetruecolor($tw, $th);
    		$white = imagecolorallocate($thumb, 255, 255, 255);
    		imagefill($thumb, 0, 0, $white);
    		$f_create = 'imagecreatefrom' . $filetype;
    		$source = $f_create($filetmp);
    		// Get source image width and height
    		$w = imagesx($source);
    		$h = imagesy($source);
    		// 计算imagecopyresampled压缩参数值
    		$wrate = $w / $tw;
    		$hrate = $h / $th;
    		if ($wrate > $hrate) {
    			$minh = $h;
    			$minw = $tw * $hrate;
    		} else {
    			$minh = $th * $wrate;
    			$minw = $w;
    		}
    		$sx = ($w - $minw) / 2;
    		$sy = ($h - $minh) / 2;
    
    		// Resize
    		imagecopyresampled($thumb, $source, 0, 0, $sx, $sy, $tw, $th,
    				$minw, $minh);
    		// Output
    		$f_save = 'image' . $filetype;
    		$f_save($thumb, $thumb_path.'/'.$filename);
    		imagedestroy($thumb);
    		imagedestroy($source);
    	}
    	return ($thumb_dir . '/' . $filename);
    }
    
    private function _remove_preimage($id, $post_content = null)
    {
    	$newarr_imagepaths = array();
    	// 遍历找到所有新imagepath
    	if (!empty($post_content)) {
    		$newarr_imagepaths = $this->_find_all_imagepath($post_content);
    	}
    	$oldarr_imagepaths = array();
    	$dbpost = new Post_Model_DbTable_Post();
    	$old_content = $dbpost->get_one_value($id, 'content');
    	if (!empty($old_content)) {
    		$oldarr_imagepaths = $this->_find_all_imagepath($old_content);
    	}
    	
    	// 遍历找到所有旧imagepath
    	foreach ($oldarr_imagepaths as $imagepath) {
    		// 不在新imagepath里面的就删除
    		if (empty($newarr_imagepaths) || (!in_array($imagepath, $newarr_imagepaths))) {
    			@unlink($_SERVER['DOCUMENT_ROOT'].$imagepath);
    		}
    	}
    }
    
    private function _remove_prethumb($id, $post_index_thumb = null)
    {
    	$dbpost = new Post_Model_DbTable_Post();
    	$pre_thumb = $dbpost->get_one_value($id, 'index_thumb');
    	if ($pre_thumb != $post_index_thumb) {
    		if (!empty($pre_thumb)) @unlink($_SERVER['DOCUMENT_ROOT'].$pre_thumb);
    	}
    }
    
    private function _addpost($request, $post_id)
    {
    	// title
    	$post_title = $request->getParam('post_title');
    	// content
    	$post_content = $this->_transform_content($request->getParam('post_content'));
    	// pub_datetime
    	$post_pub_datetime = date('Y-m-d H:i:s');
    	// author
    	$post_author = Util_Global::getUsername(true);
    	if (false == $post_author) { $this->redirect('/auth/index/login'); }
    	// category and sub_category
    	$post_category = $request->getParam('post_select_bigcategory');
    	$post_sub_category = $request->getParam('post_select_subcategory');
    	// abstract
    	$post_abstract = $this->_extract_abstract($post_content);
    	// index_thumb
    	$post_index_thumb = $this->_find_thumbpath($post_content);
    	// Insert to DB
    	$dbpost = new Post_Model_DbTable_Post();
    	if (empty($post_id)) {
	    	$data = array('title' => $post_title,
	    			'content' => $post_content,
	    			'pub_datetime' => $post_pub_datetime,
	    			'author' => $post_author, 
	    			'category' => $post_category,
	    			'sub_category' => $post_sub_category,
	    			'abstract' => $post_abstract,
	    			'index_thumb' => $post_index_thumb);
	    	$id_post = $dbpost->insert($data);
	    	// SEARCH
	    	$search = new SearchAdapter();
	    	$search->addPosttoIndex($post_title, $post_content, $post_pub_datetime, $id_post);
    	} else {
    		// update record and remove thumb
    		$this->_remove_prethumb($post_id, $post_index_thumb);
    		$this->_remove_preimage($post_id, $post_content);
    		$data = array('title' => $post_title,
    				'content' => $post_content,
    				'category' => $post_category,
    				'sub_category' => $post_sub_category,
    				'abstract' => $post_abstract,
    				'index_thumb' => $post_index_thumb);
    		$where = $dbpost->getAdapter()->quoteInto('id=?', $post_id);
    		$dbpost->update($data, $where);
    		// SEARCH
    		$search = new SearchAdapter();
    		$search->deletePostinIndex($post_id);
    		$search->addPosttoIndex($post_title, $post_content, $post_pub_datetime, $post_id);
    	}
    }
    
    public function addAction()
    {
    	$this->view->editor = true;
    	$request = $this->getRequest();
    	$post_title = $request->getParam('post_title');
    	if (empty($post_title)) { // 没有提交情况下
    		$db_category = new Post_Model_DbTable_Category();
    		$bigcategorys = $db_category->getAllBigCategory();
    		$this->view->bigcategorys = $bigcategorys;
    		$this->render("edit");
    		return;
    	}
    	// add post to db
    	$post_id = $request->getParam('post_id');
    	$this->_addpost($request, $post_id);
    	
    	// forwart to list posts
    	$this->redirect('/post/post/list');
    }
    
    public function editAction()
    {
    	$this->view->editor = true;
		$request = $this->getRequest();
		$post_id = $request->getParam('post_id');
		$editable = $request->getParam('editable');
		if (empty($post_id) || (!empty($editable))) {
			// title
			$post_title = $request->getParam('post_title');
			// content
			$post_content = $this->_transform_content($request->getParam('post_content'));
			// category and sub_category
			$post_category = $request->getParam('post_select_bigcategory');
			$post_sub_category = $request->getParam('post_select_subcategory');
			
			$result = array(
				'id' => $post_id,
				'title' => $post_title,
				'content' => $post_content,
				'category' => $post_category,
				'sub_category' => $post_sub_category);
			$this->view->result = $result;
		} else {
			$db_post = new Post_Model_DbTable_Post();
			$where = $db_post->getAdapter()->quoteInto('id=?', $post_id);
			$result = $db_post->fetchRow($where);
			if (empty($result)) return false;
			$result = $result->toArray();
			$this->view->result = $result;
		}
		
		$db_category = new Post_Model_DbTable_Category();
		$bigcategorys = $db_category->getAllBigCategory();
		$this->view->bigcategorys = $bigcategorys;
		
		if (!empty($result['category'])) {
			$db_category = new Post_Model_DbTable_Category();
			$subcategorys = $db_category->getAllSubCategory($result['category']);
			$this->view->subcategorys = $subcategorys;
		}
    }
    
	public function deleteAction()
	{
		$username = Util_Global::getUsername(true);
		if (false == $username) {
			$this->redirect('/auth/index/login');
		}
		
		$request = $this->getRequest();
		$post_id = $request->getParam('post_id');
		$category = $request->getParam('category');
		
		$db_post = new Post_Model_DbTable_Post();
		if ($username != $db_post->get_one_value($post_id, 'author')) {
			$this->redirect('/index/error');
			return;
		}
		
		// DELETE upload image file and generated thumb file
		$this->_remove_prethumb($post_id);
		$this->_remove_imgandthumb($post_id);
		
		// DELETE comment
		$db_comment = new Post_Model_DbTable_Comment();
		$db_comment->deleteByPostidCascade($post_id);
		
		// DELETE post itself
		$db_post->delete_by_id($post_id);
		
		// DELETE index from SEARCH
		$search = new SearchAdapter();
		$search->deletePostinIndex($post_id);
		
    	// forwart to list posts
    	$this->redirect('/post/post/list?category='.$category);
	}
	
	public function listAction()
	{
		$request = $this->getRequest();
		$page = $request->getParam('page');
		$author = $request->getParam('author');
		$category = $request->getParam('category');
		$sub_category = $request->getParam('sub_category');
		
		$db_post = new Post_Model_DbTable_Post();
		$result = $db_post->fetch_list($page, 10, $author, $category, $sub_category);
		$this->view->result = $result;
		
		$total_count = $db_post->fetch_count($author, $category, $sub_category);
		$this->view->total_count = $total_count;
		$this->view->category = $category;
	}
	
	public function viewAction()
	{
		$username = Util_Global::getUsername();
		$this->view->username = $username;
		$db_users = new Auth_Model_DbTable_Users();
		$this->view->avatar = $db_users->fetchAvatar($username);
		
		$request = $this->getRequest();
		$post_id = $request->getParam('post_id');
		$db_post = new Post_Model_DbTable_Post();
		$where = $db_post->getAdapter()->quoteInto('id=?', $post_id);
		$result = $db_post->fetchRow($where);
		if (empty($result)) return false;
		$result = $result->toArray();
		$this->view->result = $result;
		$this->view->category = $result['category'];
		$this->view->sub_category = $result['sub_category'];
	}
	
	public function previewAction()
	{
		$username = Util_Global::getUsername(true);
		if (false == $username) { $this->redirect('/auth/index/login'); }
		$this->view->username = $username;
		$this->view->preview = true;
		
		$request = $this->getRequest();
		// id
		$post_id = $request->getParam('post_id');
		// title
		$post_title = $request->getParam('post_title');
		// content
		$post_content = $this->_transform_content($request->getParam('post_content'));
		// pub_datetime
		$post_pub_datetime = date('Y-m-d H:i:s');
		// author
		$post_author = $username;
		// category and sub_category
		$post_category = $request->getParam('post_select_bigcategory');
		$post_sub_category = $request->getParam('post_select_subcategory');
		
		$this->view->result = array(
			'id' => $post_id,
			'title' => $post_title,
			'content' => $post_content,
			'pub_datetime' => $post_pub_datetime,
			'author' => $post_author, 
			'category' => $post_category,
			'sub_category' => $post_sub_category);
	}
}
