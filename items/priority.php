<?php
// priority.php
if(defined("ROOT_PATH")){
	require_once(ROOT_PATH.'items/define.php');
} else {
	require_once('../items/define.php');
}
if(DEBUG_MODE == True){
	require_once(ROOT_PATH.'items/dBug.php');
}

class item_priority {

	/* get a specified record
	*
	* $pLevel:Priority Level / Integer
	*
	*/
	function getOne($pLevel){
		try{
			$commonItem = new item_common();

			$pdo = new PDO(HOST.DBNAME, USER, PASS,array(PDO::ATTR_EMULATE_PREPARES => false,PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET `utf8`"));

			$sql  = "SELECT * FROM m_priority WHERE level = :code ";

			$stmt = $pdo->prepare($sql);
			$stmt->bindParam('code', $pLevel);
			$stmt->execute();
			$result = $stmt->fetchAll();

			// var_dump($result);

			return $result;

		}catch (PDOException $e){
			print('Connection failed:'.$e->getMessage());
		die();
		}
	}

}
?>
