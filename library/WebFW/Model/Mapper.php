<?php

namespace WebFW\Model;
use PDO;

class Mapper
{
	protected static $conn;
	
	public static function setConnection(PDO $connection) {
		self::$conn = $connection;
	}
}