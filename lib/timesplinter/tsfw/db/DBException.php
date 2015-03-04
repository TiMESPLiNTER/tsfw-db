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

	public function __construct(\PDOException $e, $queryString = null, $queryParams = null)
	{
		parent::__construct($e->getMessage(), 0, $e);

		$this->code = $e->getCode();
		$this->errorInfo = $e->errorInfo;
		
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