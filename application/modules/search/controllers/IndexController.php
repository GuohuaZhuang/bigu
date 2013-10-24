<?php

require_once(APPLICATION_PATH . '/modules/search/models/SearchAdapter.php');
require_once(APPLICATION_PATH . '/modules/search/models/CnLuceneAnalyzer.php');

class Search_IndexController extends Zend_Controller_Action
{
	private $search;
	
    public function init()
    {
    	$this->search = new SearchAdapter();
    }
    
    /*
     * No longer use
     */
    public function testaddAction()
    {
    	Zend_Search_Lucene_Analysis_Analyzer::setDefault(new CN_Lucene_Analyzer());
    	setlocale(LC_ALL, 'zh_CN.UTF-8');
    	$indexPath = (APPLICATION_PATH . '/../data/');
    	try {
    		$index = Zend_Search_Lucene::open($indexPath);
    	} catch (Exception $e) {
    		echo 'Message: ' . $e->getMessage();
    		echo "\nAnd now is create index.\n";
    		$index = Zend_Search_Lucene::create($indexPath);
    	}
    	$doc = new Zend_Search_Lucene_Document();
    	
    	$title = "用人不器官我已经修改喽";
    	$content = "The Left Way 1 content";
    	$id_post = 1;
    	
    	$doc->addField(Zend_Search_Lucene_Field::Text('title', $title, 'UTF-8'));
    	$doc->addField(Zend_Search_Lucene_Field::UnStored('content', $content, 'UTF-8'));
    	$doc->addField(Zend_Search_Lucene_Field::Keyword('pub_datetime', time()));
    	$doc->addField(Zend_Search_Lucene_Field::Binary('id_post', $id_post));
    	$index->addDocument($doc);
    	
    	// stop layout and render
    	$this->_helper->layout->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(TRUE);
    }
    /*
     * No longer use
    */
    public function testqueryAction()
    {
    	Zend_Search_Lucene_Analysis_Analyzer::setDefault(new CN_Lucene_Analyzer());
    	setlocale(LC_ALL, 'zh_CN.UTF-8');
    	Zend_Search_Lucene::setDefaultSearchField('content');
    	$indexPath = (APPLICATION_PATH . '/../data/');
		$keyword = "用造";
		$index = Zend_Search_Lucene::open($indexPath);
		$num = $index->numDocs();
		$count = $index->count();
		echo '$num = ' . $num . "<br/>\n";
		echo '$count = ' . $count . "<br/>\n";
		echo "**********************************************************<br/><br/>\n\n";
		
		$queryStr = 'title:('.$keyword.') OR content:('.$keyword.')';
// 		$queryStr = 'title:(用)';
		$query = Zend_Search_Lucene_Search_QueryParser::parse($queryStr, 'UTF-8');
		$hits = $index->find($query);
		
        if (!empty($hits)) {
	    	foreach ($hits as $hit) {
	    		$id = $hit->id;
	    		$score = $hit->score;
	    		$title = $hit->title;
	    		//$content = $hit->content;
	    		$pub_datetime = $hit->pub_datetime;
	    		$id_post = $hit->id_post;
	    		echo '$id = ' . $id . "<br/>\n";
	    		echo '$score = ' . $score . "<br/>\n";
	    		echo '$title = ' . $title . "<br/>\n";
	    		//echo '$content = ' . $content . "<br/>\n";
	    		echo '$pub_datetime = ' . $pub_datetime . "<br/>\n";
	    		echo '$id_post = ' . $id_post . "<br/>\n";
	    		echo "===========================<br/>\n";
	    	}
        }
    	 
    	// stop layout and render
    	$this->_helper->layout->disableLayout();
    	$this->_helper->viewRenderer->setNoRender(TRUE);
    }
    /*
     * No longer use
    */
    public function pagination($q, &$total_count, $page = 1, $count = 10)
    {
    	$frontendOptions = array('lifeTime' => 3600, 'automatic_serialization' => true);
    	$backendOptions = array('cache_dir' => APPLICATION_PATH.'/../tmp/');
    	$cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
    	$cache->clean(Zend_Cache::CLEANING_MODE_OLD);
    	
    	$cacheId = md5($q);
    	echo '《《《《《《《《《《$total_count = ' . $total_count . "<br/>\n";
    	if (!$resultSet = $cache->load($cacheId)) {
    		echo '《《《《《《《《《DO SEARCH IN INDEX》》》》》》》》》》'."<br/>\n";
    		$hits = $this->search->queryPostinIndex($q);
    		if (empty($hits)) return null;
    		$resultSet = array();
    		$total_count = 0;
    		foreach ($hits as $hit) {
    			$resultSetEntry          = array();
    			$resultSetEntry['id']    = $hit->id;
    			$resultSetEntry['score'] = $hit->score;
    			$resultSet[] = $resultSetEntry;
    			$total_count ++;
    		}
    		$cache->save($resultSet, $cacheId);
    	}
    	if ($page <= 0) $page = 1;
    	$startId = ($page - 1) * $count;
    	$endId = $startId + $count;
    	$publishedResultSet = array();
    	for ($resultId = $startId; $resultId < $endId; $resultId++) {
    		$publishedResultSet[$resultId] = array(
    				'id'    => $resultSet[$resultId]['id'],
    				'score' => $resultSet[$resultId]['score'],
    				'doc'   => $index->getDocument($resultSet[$resultId]['id']),
    		);
    	}
    	return $publishedResultSet;
    }
    
    public function indexAction()
    {
    	// invoke pagination
    	$request = $this->getRequest();
    	$page = $request->getParam('page');
    	$q = $request->getParam('q');
    	
    	$total_count = 0;
    	$result = $this->search->queryPostinIndexwithPagination($q, &$total_count, $page, 10);
    	$this->view->result = $result;
    	$this->view->total_count = $total_count;
    	
    	
//     	foreach ($hits as $hit) {
//     		$id = $hit->id;
//     		$score = $hit->score;
//     		$title = $hit->title;
//     		$pub_datetime = $hit->pub_datetime;
//     		$id_post = $hit->id_post;
//     		echo '$id = ' . $id . "<br/>\n";
//     		echo '$score = ' . $score . "<br/>\n";
//     		echo '$title = ' . $title . "<br/>\n";
//     		echo '$pub_datetime = ' . $pub_datetime . "<br/>\n";
//     		echo '$id_post = ' . $id_post . "<br/>\n";
//     		echo "===========================<br/>\n";
//     	}
    }

}
