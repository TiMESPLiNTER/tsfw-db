<?php

namespace timesplinter\tsfw\db;

/**
 * @author Pascal Muenst <dev@timesplinter.ch>
 * @copyright Copyright (c) 2012, TiMESPLiNTER
 */
class DBConnect
{
	private $host;
	private $username;
	private $password;
	private $charset;
	private $database;

	public function __construct($host, $database, $username, $password, $charset = 'UTF8')
	{
		$this->host = $host;
		$this->database = $database;
		$this->username = $username;
		$this->password = $password;
		$this->charset = $charset;
	}

	public function getHost()
	{
		return $this->host;
	}

	public function getUsername()
	{
		return $this->username;
	}

	public function getPassword()
	{
		return $this->password;
	}

	public function getCharset()
	{
		return $this->charset;
	}

	public function getDatabase()
	{
		return $this->database;
	}
}

/* EOF */