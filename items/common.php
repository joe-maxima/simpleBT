<?php
// common.php
if(defined("ROOT_PATH")){
	require_once(ROOT_PATH.'items/define.php');
} else {
	require_once('../items/define.php');
}
if(DEBUG_MODE == True){
	require_once(ROOT_PATH.'items/dBug.php');
}

class item_common {

	// get log file path
	function getLogPath(){
		return LOG_PATH.date("Y-m-d").".log";
	}

	/* output log
	*
	* $pMessage ... log message / string
	*
	*/
	function writeLog( $pMessage ){
		$log_msg = date("Y-m-d H:i:s");
		if(isset($_SERVER['REMOTE_HOST'])){
			$log_msg .= "SV->".$_SERVER['REMOTE_HOST'];
		}
		$log_msg .= " IP->".$_SERVER['REMOTE_ADDR']."--->".$pMessage."\n";
		
		error_log( $log_msg ,3,$this->getLogPath());
	}

	/* get all record from specified table
	*
	* $tableName ... source table name / string
	* $order ... order column names / string
	*
	*/
	function getAllItems($tableName, $order){
		try{
			$commonItem = new item_common();

			if(DEBUG_MODE == True){
				error_log(">>> getAllItems start.\n",3,$commonItem->getLogPath());
				error_log("    table=".$tableName,3,$commonItem->getLogPath());
				error_log("    order=".$order,3,$commonItem->getLogPath());
			}
			
			$pdo = new PDO(HOST.DBNAME, USER, PASS,array(PDO::ATTR_EMULATE_PREPARES => false,PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET `utf8`"));

			$sql  = "SELECT * FROM ".$tableName." ORDER BY ".$order;

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

	// get all product records
	function getAllProducts(){
		$commonItem = new item_common();

		if(DEBUG_MODE == True){
			error_log(">>> getAllProducts start.\n",3,$commonItem->getLogPath());
		}

		return $this->getAllItems("t_product","product_code");
	}

	// get all priority records
	function getAllPriorities(){
		$commonItem = new item_common();

		if(DEBUG_MODE == True){
			error_log(">>> getAllPriorities start.\n",3,$commonItem->getLogPath());
		}

		return $this->getAllItems("m_priority","level");
	}

	// get all status records
	function getStatuses(){
		$commonItem = new item_common();

		if(DEBUG_MODE == True){
			error_log(">>> getStatuses start.\n",3,$commonItem->getLogPath());
		}

		return $this->getAllItems("m_status","status_id");
	}

}
?>
