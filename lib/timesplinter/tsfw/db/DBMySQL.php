<?php

namespace timesplinter\tsfw\db;

/**
 * @author Pascal Münst <dev@timesplinter.ch>
 * @copyright Copyright (c) 2012, TiMESPLiNTER Webdevelopment
 */
class DBMySQL extends DB
{
	protected static $defaultFetchStyle = self::FETCH_OBJ;

	public function __construct(DBConnect $dbConnect)
	{
		$this->dbConnect = $dbConnect;

		try {
			parent::__construct(
				'mysql:host=' . $dbConnect->getHost() . ';dbname=' . $dbConnect->getDatabase() . ';charset=' . $dbConnect->getCharset()
				, $dbConnect->getUsername()
				, $dbConnect->getPassword()
			);

			$this->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

			$this->query("SET NAMES '" . $dbConnect->getCharset() . "'");
			$this->query("SET CHARSET '" . $dbConnect->getCharset() . "'");

			$this->triggerListeners('onConnect', array($this, $this->dbConnect));
		} catch(\PDOException $e) {
			throw new DBException('PDO could not connect to database: ' . $e->getMessage(), $e->getCode());
		}
	}

	public function prepareStatement($sql)
	{
		return self::prepare($sql);
	}
	
	public function prepare($sql, $driver_options = array())
	{
		try {
			$stmnt = parent::prepare($sql, $driver_options);

			$this->triggerListeners('onPrepare', array($this, $stmnt));

			return $stmnt;
		} catch(\PDOException $e) {
			throw new DBException('PDO could not prepare query: ' . $e->getMessage(), $e->getCode(), $sql);
		}
	}

	public function select(\PDOStatement $stmnt, array $params = array(), $fetchStyle = null, $fetchArgument = null, array $ctorArgs = array())
	{
		$fetchStyle = ($fetchStyle !== null) ? $fetchStyle : self::$defaultFetchStyle;

		try {
			$this->triggerListeners('beforeSelect', array($this, $stmnt, $params));

			$stmnt->execute($params);

			$this->triggerListeners('afterSelect', array($this, $stmnt, $params));

			if($fetchStyle !== \PDO::FETCH_CLASS && $fetchStyle !== self::FETCH_FUNC)
				return $stmnt->fetchAll($fetchStyle);
			else
				return $stmnt->fetchAll($fetchStyle, $fetchArgument);
		} catch(\PDOException $e) {
			throw new DBException('PDO could not execute select query: ' . $e->getMessage(), $e->getCode(), $stmnt->queryString, $params);
		}
	}

	public function insert(\PDOStatement $stmnt, array $params = array())
	{
		try {
			$this->triggerListeners('beforeMutation', array($this, $stmnt, $params, DBListener::QUERY_TYPE_INSERT));

			$stmnt->execute($params);

			$this->triggerListeners('afterMutation', array($this, $stmnt, $params, DBListener::QUERY_TYPE_INSERT));

			return $this->lastInsertId();
		} catch(\PDOException $e) {
			throw new DBException('PDO could not execute insert query: ' . $e->getMessage(), $e->getCode(), $stmnt->queryString, $params);
		}
	}

	public function update(\PDOStatement $stmnt, array $params = array())
	{
		try {
			$this->triggerListeners('beforeMutation', array($this, $stmnt, $params, DBListener::QUERY_TYPE_UPDATE));

			$stmnt->execute($params);

			$this->triggerListeners('afterMutation', array($this, $stmnt, $params, DBListener::QUERY_TYPE_UPDATE));

			return $stmnt->rowCount();
		} catch(\PDOException $e) {
			throw new DBException('PDO could not execute update query: ' . $e->getMessage(), $e->getCode(), $stmnt->queryString, $params);
		}
	}

	public function delete(\PDOStatement $stmnt, array $params)
	{
		try {
			$this->triggerListeners('beforeMutation', array($this, $stmnt, $params, DBListener::QUERY_TYPE_DELETE));

			$stmnt->execute($params);

			$this->triggerListeners('afterMutation', array($this, $stmnt, $params, DBListener::QUERY_TYPE_DELETE));

			return $stmnt->rowCount();
		} catch(\PDOException $e) {
			throw new DBException('PDO could not execute delete query: ' . $e->getMessage(), $e->getCode(), $stmnt->queryString, $params);
		}
	}

	public function getDbConnect()
	{
		return $this->dbConnect;
	}

	public static function setDefaultFetchMode($defaultFetchStyle)
	{
		self::$defaultFetchStyle = $defaultFetchStyle;
	}
}

/* EOF */