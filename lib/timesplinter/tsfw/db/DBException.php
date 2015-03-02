<?php

namespace timesplinter\tsfw\db;

/**
 * @author Pascal Muenst <dev@timesplinter.ch>
 * @copyright Copyright (c) 2012, TiMESPLiNTER
 */
class DBException extends \PDOException
{
	protected $queryString;
	protected $queryParams;

	public function __construct($message, $code, $queryString = '', $queryParams = array(), \Exception $previous = null)
	{
		parent::__construct($message, 0, $previous);

		$this->code = $code;
		$this->queryString = $queryString;
		$this->queryParams = $queryParams;
	}

	public function getQueryString()
	{
		return $this->queryString;
	}

	public function getQueryParams()
	{
		return $this->queryParams;
	}
}

/* EOF */