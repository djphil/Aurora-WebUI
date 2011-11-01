<?php
/*
 * Copyright (c) 2011 Contributors, http://opensimulator.org/
 * See CONTRIBUTORS for a full list of copyright holders.
 *
 * See LICENSE for the full licensing terms of this file.
 *
*/
namespace Aurora\WebUI{
	use RuntimeException;
	use InvalidArgumentException;

	use ArrayAccess;

	use PDO;

	class DB implements ArrayAccess{
		public static function i(){ // singleton
			static $instance;
			if(isset($instance) === false){
				$instance = new static();
			}
			return $instance;
		}
	
		private $PDO = array();
		public function offsetExists($offset){
			return isset($this->PDO[$offset]);
		}

		public function offsetGet($offset){
			return isset($this->PDO[$offset]) ? $this->PDO[$offset] : null;
		}

		public function offsetSet($offset, $value){
			if(isset($offset) === false){
				throw new InvalidArgumentException('key must be specified'                    , 1);
			}else if(is_string($offset) === false){
				throw new InvalidArgumentException('key must be a string'                     , 2);
			}else if(ctype_graph($offset) === false){
				throw new InvalidArgumentException('key can only contain printable characters', 3);
			}
			$this->lastKey = $offset; // doing this to aid debugging and user-friendly error reporting.
			if(($value instanceof PDO) === false){
				throw new InvalidArgumentException('Value must be an instance of PDO'         , 4);
			}else if(isset($this[$offset]) === true && $this[$offset] !== $value){ // if it's the same object then this will have little or no effect.
				throw new InvalidArgumentException('Cannot overwrite previously defined entry', 5);
			}
			$this->PDO[$offset] = $value;
		}

		public function offsetUnset($offset){
			throw new RuntimeException('DB::offsetUnset() is disabled');
		}

		protected $lastKey;
		public function lastKey(){
			return $this->lastKey;
		}
	}
}
namespace Aurora\WebUI\PDO{
	use RuntimeException;
	use PDOException;

	use PDO;
	use PDOStatement;

	class helper{
		public static function PDO($dsn, $username=null, $password=null, array $options=null){
			static $registry = array();
			if(isset($registry[$dsn]) === false){
				$registry[$dsn] = array();
			}

			$hash = md5($username . "\n" . $password . "\n" . print_r($options,true));
			if(isset($registry[$dsn][$hash]) === false){
				$registry[$dsn][$hash] = new PDO($dsn, $username, $password, $options);
			}

			return $registry[$dsn][$hash];
		}
	}
}
?>