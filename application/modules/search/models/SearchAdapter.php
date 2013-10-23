<?php

require_once(APPLICATION_PATH . '/modules/search/models/CnLuceneAnalyzer.php');

class SearchAdapter
{
	private $indexPath = '';
	
	public function __construct()
	{
		setlocale(LC_ALL, 'zh_CN.UTF-8');
		$this->indexPath = (APPLICATION_PATH . '/../data/');
		Zend_Search_Lucene_Analysis_Analyzer::setDefault(new CN_Lucene_Analyzer());
	}
	
	public function addPosttoIndex($title, $content, $pub_datetime, $id_post)
	{
		// prepare to format data field value
		$content = strip_tags($content);
		$date = DateTime::createFromFormat('Y-m-d H:i:s', $pub_datetime);
		$pub_datetime = $date->getTimestamp();
		
		try {
			$index = Zend_Search_Lucene::open($this->indexPath);
		} catch (Exception $e) {
			echo 'Message: ' . $e->getMessage();
			echo "第一次建搜索索引库，不要大惊小怪^_^。<br/>\n";
			echo "\nAnd now is create index.\n";
			$index = Zend_Search_Lucene::create($this->indexPath);
		}
		
		$doc = new Zend_Search_Lucene_Document();
		$doc->addField(Zend_Search_Lucene_Field::Text('title', $title, 'UTF-8'));
		$doc->addField(Zend_Search_Lucene_Field::UnStored('content', $content, 'UTF-8'));
		$doc->addField(Zend_Search_Lucene_Field::Keyword('pub_datetime', $pub_datetime));
		$doc->addField(Zend_Search_Lucene_Field::Keyword('id_post', $id_post));
		$index->addDocument($doc);
		
		$index->commit();
	}
	
	public function deletePostinIndex($id_post)
	{
		try {
			$index = Zend_Search_Lucene::open($this->indexPath);
			$term = new Zend_Search_Lucene_Index_Term($id_post, 'id_post');
			$query = new Zend_Search_Lucene_Search_Query_Term($term);
			$hits  = $index->find($query);
			foreach ($hits as $hit) {
				$index->delete($hit->id);
			}
		} catch (Exception $e) {
			echo 'Message: ' . $e->getMessage();
			echo "“管理员和搜索索引数据库去月球私奔了，赶紧Email给".ADMIN_EMAIL."”<br/>\n";
		}
	}
	
	public function queryPostinIndex($keyword)
	{
		try {
			if (empty($keyword)) return null;
			$index = Zend_Search_Lucene::open($this->indexPath);
			
			$query = new Zend_Search_Lucene_Search_Query_Boolean();
			$queryTokens = Zend_Search_Lucene_Analysis_Analyzer::getDefault()->tokenize($keyword, 'utf-8');
			foreach ($queryTokens as $token) {
				$subquery = new Zend_Search_Lucene_Search_Query_MultiTerm();
				$subquery->addTerm(new Zend_Search_Lucene_Index_Term($token->getTermText(), 'title'), null);
				$subquery->addTerm(new Zend_Search_Lucene_Index_Term($token->getTermText(), 'content'), null);
				$query->addSubquery($subquery, true);
			}
			// $queryStr = 'title:('.$keyword.') OR content:('.$keyword.')';
			// $query = Zend_Search_Lucene_Search_QueryParser::parse($queryStr, 'UTF-8');
			// echo $query->__toString();
			$hits = $index->find($query, 'id_post', SORT_NUMERIC, SORT_DESC);
			return $hits;
		} catch (Exception $e) {
			echo 'Message: ' . $e->getMessage() . "<br/>\n";
			echo "“管理员和搜索索引数据库去月球私奔了，赶紧Email给".ADMIN_EMAIL."”<br/>\n";
		}
		return null;
	}
	
	public function queryPostinIndexwithPagination($keyword, &$total_count, $page = 1, $count = 10)
	{
		// open lucene index
		try {
			if (empty($keyword)) return null;
			$index = Zend_Search_Lucene::open($this->indexPath);
		} catch (Exception $e) {
			echo 'Message: ' . $e->getMessage() . "<br/>\n";
			echo "“管理员和搜索索引数据库去月球私奔了，赶紧Email给".ADMIN_EMAIL."”<br/>\n";
		}
		// init cache
		$frontendOptions = array('lifeTime' => 600, 'automatic_serialization' => true);
		$backendOptions = array('cache_dir' => APPLICATION_PATH.'/../tmp/');
		$cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
		$cache->clean(Zend_Cache::CLEANING_MODE_OLD);
		// pagination
		$cacheId = md5($keyword);
		// query
		$query_highlight_word = array();
		$query = new Zend_Search_Lucene_Search_Query_Boolean();
		$queryTokens = Zend_Search_Lucene_Analysis_Analyzer::getDefault()->tokenize($keyword, 'utf-8');
		foreach ($queryTokens as $token) {
			$query_highlight_word[] = $token->getTermText();
			$subquery = new Zend_Search_Lucene_Search_Query_MultiTerm();
			$subquery->addTerm(new Zend_Search_Lucene_Index_Term($token->getTermText(), 'title'), null);
			$subquery->addTerm(new Zend_Search_Lucene_Index_Term($token->getTermText(), 'content'), null);
			$query->addSubquery($subquery, true);
		}
		if (!$resultSet = $cache->load($cacheId)) {
			// Search in Lucene
			$hits = $index->find($query, 'id_post', SORT_NUMERIC, SORT_DESC);
			
			if (empty($hits)) return null;
			$resultSet = array();
			foreach ($hits as $hit) {
				$resultSetEntry          = array();
				$resultSetEntry['id']    = $hit->id;
				$resultSetEntry['score'] = $hit->score;
				$resultSet[] = $resultSetEntry;
			}
			$cache->save($resultSet, $cacheId);
		}
		$total_count = count($resultSet);
		if ($page <= 0) $page = 1;
		$startId = ($page - 1) * $count;
		$endId = $startId + $count;
		$endId = ($endId <= $total_count) ? $endId : $total_count;
		$publishedResultSet = array();
		for ($resultId = $startId; $resultId < $endId; $resultId++) {
			$document = $index->getDocument($resultSet[$resultId]['id']);
			$publishedResultSet[$resultId] = array(
					'id'    => $resultSet[$resultId]['id'],
					'score' => $resultSet[$resultId]['score'],
					'title' => $this->getHightlightResult($document->getFieldUtf8Value('title'), $query_highlight_word),
					'pub_datetime' => $document->getFieldValue('pub_datetime'),
					'id_post' => $document->getFieldValue('id_post')
			);
		}
		return $publishedResultSet;
	}
	
	public function getHightlightResult($str, $words)
	{
		foreach ($words as $word) {
			if (strstr("<b style=\"color:red\"></b>", $word) == false) {
				$str = preg_replace("/(".$word.")/i","<b style=\"color:red\">\\1</b>", $str);
			}
		}
		return $str;
	}
}
