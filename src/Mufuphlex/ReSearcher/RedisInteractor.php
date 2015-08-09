<?php
namespace Mufuphlex\ReSearcher;

/**
 * Class RedisInteractor
 */
class RedisInteractor
{
	const REDIS_DB = 1;
	const REDIS_NAMESPACE = 'RI:';

	const KEY_PREFIX_TOKEN = 'tkn';
	const KEY_PREFIX_ENTRY = 'ent';
	const KEY_PREFIX_DEFAULT = 'RI:';

	const SET_NAME_KNOWN_TYPES = 'known_types';

	/** @var array */
	protected $_connectConfig = array();

	/** @var string */
	protected $_keyPrefixToken = self::KEY_PREFIX_TOKEN;
	/** @var string */
	protected $_keyPrefixEntry = self::KEY_PREFIX_ENTRY;
	/** @var string */
	protected $_keyPrefixDefault = self::KEY_PREFIX_DEFAULT;
	/** @var string */
	protected $_setNameKnownTypes = self::SET_NAME_KNOWN_TYPES;

	/**
	 * @param array $connectConfig
	 * <dl>
	 * <dt>string namespace</dt><dd>Prefix for redis keys</dd>
	 * <dt>int db</dt><dd>Redis DB num</dd>
	 * </dl>s
	 */
	public function __construct(array $connectConfig = array())
	{
		if (empty($connectConfig['namespace']))
		{
			$connectConfig['namespace'] = '';
		}

		$connectConfig['namespace'] = static::REDIS_NAMESPACE.$connectConfig['namespace'];

		if (empty($connectConfig['db']))
		{
			$connectConfig['db'] = static::REDIS_DB;
		}

		$this->_connectConfig = $connectConfig;
		$this->_redisUtil = new \Mufuphlex\Util\RedisUtil($this->_connectConfig);
	}

	/**
	 * @param \Mufuphlex\Util\RedisUtil $redisUtil
	 * @return $this
	 */
	public function setRedisUtil(\Mufuphlex\Util\RedisUtil $redisUtil)
	{
		if ($this->_redisUtil !== null)
		{
			throw new Exception('Property "redisUtil" can not be redefined');
		}
		$this->_redisUtil = $redisUtil;
		return $this;
	}

	/**
	 * @return \Mufuphlex\Util\RedisUtil
	 */
	public function getRedisUtil()
	{
		return $this->_redisUtil;
	}

	/**
	 * @return array
	 */
	public function getKnownTypes()
	{
		$this->_redisUtil->setGet(static::SET_NAME_KNOWN_TYPES);
	}

	/**
	 * @param string $str
	 * @param string $prefix
	 * @return string
	 */
	public function makeKeyName($str, $prefix = self::KEY_PREFIX_DEFAULT, $postfix = '')
	{
		$parts = array($prefix, $str);

		if ($postfix)
		{
			$parts[] = $postfix;
		}

		return implode('_', $parts);
	}

	/**
	 * @return string
	 */
	public function getPrefixToken()
	{
		return $this->_keyPrefixToken;
	}

	/**
	 * @return string
	 */
	public function getPrefixEntry()
	{
		return $this->_keyPrefixEntry;
	}

	/**
	 * @return string
	 */
	public function getPrefixDefault()
	{
		return $this->_keyPrefixDefault;
	}

	/**
	 * @return string
	 */
	public function getSetNameKnownTypes()
	{
		return $this->_setNameKnownTypes;
	}
}