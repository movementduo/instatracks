<?php

	final class PDOManager extends DBConfig implements DBConfigInterface, Serializable {
	
		/**
		 * Variable holding the PDOManager instance.
		 * 
		 * @var PDOManager
		 * @access static
		 */
		private static $singleInstance;
		protected $pdoObject;
		private $prepareStatement;
	
		/**
		 * Create the single instance of class
		 *
		 * @param none
		 * @return Object self::$singleInstance Instance
		 */
		public static function getInstance() {
			if(!(self::$singleInstance instanceof self)) {
				self::$singleInstance = new self();
			}
			return self::$singleInstance;
		}

		/**
		 * 
		 */
		private function __construct() {
			parent::initializeConfiguration();
			$this->makeConnection();
		}
	
	
		/**
		 * Method for connecting to database
		 *
		 * @param none
		 * @return none
		 */
		public function makeConnection() {
			$this->pdoObject = new PDO($this->dbType.":host=".$this->sqlHost.";dbname=".$this->sqlDB.";charset=utf8", $this->sqlUser, $this->sqlPass, array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
		}
	
		/**
		 * Execute a sql query
		 *
		 * @param String $query
		 * @return Object
		 */
		public function executeSql($query, $parameter = array()) {
			$this->prepareStatement = $this->pdoObject->prepare($query);
			for($i = 0; $i < sizeof($parameter); $i++) {
				$this->prepareStatement->bindParam(":x".intval($i+1), $parameter[$i]);
			}
			$this->prepareStatement->execute();
			return $this;
		}
	
		/**
		 * Begin the transaction
		 *
		 * @param none
		 * @return none
		 */
		public function beginTransaction() {
			$this->pdoObject->beginTransaction();
			return $this;
		}
	
		/**
		 * Commit the transaction
		 *
		 * @param none
		 * @return none
		 */
		public function commitTransaction() {
			$this->pdoObject->commit();
			return $this;
		}
	
		/**
		 * Rolls back the transaction
		 *
		 * @param none
		 * @return none
		 */
		public function rollbackTransaction() {
			$this->pdoObject->rollBack();
			return $this;
		}
	
		/**
		 * Fetch associative array
		 *
		 * @param none
		 * @return none
		 */
		public function fetchAssoc() {
			$result = $this->prepareStatement->fetchAll(PDO::FETCH_ASSOC);
			$this->freeResult();
			return $result;
		}
	
		/**
		 * Fetch enumerated array
		 *
		 * @param none
		 * @return none
		 */
		public function fetchArray() {
			$result = $this->prepareStatement->fetchAll(PDO::FETCH_BOTH);
			$this->freeResult();
			return $result;
		}
	
		/**
		 * Fetch Object instead of array
		 *
		 * @param none
		 * @return none
		 */
		public function fetchObject() {
			$result = $this->prepareStatement->fetchAll(PDO::FETCH_OBJ);
			$this->freeResult();
			return $result;
		}
	
		/**
		 * Fetch the number of affected rows
		 *
		 * @param none
		 * @return int number of rows
		 */
		public function affectedRows() {
			return $this->prepareStatement->rowCount();
		}
	
		/**
		 * Fetch the last inserted id
		 *
		 * @param noe
		 * @return int last row id of table
		 */
		public function lastID() {
			return intval($this->pdoObject->lastInsertId());
		}
	
		/**
		 * Fetch the ids of last entry
		 *
		 * @param int $size
		 */
		public function multipleID($size) {
			$lastID = intval($this->lastID());
			for($i = $lastID;$i < ($lastID + $size);$i++) {
				$lastIDs[] = $i;
			}
			return $lastIDs;
		}
	
		/**
		 * Frees the database result
		 *
		 * @param none
		 * @return none
		 */
		public function freeResult() {
			$this->prepareStatement->closeCursor();		
		}
	
		/**
		 * (non-PHPdoc)
		 * @see Serializable::serialize()
		 */
		public function serialize() {
			throw new Exception("Serialization is not supported.");
		}
	
		/**
		 * (non-PHPdoc)
		 * @see Serializable::unserialize()
		 */
		public function unserialize($serialized) {
			throw new Exception("Serialization is not supported.");
		}
	
		/**
		 * Override clone method to stop cloning of the object
		 *
		 * @throws Exception
		 */
		private function __clone() {
			throw new Exception("Cloning is not supported in singleton class");
		}
	
		/**
		 * Make connection null and void. Comment the statement if you don't want this.
		 * 
		 * @param none
		 * @return none
		 */
		public function __destruct() {
			$this->pdoObject = null;
		}

		public function rowCount() {
			return intval($this->prepareStatement->rowCount());

		}
	}

	interface DBConfigInterface {
	
		/**
		 * Get the single instance of class
		 * 
		 * @param none
		 * @return Object
		 */
		public static function getInstance();
	
		/**
		 * Method for connecting to database
		 * 
		 * @param none
		 * @return none
		 */
		public function makeConnection();
	
		/**
		 * Execute a sql query
		 * 
		 * @param String $query
		 * @return Object
		 */
		public function executeSql($query);
	
		/**
		 * Begin the transaction
		 * 
		 * @param none
		 * @return none
		 */
		public function beginTransaction();
	
		/**
		 * Commit the transaction
		 *
		 * @param none
		 * @return none
		 */
		public function commitTransaction();
	
		/**
		 * Rolls back the transaction
		 *
		 * @param none
		 * @return none
		 */
		public function rollbackTransaction();
	
		/**
		 * Fetch associative array
		 * 
		 * @param none
		 * @return none
		 */
		public function fetchAssoc();
	
		/**
		 * Fetch enumerated array
		 *
		 * @param none
		 * @return none
		 */
		public function fetchArray();
	
		/**
		 * Fetch Object instead of array
		 *
		 * @param none
		 * @return none
		 */
		public function fetchObject();
	
		/**
		 * Fetch the number of affected rows
		 *
		 * @param none
		 * @return int number of rows
		 */
		public function affectedRows();
	
		/**
		 * Fetch the last inserted id
		 * 
		 * @param noe
		 * @return int last row id of table
		 */
		public function lastID();
	
		/**
		 * Fetch the ids of last entry
		 * 
		 * @param int $size
		 */
		public function multipleID($size);
	
		/**
		 * Frees the database result
		 * 
		 * @param none
		 * @return none
		 */
		public function freeResult();
		
		public function rowCount();
	}

	abstract class DBConfig {

		protected $sqlHost;
		protected $sqlUser;
		protected $sqlPass;
		protected $sqlDB;
		protected $dbType;
		protected $dbport;

		protected function initializeConfiguration() {

			$this->dbType  	= 'mysql';
			$this->sqlHost 	= DBHOST;
			$this->sqlUser 	= DBUSER;
			$this->sqlPass 	= DBPASS;
			$this->sqlDB 	= DBFILE;
			$this->dbport 	= defined('DBPORT')?DBPORT:3306;
		}
	}