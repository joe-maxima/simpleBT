<?php
// status.php
if(defined("ROOT_PATH")){
	require_once(ROOT_PATH.'items/define.php');
} else {
	require_once('items/define.php');
}
require_once(ROOT_PATH.'items/common.php');

if(DEBUG_MODE == True){
	require_once(ROOT_PATH.'items/dBug.php');
}

class item_status {

	/* get status name from ID
	*
	* $pID ... Target status ID / Integer
	*
	*/
	function GetStatusNameByID($pID) 
	{
		try{
			$commonItem = new item_common();

			$pdo = new PDO(HOST.DBNAME, USER, PASS,array(PDO::ATTR_EMULATE_PREPARES => false,PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET `utf8`"));

			$sql  = "SELECT status_name FROM m_status WHERE status_id = :id ";

			$stmt = $pdo->prepare($sql);
			$stmt->bindParam('id', $pID);
			$stmt->execute();
            $result = $stmt->fetchColumn(0);

			// var_dump($result);

			return $result;

		}catch (PDOException $e){
			print('Connection failed:'.$e->getMessage());
			die();
		}
	}

	/* add 1 status record for a ticket
	*
	* $pPid ... Target product code for ticket
	* $pTid ... Target ticket ID
	* $pStatus ... Status code for register
	*
	*/
	function addData($pPid,$pTid,$pStatus) 
	{
		try{
			$commonItem = new item_common();

			$pdo = new PDO(HOST.DBNAME, USER, PASS,array(PDO::ATTR_EMULATE_PREPARES => false,PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET `utf8`"));

			$sql  = "INSERT INTO t_ticket_status ( ";
			$sql .= "product_code,ticket_id,status,update_datetime,update_user ";
			$sql .= ") VALUES ( ";
			$sql .= ":product_code,:ticket_id,:status,now(),:update_user ";
			$sql .= ") ";

			$stmt = $pdo->prepare($sql);
			$stmt->bindParam('product_code', $pPid);
			$stmt->bindParam('ticket_id', $pTid);
			$stmt->bindParam('status', $pStatus);
			$stmt->bindParam('update_user', $_SESSION['user_code']);
			$stmt->execute();

		}catch (PDOException $e){
			print('Connection failed:'.$e->getMessage());
			die();
		}
	}

}
?>
