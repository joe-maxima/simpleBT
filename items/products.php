<?php
// products.php
if(defined("ROOT_PATH")){
	require_once(ROOT_PATH.'items/define.php');
} else {
	require_once('../items/define.php');
}
if(DEBUG_MODE == True){
	require_once(ROOT_PATH.'items/dBug.php');
}

class item_product {

	/* get a specified record
	*
	* $pProductCode ... Target product code / Integer
	*
	*/
	function getOne($pProductCode){
		try{
			$commonItem = new item_common();

			$pdo = new PDO(HOST.DBNAME, USER, PASS,array(PDO::ATTR_EMULATE_PREPARES => false,PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET `utf8`"));

			$sql  = "SELECT * FROM t_product WHERE product_code = :code ";

			$stmt = $pdo->prepare($sql);
			$stmt->bindParam('code', $pProductCode);
			$stmt->execute();
			$result = $stmt->fetchAll();

			// var_dump($result);

			return $result;

		}catch (PDOException $e){
			print('Connection failed:'.$e->getMessage());
			die();
		}
	}

	/*
	*
	* get all records
	*
	*/
	function getAllItems() {
		try{

			$commonItem = new item_common();

			if(DEBUG_MODE == True){
				error_log(">>> getAllItems start.\n",3,$commonItem->getLogPath());
			}

			$pdo = new PDO(HOST.DBNAME, USER, PASS,array(PDO::ATTR_EMULATE_PREPARES => false,PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET `utf8`"));

			$sql  = "SELECT * FROM t_product ORDER BY product_code ";

			$stmt = $pdo->prepare($sql);
			$stmt->execute();
			$result = $stmt->fetchAll();

			// var_dump($result);

			return $result;

		}catch (PDOException $e){
			print('Connection failed:'.$e->getMessage());
			die();
		}
	}

	/*
	*
	* get max product code
	*
	* $pdo ... DB access object
	*
	*/
	function getMaxCode($pdo) {
		try{
			$sql = "SELECT MAX(product_code) AS maxcode FROM t_product ";
			
			$stmt = $pdo->prepare($sql);

			$stmt->execute();
			$maxcode = $stmt->fetchColumn();

			if($maxcode > 0){
				return $maxcode;
			} else {
				return 0;
			}
		}catch (PDOException $e){
			print('Connection failed:'.$e->getMessage());
			die();
		}
	}

	/*
	*
	* add 1 record
	*
	* $pProductName ... Product name for register / String
	*
	*/
	function addData($pProductName) {
		try{
			$pdo = new PDO(HOST.DBNAME, USER, PASS,array(PDO::ATTR_EMULATE_PREPARES => false,PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET `utf8`"));
			$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			// get currenct max product code, and increment
			$pid = ( $this->getMaxCode($pdo) + 1);

			$sql = "INSERT INTO t_product ";
			$sql.= "( product_code,product_name )";
			$sql.= " VALUES ";
			$sql.= "( :product_code,:product_name )";

			$stmt = $pdo->prepare($sql);

			$stmt->bindParam('product_code', $pid);
			$stmt->bindParam('product_name',$pProductName);

			$stmt->execute();

		}catch (PDOException $e)
		{
			print('Connection failed:'.$e->getMessage());
			die();
		}
	}

	/*
	*
	* update 1 record
	*
	* $pProductName ... Product code for target / Integer
	* $pProductName ... Product name for register / String
	*
	*/
	function updateData($pProductCode, $pProductName) {
		try{
			$pdo = new PDO(HOST.DBNAME, USER, PASS,array(PDO::ATTR_EMULATE_PREPARES => false,PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET `utf8`"));

			$sql = "UPDATE t_product SET ";
			$sql.= "product_name = :product_name ";
			$sql.= "WHERE ";
			$sql.= "product_code = :product_code ";

			$stmt = $pdo->prepare($sql);

			$stmt->bindParam('product_name',$pProductName);
			$stmt->bindParam('product_code',$pProductCode);

			$stmt->execute();

		}catch (PDOException $e){
			print('Connection failed:'.$e->getMessage());
			die();
		}
	}

	/*
	*
	* delete 1 record
	*
	* $pProductCode ... Product code for target
	*
	*/
	function deleteData($pProductCode) {
		try{
			$pdo = new PDO(HOST.DBNAME, USER, PASS,array(PDO::ATTR_EMULATE_PREPARES => false,PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET `utf8`"));

			$sql = "DELETE FROM t_product WHERE ";
			$sql.= "product_code = :product_code ";
			$stmt = $pdo->prepare($sql);
			$stmt->bindParam('product_code', $pProductCode);
			$stmt->execute();

		}catch (PDOException $e){
			print('Connection failed:'.$e->getMessage());
			die();
		}
	}
}
?>
