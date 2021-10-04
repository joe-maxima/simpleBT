<?php
// ticket_status.php
if(defined("ROOT_PATH")){
	require_once(ROOT_PATH.'items/define.php');
} else {
	require_once('items/define.php');
}
require_once(ROOT_PATH.'items/common.php');

if(DEBUG_MODE == True){
	require_once(ROOT_PATH.'items/dBug.php');
}

class item_ticket_status {

	/*
	*
	* add 1 record
	*
	* $pdo ... DB access object
	* $pProductCode ... Target product code for ticket
	* $pTicketID ... Target ticket ID for ticket
	* $pStatus ... Status code for register
	*
	* Notice: Required user information for table 't_ticket_status', it get from the session variables.
	*/
	function addData($pdo,$pProductCode,$pTicketID,$pStatus) {
		try{
			$sql = "INSERT INTO t_ticket_status ";
			$sql.= "( product_code,ticket_id,status,update_datetime,update_user ) ";
			$sql.= " VALUES ";
			$sql.= "( :product_code,:ticket_id,:status,NOW(),:update_user ) ";

			$stmt = $pdo->prepare($sql);

			$stmt->bindParam('product_code', $pProductCode);
			$stmt->bindParam('ticket_id',$pTicketID);
			$stmt->bindParam('status',$pStatus);
			$stmt->bindParam('update_user', $_SESSION['user_code']);

			/*
			print_r("stmt=");
			var_dump($stmt);
			print_r("<BR>");
			*/

			$stmt->execute();
			
			/*
			print_r("pdo error=");
			var_dump($pdo->errorInfo());
			*/

		}catch (PDOException $e){
			print('Connection failed:'.$e->getMessage());
			die();
		}
	}

	/*
	*
	* get the latest status
	*
	* $pProductCode ... Target product code for ticket
	* $pTicketID ... Target ticket ID for ticket
	*
	*/
	function getLastStatus($pProductCode,$pTicketID) {
		try{
			$commonItem = new item_common();

			$pdo = new PDO(HOST.DBNAME, USER, PASS,array(PDO::ATTR_EMULATE_PREPARES => false,PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET `utf8`"));

			$sql  = "SELECT  ";
			$sql .= "t_ticket_status.status ";
			$sql .= "FROM  ";
			$sql .= "t_ticket_status, ";
			$sql .= "( ";
			$sql .= "SELECT product_code,ticket_id,MAX(update_datetime) AS lastdatetime FROM t_ticket_status ";
			$sql .= "WHERE ";
			$sql .= "product_code = :p_product_code AND ";
			$sql .= "ticket_id = :p_ticket_id ";
			$sql .= "GROUP BY product_code,ticket_id ";
			$sql .= ") m ";
			$sql .= "WHERE ";
			$sql .= "t_ticket_status.product_code = m.product_code AND ";
			$sql .= "t_ticket_status.ticket_id = m.ticket_id AND ";
			$sql .= "t_ticket_status.update_datetime = m.lastdatetime ";

			/*
			print_r("SQL=".$sql."<BR>");
			print_r("pProductCode=".$pProductCode."<BR>");
			print_r("pTicketID=".$pTicketID."<BR>");
			*/

			$stmt = $pdo->prepare($sql);
			
			/*
			print_r("errorInfo=");
			var_dump($pdo->errorInfo());
			*/
			
			$stmt->bindParam("p_product_code", $pProductCode);
			$stmt->bindParam("p_ticket_id", $pTicketID);
			$stmt->execute();
			$result = $stmt->fetchColumn(0);

			// var_dump($result);

			return $result;

		}catch (PDOException $e){
			print('Connection failed:'.$e->getMessage());
			die();
		}
	}

}
?>
