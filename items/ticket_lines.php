<?php
// ticket_lines.php
if(defined("ROOT_PATH")){
	require_once(ROOT_PATH.'items/define.php');
} else {
	require_once('items/define.php');
}
require_once(ROOT_PATH.'items/common.php');

if(DEBUG_MODE == True){
	require_once(ROOT_PATH.'items/dBug.php');
}

class item_ticket_lines {

	/*
	*
	* add 1 record
	*
	* $pProductCode ... Target product code for ticket
	* $pTicketID ... Target ticket ID for ticket
	* $pLineNo ... Target line no
	* $pComment ... Comment messages
	*
	* Notice: Required user information for table 't_ticket_lines', it get from the session variables.
	*
	*/
	function addData($pProductCode,$pTicketID,$pLineNo,$pComment) {
		try{
			$commonItem = new item_common();

			$pdo = new PDO(HOST.DBNAME, USER, PASS,array(PDO::ATTR_EMULATE_PREPARES => false,PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET `utf8`"));

			$sql = "INSERT INTO t_ticket_lines ";
			$sql.= "( product_code,ticket_id,lines_no,comment,last_update,update_user ) ";
			$sql.= " VALUES ";
			$sql.= "( :product_code,:ticket_id,:lines_no,:comment,NOW(),:update_user ) ";

			$stmt = $pdo->prepare($sql);

			/*
			print_r("SQL=".$sql."<BR>");
			print_r("product_code=".$pProductCode."<BR>");
			print_r("ticket_id=".$pTicketID."<BR>");
			print_r("lines_no=".$pLineNo."<BR>");
			print_r("comment=".$pComment."<BR>");
			*/

			$stmt->bindParam('product_code', $pProductCode);
			$stmt->bindParam('ticket_id',$pTicketID);
			$stmt->bindParam('lines_no',$pLineNo);
			$stmt->bindParam('comment',$pComment);
			$stmt->bindParam('update_user', $_SESSION['user_code']);

			$stmt->execute();

		}catch (PDOException $e){
			print('Connection failed:'.$e->getMessage());
			die();
		}
	}

	/*
	*
	* get the maximum number
	*
	* $pProductCode ... Target product code for ticket
	* $pTicketID ... Target ticket ID for ticket
	*
	*/
	function getMaxLineNo($pProductCode,$pTicketID) {
		try{
			$commonItem = new item_common();

			$pdo = new PDO(HOST.DBNAME, USER, PASS,array(PDO::ATTR_EMULATE_PREPARES => false,PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET `utf8`"));

			$sql  = "SELECT MAX(lines_no) AS maxno FROM t_ticket_lines WHERE ";
			$sql .= "product_code = :code AND ";
			$sql .= "ticket_id = :id ";

			$stmt = $pdo->prepare($sql);
			$stmt->bindParam('code', $pProductCode);
			$stmt->bindParam('id', $pTicketID);
			$stmt->execute();
            $result = $stmt->fetchColumn(0);

			if(is_null($result)) {
				$result = 0;
			}

			// var_dump($result);

			return $result;

		}catch (PDOException $e){
			print('Connection failed:'.$e->getMessage());
			die();
		}
	}

	/*
	*
	* get the specified 1 record
	*
	* $pProductCode ... Target product code for ticket
	* $pTicketID ... Target ticket ID for ticket
	* $pLinesNo ... Target line no for ticket lines
	* 
	*/
	function getOne($pProductCode,$pTicketID,$pLinesNo) {
		try{

			/*
			print_r("pProductCode=".$pProductCode."<BR>");
			print_r("pTicketID=".$pTicketID."<BR>");
			print_r("pLinesNo=".$pLinesNo."<BR>");
			*/

			$pdo = new PDO(HOST.DBNAME, USER, PASS,array(PDO::ATTR_EMULATE_PREPARES => false,PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET `utf8`"));

			$sql = "SELECT * FROM t_ticket_lines ";
			$sql.= "WHERE ";
			$sql.= "product_code = :pcode AND ";
			$sql.= "ticket_id = :tid AND ";
			$sql.= "lines_no = :no ";

			$stmt = $pdo->prepare($sql);
			
			$vProductCode = htmlspecialchars($pProductCode);
			$vTickedID = htmlspecialchars($pTicketID);
			$vLinesNo = htmlspecialchars($pLinesNo);

			$stmt->bindParam("pcode", $vProductCode);
			$stmt->bindParam("tid", $vTickedID);
			$stmt->bindParam("no",$vLinesNo);

			$stmt->execute();
            $result = $stmt->fetchAll();

			return $result;

		}catch (PDOException $e){
			print('Connection failed:'.$e->getMessage());
			die();
		}
	}

}
?>
