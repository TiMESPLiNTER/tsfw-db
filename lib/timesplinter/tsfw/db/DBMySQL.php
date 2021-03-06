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
				'mysql:host=' . $dbConnect->getHost() . ';dbname=' . $dbConnect->getDatabase() .  ($dbConnect->getCharset() !== null ? ';charset=' . $dbConnect->getCharset() : null)
				, $dbConnect->getUsername()
				, $dbConnect->getPassword()
			);

			$this->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
			$this->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);

			if(($charset = $dbConnect->getCharset()) !== null) {
				$this->query("SET NAMES '" . $charset . "'");
				$this->query("SET CHARSET '" . $charset . "'");
			}

			$this->triggerListeners('onConnect', array($this, $this->dbConnect));
		} catch(\PDOException $e) {
			throw new DBException($e);
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
			throw new DBException($e, $sql);
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
			throw new DBException($e, $stmnt->queryString, $params);
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
			throw new DBException($e, $stmnt->queryString, $params);
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
			throw new DBException($e, $stmnt->queryString, $params);
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
			throw new DBException($e, $stmnt->queryString, $params);
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