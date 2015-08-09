<?php
class InteractorAdvert extends \Mufuphlex\ReSearcher\InteractableObject
{
	protected $_type = 'ad';
	protected static $_tokenizer = null;

	/**
	 * @param $source
	 * @return InteractorAdvert
	 */
	public static function create($source)
	{
		$obj = new self();
		$obj->setId((int)$source['id']);
		$tokens = self::_tokenizer()->tokenize($source['title']);
		$tokens = array_merge($tokens, self::_tokenizer()->tokenize($source['description']));
		$url = trim($source['url'], " \t\r\n");
		$url = preg_replace('/[\?&]utm_(?:source|campaign|media)=.*(?:$|[&#])/su', '', $url);
		$url = preg_replace('@^http\s?://(?:www\d?\.)?@su', '', $url);
		$tokens = array_merge($tokens, self::_tokenizer()->tokenize($url));
		$obj->setTokens($tokens);
		return $obj;
	}

	/**
	 * @param array $resultsIds
	 * @return mixed
	 */
	public static function createResults(array $resultsIds)
	{
		$results = array();

		foreach ($resultsIds as $id)
		{
			$results[] = array('id' => (int)$id);
		}

		return $results;
	}

	/**
	 * @return \Mufuphlex\ReSearcher\TokenizerVn
	 */
	protected static function _tokenizer()
	{
		if (!self::$_tokenizer)
		{
			self::$_tokenizer = new \Mufuphlex\ReSearcher\TokenizerVn();
		}
		return self::$_tokenizer;
	}
}

class InteractorPhrase extends InteractorAdvert
{
	protected $_type = 'ph';
	protected $_mutable = false;

	/**
	 * @param $source
	 * @return InteractorPhrase
	 */
	public static function create($source)
	{
		$obj = new self();
		$obj->setId((int)$source['id']);
		$obj->setTokens(self::_tokenizer()->tokenize($source['phrase']));
		return $obj;
	}
}