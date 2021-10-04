<?php
// ticket_authority.php
if(defined("ROOT_PATH")){
	require_once(ROOT_PATH.'items/define.php');
} else {
	require_once('items/define.php');
}
require_once(ROOT_PATH.'items/common.php');

if(DEBUG_MODE == True){
	require_once(ROOT_PATH.'items/dBug.php');
}

class item_ticket_authority {

	/*
	*
	* add 1 record
	*
	* $pProductCode ... Target product code for ticket
	* $pTicketID ... Target ticket ID for ticket
	*
	* Notice: Required user information for table 't_ticket_authority', it get from the session variables.
	*/
	function addData($pProductCode,$pTicketID) {
		try{
			$pdo = new PDO(HOST.DBNAME, USER, PASS,array(PDO::ATTR_EMULATE_PREPARES => false,PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET `utf8`"));

			$sql = "INSERT INTO t_ticket_authority ";
			$sql.= "( product_code,ticket_id,user_code,update_datetime ) ";
			$sql.= " VALUES ";
			$sql.= "( :product_code,:ticket_id,:user_code,NOW() ) ";

			$stmt = $pdo->prepare($sql);

			$stmt->bindParam('product_code', $pProductCode);
			$stmt->bindParam('ticket_id',$pTicketID);
			$stmt->bindParam('user_code', $_SESSION['user_code']);

			$stmt->execute();

		}catch (PDOException $e){
			print('Connection failed:'.$e->getMessage());
			die();
		}
	}

	/*
	*
	* get recently authority information
	*
	* $pProductCode ... Target product code for ticket
	* $pTicketID ... Target ticket ID for ticket
	*
	*/
	function getAuthorities($pProductCode,$pTicketID) {
		try{
			$commonItem = new item_common();

			$pdo = new PDO(HOST.DBNAME, USER, PASS,array(PDO::ATTR_EMULATE_PREPARES => false,PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET `utf8`"));

			$sql  = "SELECT ";
			$sql .= "t_ticket_authority.update_datetime, ";
			$sql .= "t_user.user_code, ";
			$sql .= "t_user.user_name ";
			$sql .= "FROM ";
			$sql .= "t_ticket_authority,t_user ";
			$sql .= "WHERE ";
			$sql .= "t_ticket_authority.product_code = :code AND ";
			$sql .= "t_ticket_authority.ticket_id = :id AND ";
			$sql .= "t_ticket_authority.user_code = t_user.user_code AND ";
			$sql .= "t_user.authority = 1 ";

			$stmt = $pdo->prepare($sql);
			$stmt->bindParam('code', $pProductCode);
			$stmt->bindParam('id', $pTicketID);
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
	* get the status of whether or not ticket approve.
	*
	* $pProductCode ... Target product code for ticket
	* $pTicketID ... Target ticket ID for ticket
	*
	* Notice: Required user information for table 't_ticket_authority', it get from the session variables.
	*
	*/
	function getISNeedAuthority($pProductCode,$pTicketID) {
		try{
			$commonItem = new item_common();

			if($_SESSION['authority'] == 0) {
				// User does not have approval rights
				// print_r("User does not have approval rights<BR>");
				return False;
			} else {
				// The user has approval rights
				// print_r("The user has approval rights<BR>");
				$pdo = new PDO(HOST.DBNAME, USER, PASS,array(PDO::ATTR_EMULATE_PREPARES => false,PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET `utf8`"));

				$sql  = "SELECT COUNT(*) AS cnt FROM t_ticket_authority WHERE ";
				$sql .= "product_code = :code AND ";
				$sql .= "ticket_id = :id AND ";
				$sql .= "user_code = :user_code ";

				$stmt = $pdo->prepare($sql);
				$stmt->bindParam('code', $pProductCode);
				$stmt->bindParam('id', $pTicketID);
				$stmt->bindParam('user_code', $_SESSION['user_code']);
				$stmt->execute();
				$result = $stmt->fetchColumn(0);

				if($result > 0) {
					// approved
					return False;
				} else {
					// not approved
					return True;
				}
			}
		}catch (PDOException $e){
			print('Connection failed:'.$e->getMessage());
			die();
		}
	}

	/*
	*
	* get the number of people who approve
	*
	* $pProductCode ... Target product code for ticket
	* $pTicketID ... Target ticket ID for ticket
	*
	*/
	function getAuthorityStatusMessage($pProductCode,$pTicketID) {
		try{
			$commonItem = new item_common();

			$pdo = new PDO(HOST.DBNAME, USER, PASS,array(PDO::ATTR_EMULATE_PREPARES => false,PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET `utf8`"));

			// get the number of people who approve.
			$sql  = "SELECT COUNT(*) AS cnt ";
			$sql .= "FROM ";
			$sql .= "t_ticket_authority,t_user ";
			$sql .= "WHERE ";
			$sql .= "t_ticket_authority.product_code = :code AND ";
			$sql .= "t_ticket_authority.ticket_id = :id AND ";
			$sql .= "t_ticket_authority.user_code = t_user.user_code AND ";
			$sql .= "t_user.authority = 1 ";

			$stmt = $pdo->prepare($sql);
			$stmt->bindParam('code', $pProductCode);
			$stmt->bindParam('id', $pTicketID);
			$stmt->execute();
			$result['authorized_count'] = $stmt->fetchColumn(0);

			// get the number of people with approval authority
			$sql  = "SELECT COUNT(*) AS cnt FROM t_user WHERE authority = 1";

			$stmt = $pdo->prepare($sql);
			$stmt->execute();
			$result['authorize_count'] = $stmt->fetchColumn(0);

			return $result;

		}catch (PDOException $e){
			print('Connection failed:'.$e->getMessage());
			die();
		}
	}

}
?>
