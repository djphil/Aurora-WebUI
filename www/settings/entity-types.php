<?php
namespace Aurora{
	interface Region{
		public function ScopeID();
		public function RegionUUID();
		public function RegionName();
		public function LocX();
		public function LocY();
		public function LocZ();
		public function OwnerUUID();
		public function Access();
		public function SizeX();
		public function SizeY();
		public function SizeZ();
		public function Flags();
		public function SessionID();
		public function Info();
	}

	function is_uuid($uuid){
		return is_string($uuid) && preg_match('/^[A-Fa-f\d]{8}\-[A-Fa-f\d]{4}\-[A-Fa-f\d]{4}\-[A-Fa-f\d]{4}\-[A-Fa-f\d]{12}$/', $uuid) === 1;
	}
}

namespace Aurora\WebUI{
	use InvalidArgumentException;
	use RuntimeException;
	use PDOException;

	use PDO;

	use Aurora;
	use Aurora\Region as typeRegion;

	abstract class Region implements typeRegion{
		protected function __construct(){ // here to prevent direct instantiation
			$this->ScopeID    = static::validate_ScopeID   ( $this->ScopeID    );
			$this->RegionUUID = static::validate_RegionUUID( $this->RegionUUID );
			$this->RegionName = static::validate_RegionName( $this->RegionName );
			$this->LocX       = static::validate_LocX      ( $this->LocX       );
			$this->LocY       = static::validate_LocY      ( $this->LocY       );
			$this->LocZ       = static::validate_LocZ      ( $this->LocZ       );
			$this->OwnerUUID  = static::validate_OwnerUUID ( $this->OwnerUUID  );
			$this->Access     = static::validate_Access    ( $this->Access     );
			$this->SizeX      = static::validate_SizeX     ( $this->SizeX      );
			$this->SizeY      = static::validate_SizeY     ( $this->SizeY      );
			$this->SizeZ      = static::validate_SizeZ     ( $this->SizeZ      );
			$this->Flags      = static::validate_Flags     ( $this->Flags      );
			$this->SessionID  = static::validate_SessionID ( $this->SessionID  );
			$this->Info       = static::validate_Info      ( $this->Info       );
		}

		protected $ScopeID;
		public function ScopeID(){
			return $this->ScopeID;
		}

		protected $RegionUUID;
		public function RegionUUID(){
			return $this->RegionUUID;
		}

		protected $RegionName;
		public function RegionName(){
			return $this->RegionName;
		}

		protected $LocX;
		public function LocX(){
			return $this->LocX;
		}
		protected $LocY;
		public function LocY(){
			return $this->LocY;
		}
		protected $LocZ;
		public function LocZ(){
			return $this->LocZ;
		}

		protected $OwnerUUID;
		public function OwnerUUID(){
			return $this->OwnerUUID;
		}

		protected $Access;
		public function Access(){
			return $this->Access;
		}

		protected $SizeX;
		public function SizeX(){
			return $this->SizeX;
		}
		protected $SizeY;
		public function SizeY(){
			return $this->SizeY;
		}
		protected $SizeZ;
		public function SizeZ(){
			return $this->SizeZ;
		}

		protected $Flags;
		public function Flags(){
			return $this->Flags;
		}

		protected $SessionID;
		public function SessionID(){
			return $this->SessionID;
		}

		protected $Info;
		public function Info(){
			return $this->Info;
		}

		protected static function validate_Int($value){
			if(is_integer($value) === false){
				if((is_float($value) && ($value % 1) === 0) || (is_string($value) && ctype_digit($value))){
					
				}else{
					throw new InvalidArgumentException('Integer should be an integer, integer-as-float or integer-as-string');
				}
			}
			return $value;
		}

		public static function validate_UUID($value){
			if(is_string($value) === false){
				throw new InvalidArgumentException('Value should be a string'    , 1);
			}else if(Aurora\is_uuid($value) === false){
				throw new InvalidArgumentException('Value should be a valid UUID', 2);
			}
			return $value;
		}

		protected static function validate_ScopeID($value){
			try{
				return static::validate_UUID($value);
			}catch(InvalidArgumentException $e){
				throw new InvalidArgumentException('Scope ID was not valid', 10 + $e->getCode());
			}
		}

		protected static function validate_RegionUUID($value){
			try{
				return static::validate_UUID($value);
			}catch(InvalidArgumentException $e){
				throw new InvalidArgumentException('RegionUUID was not valid', 20 + $e->getCode());
			}
		}

		protected static function validate_RegionName($value){
			if(is_string($value) === false){
				throw new InvalidArgumentException('Region name should be string', 31);
			}
			return $value;
		}

		protected static function validate_LocX($value){
			try{
				return static::validate_Int($value);
			}catch(InvalidArgumentException $e){
				throw new InvalidArgumentException('X-axis location should be an integer', 41);
			}
		}

		protected static function validate_LocY($value){
			try{
				return static::validate_Int($value);
			}catch(InvalidArgumentException $e){
				throw new InvalidArgumentException('Y-axis location should be an integer', 51);
			}
		}

		protected static function validate_LocZ($value){
			try{
				return static::validate_Int($value);
			}catch(InvalidArgumentException $e){
				throw new InvalidArgumentException('Z-axis location should be an integer', 61);
			}
		}

		protected static function validate_OwnerUUID($value){
			try{
				return static::validate_UUID($value);
			}catch(InvalidArgumentException $e){
				throw new InvalidArgumentException('OwnerUUID was not valid', 70 + $e->getCode());
			}
		}

		protected static function validate_Access($value){
			try{
				return static::validate_Int($value);
			}catch(InvalidArgumentException $e){
				throw new InvalidArgumentException('Access should be an integer', 81);
			}
		}

		protected static function validate_SizeX($value){
			try{
				return static::validate_Int($value);
			}catch(InvalidArgumentException $e){
				throw new InvalidArgumentException('X-axis size should be an integer', 91);
			}
		}

		protected static function validate_SizeY($value){
			try{
				return static::validate_Int($value);
			}catch(InvalidArgumentException $e){
				throw new InvalidArgumentException('Y-axis size should be an integer', 101);
			}
		}

		protected static function validate_SizeZ($value){
			try{
				return static::validate_Int($value);
			}catch(InvalidArgumentException $e){
				throw new InvalidArgumentException('Z-axis size should be an integer', 111);
			}
		}

		protected static function validate_Flags($value){
			try{
				return static::validate_Int($value);
			}catch(InvalidArgumentException $e){
				throw new InvalidArgumentException('Flags should be an integer', 121);
			}
		}

		protected static function validate_SessionID($value){
			try{
				return static::validate_UUID($value);
			}catch(InvalidArgumentException $e){
				throw new InvalidArgumentException('Session ID was not valid', 130 + $e->getCode());
			}
		}

		protected static function validate_Info($value){
			if(is_string($value) === false){
				throw new InvalidArgumentException('Info should be a string', 141);
			}
			return $value;
		}
	}

	class RegionFromDB extends Region{
		const sql_get_by_uuid =
'SELECT
	ScopeID,
	RegionUUID,
	RegionName,
	LocX,
	LocY,
	LocZ,
	OwnerUUID,
	Access,
	SizeX,
	SizeY,
	SizeZ,
	Flags,
	SessionID,
	Info
FROM
	gridregions
WHERE
	RegionUUID = :RegionUUID';
		public static function r(PDO $db, $uuid){
			static $registry = array();

			$hash = spl_object_hash($db);
			if(isset($registry[$hash]) === false){
				$registry[$hash] = array();
			}

			$uuid = static::validate_RegionUUID($uuid);
			if(isset($registry[$hash][$uuid]) === false){
				try{
					$sth = $db->prepare(static::sql_get_by_uuid);
				}catch(PDOException $e){
					throw new RuntimeException('Could not prepare query (check ' . get_called_class()  . '::sql_get_by_uuid )', 1001);
				}
				try{
					$sth->bindValue(':RegionUUID', $uuid);
				}catch(PDOException $e){
					throw new RuntimeException('Could not bind UUID to query', 1002);
				}

				try{
					$sth->execute();
				}catch(PDOException $e){
					throw new RuntimeException('Could not execute query', 1003);
				}

				try{
					$registry[$hash][$uuid] = $sth->fetchObject(get_called_class());
				}catch(PDOException $e){
					throw new RuntimeException('Could not get region object', 1004);
				}catch(InvalidArgumentException $e){
					throw new RuntimeException('There appears to be some invalid data in the database:' . "\n" + $e->getMessage(), 1005);
				}

				if(($registry[$hash][$uuid] instanceof typeRegion) === false){
					throw new RuntimeException('Failed to get region object', 1006);
				}
			}

			return $registry[$hash][$uuid];
		}

		const sql_get_uuid_by_LocX_LocY =
'SELECT
	RegionUUID
FROM
	gridregions
WHERE
	LocX = :LocX AND
	LocY = :LocY
LIMIT 1';
		public static function get_by_LocX_LocY(PDO $db, $LocX, $LocY){
			try{
				$LocX = static::validate_LocX($LocX);
			}catch(InvalidArgumentException $e){
				throw new InvalidArgumentException('X-axis argument was invalid', 1101);
			}
			try{
				$LocY = static::validate_LocY($LocY);
			}catch(InvalidArgumentException $e){
				throw new InvalidArgumentException('Y-axis argument was invalid', 1102);
			}

			try{
				$sth = $db->prepare(static::sql_get_uuid_by_LocX_LocY);
			}catch(PDOException $e){
				throw new RuntimeException('Could not prepare query (check ' . get_called_class() . '::sql_get_uuid_by_LocX_LocY )', 1103);
			}

			try{
				$sth->bindValue(':LocX', $LocX, PDO::PARAM_INT);
				$sth->bindValue(':LocY', $LocY, PDO::PARAM_INT);
			}catch(PDOException $e){
				throw new RuntimeException('Could not bind arguments', 1104);
			}

			try{
				$sth->execute();
			}catch(PDOException $e){
				throw new RuntimeException('Could not execute query', 1105);
			}

			$uuid = null;
			try{
				$uuid = $sth->fetchColumn();
			}catch(PDOException $e){
				throw new RuntimeException('Could not fetch UUID', 1106);
			}

			if($uuid === false){
				throw new RuntimeException('Could not fetch UUID', 1107);
			}

			try{
				$uuid = static::validate_RegionUUID($uuid);
			}catch(InvalidArgumentException $e){
				throw new RuntimeException('There appears to be some invalid data in the database', 1108);
			}

			return static::r($db, $uuid);
		}
	}
}
?>