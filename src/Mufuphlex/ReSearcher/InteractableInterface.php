<?php
namespace Mufuphlex\ReSearcher;

/**
 * Interface InteractableInterface
 * @package Mufuphlex\ReSearcher
 */
interface InteractableInterface
{
	/**
	 * @return string
	 */
	public function getId();

	/**
	 * @return array
	 */
	public function getTokens();

	/**
	 * @return array
	 */
	public function getTokensUnique();

	/**
	 * @return string
	 */
	public function getType();

	/**
	 * @return boolean
	 */
	public function isMutable();

	/**
	 * @param $source
	 * @return $this
	 */
	public static function create($source);

	/**
	 * @param array $resultsIds
	 * @return mixed
	 */
	public static function createResults(array $resultsIds);
}