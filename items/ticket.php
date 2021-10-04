<?php
// tickets.php
if(defined("ROOT_PATH")){
	require_once(ROOT_PATH.'items/define.php');
} else {
	require_once('items/define.php');
}
require_once(ROOT_PATH.'items/common.php');
require_once(ROOT_PATH.'items/ticket_status.php');

if(DEBUG_MODE == True){
	require_once(ROOT_PATH.'items/dBug.php');
}

class item_ticket {

	/*
	*
	* get ticket list for index.php
	*
	* $pIsShowAll ... show hidden data(True:show False:hidden) / Boolean
	* $pStatus ... Filter data by status code(Default:null = no filtered) / Integer
	* 
	*/
	function getListLine($pIsShowAll,$pStatus = null) {
		try{

			$commonItem = new item_common();

			if(DEBUG_MODE == True){
				error_log(">>> getListLine start.\n",3,$commonItem->getLogPath());
			}
			
			$pdo = new PDO(HOST.DBNAME, USER, PASS,array(PDO::ATTR_EMULATE_PREPARES => false,PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET `utf8`"));

			$sql  = "SELECT ";
			$sql .= "t_ticket.*, ";
			$sql .= "t_product.product_name, ";
			$sql .= "t_user.user_name AS request_user_name, ";
			$sql .= "m_priority.level_name AS priority_level_name, ";
			$sql .= "l.lastno, ";
			$sql .= "lts.last_ticket, ";
			$sql .= "t_ticket_status.status, ";
			$sql .= "m_status.status_name AS status_name, ";
			$sql .= "m_status.color_name AS status_color ";
			$sql .= "FROM ";
			$sql .= "t_product,t_user,m_priority, ";
			$sql .= "t_ticket_status, m_status, ";
			$sql .= "(SELECT product_code,ticket_id,MAX(update_datetime) AS last_ticket FROM t_ticket_status GROUP BY product_code,ticket_id) AS lts, ";
			$sql .= "t_ticket  ";
			// $sql .= "LEFT JOIN (SELECT product_code,ticket_id,MAX(last_update) AS lastupdate FROM t_ticket_lines GROUP BY product_code,ticket_id) AS l ";
			$sql .= "LEFT JOIN (SELECT product_code,ticket_id,MAX(lines_no) AS lastno FROM t_ticket_lines GROUP BY product_code,ticket_id) AS l ";
			$sql .= "ON  ";
			$sql .= "t_ticket.product_code = l.product_code AND ";
			$sql .= "t_ticket.ticket_id = l.ticket_id ";
			$sql .= "WHERE ";
			if($pIsShowAll == False) {
				$sql .= "t_ticket.is_show = 1 AND ";
			}
			$sql .= "t_ticket.product_code = t_product.product_code AND ";
			$sql .= "t_ticket.request_user = t_user.user_code AND ";
			$sql .= "t_ticket.priority_level = m_priority.level AND ";
			$sql .= "t_ticket_status.product_code = t_ticket.product_code AND ";
			$sql .= "t_ticket_status.ticket_id = t_ticket.ticket_id AND ";
			$sql .= "t_ticket_status.product_code = lts.product_code AND ";
			$sql .= "t_ticket_status.ticket_id = lts.ticket_id AND ";
			$sql .= "t_ticket_status.update_datetime = lts.last_ticket AND ";
			$sql .= "m_status.status_id = t_ticket_status.status ";
			if($pStatus != null) {
				$sql .= "AND t_ticket_status.status = " . $pStatus . " ";
			}
			$sql .= "ORDER BY ";
			// $sql .= "l.lastupdate DESC, ";
			$sql .= "t_ticket.request_date DESC, ";
			$sql .= "t_ticket.ticket_id DESC ";

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
	* get the maximum ticked id
	*
	* $pdo ... DB access object
	* $pProductCode ... Target product code for ticket
	*
	*/
	function getMaxID($pdo,$pProductCode) {
		try{
			$sql = "SELECT MAX(ticket_id) AS maxid FROM t_ticket ";
			$sql.= "WHERE product_code = :product_code";
			
			$stmt = $pdo->prepare($sql);

			$stmt->bindParam('product_code', $pProductCode);

			$stmt->execute();
			$maxid = $stmt->fetchColumn();

			if($maxid > 0) {
				return $maxid;
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
	* get the spcified 1 record
	*
	* $pProductCode ... Target product code for ticket
	* $pTicketID ... Target ticket ID for ticket
	*
	*/
	function getOne($pProductCode,$pTicketID) {
		try{

			/*
			print_r("pProductCode=".$pProductCode."<BR>");
			print_r("pTicketID=".$pTicketID."<BR>");
			*/

			$pdo = new PDO(HOST.DBNAME, USER, PASS,array(PDO::ATTR_EMULATE_PREPARES => false,PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET `utf8`"));

			$sql = "SELECT * FROM t_ticket ";
			$sql.= "WHERE ";
			$sql.= "product_code = :pcode AND ";
			$sql.= "ticket_id = :tid ";

			$stmt = $pdo->prepare($sql);
			
			$vProductCode = htmlspecialchars($pProductCode);
			$vTickedID = htmlspecialchars($pTicketID);

			$stmt->bindParam("pcode", $vProductCode);
			$stmt->bindParam("tid", $vTickedID);

			$stmt->execute();
			$result = $stmt->fetchAll();

			return $result;

		}catch (PDOException $e){
			print('Connection failed:'.$e->getMessage());
			die();
		}
	}

	/*
	*
	* get whether the specified record exists
	*
	* $pProductCode ... Target product code for ticket
	* $pTicketID ... Target ticket ID for ticket
	*
	*/
	function getIsExist($pProductCode,$pTicketID) {
		try{

			$pdo = new PDO(HOST.DBNAME, USER, PASS,array(PDO::ATTR_EMULATE_PREPARES => false,PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET `utf8`"));

			$sql = "SELECT COUNT(*) AS cnt FROM t_ticket ";
			$sql.= "WHERE ";
			$sql.= "product_code = :pcode AND ";
			$sql.= "ticket_id = :tid ";

			$stmt = $pdo->prepare($sql);
			
			$vProductCode = htmlspecialchars($pProductCode);
			$vTicketID = htmlspecialchars($pTicketID);

			$stmt->bindParam(":pcode", $vProductCode);
			$stmt->bindParam(":tid", $vTicketID);

			$stmt->execute();
			$result = $stmt->fetchColumn(0);

			if($result == 0) {
				return False;
			} else {
				return True;
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
	* $pData ... ticket data for register
	*
	*/
	function addData($pData) {
		try{
			$tsItem = new item_ticket_status;

			$pdo = new PDO(HOST.DBNAME, USER, PASS,array(PDO::ATTR_EMULATE_PREPARES => false,PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET `utf8`"));
			$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$sql = "INSERT INTO t_ticket ";
			$sql.= "( product_code,ticket_id,request_date,request_user,target,ticket_msg,priority_level,is_show,src_div,src_name,user_affect )";
			$sql.= " VALUES ";
			$sql.= "( :product_code,:ticket_id,NOW(),:request_user,:target,:ticket_msg,:priority_level,1,:src_div,:src_name,:user_affect )";

			/*
			print_r("pData=<BR>");
			var_dump($pData);
			print_r("<BR>product=".$pData['product']);
			*/

			// begin transaction
			$pdo->beginTransaction();

			try {
				// get the maximum ticket id and increment
				$tid = ( $this->getMaxID($pdo,$pData['product']) + 1);

				$stmt = $pdo->prepare($sql);

				/*
				var_dump($_SESSION);
				*/

				$stmt->bindParam('product_code', $pData['product']);
				$stmt->bindParam('ticket_id',$tid);
				$stmt->bindParam('request_user', $_SESSION['user_code']);
				$stmt->bindParam('target',$pData['target']);
				$stmt->bindParam('ticket_msg',$pData['msg']);
				$stmt->bindParam('priority_level',$pData['priority_level']);
				// $stmt->bindParam('is_show', 1 );
				$stmt->bindParam('src_div',$pData['src_div']);
				$stmt->bindParam('src_name',$pData['src_name']);
				$stmt->bindParam('user_affect',$pData['user_affect']);
				
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

				// add 1 ticket status record
				$tsItem->addData($pdo,$pData['product'],$tid,$pData['status']);
				
				// transaction commit
				$pdo->commit();

				/*
				print_r("commited. pdo error=");
				var_dump($pdo->errorInfo());
				*/

			}catch(PDOException $e) {
				// transaction rollback
				$pdo->rollback();
				
				throw $e;
			}
		}catch (PDOException $e) {
			print('Connection failed:'.$e->getMessage());
			die();
		}
	}

	/*
	*
	* update 1 record
	*
	* $pData ... ticket data for register
	*
	*/
	function updateData($pData) {
		try{
			$tsItem = new item_ticket_status;

			$pdo = new PDO(HOST.DBNAME, USER, PASS,array(PDO::ATTR_EMULATE_PREPARES => false,PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET `utf8`"));

			$sql = "UPDATE t_ticket SET ";
			$sql.= "target = :target,";
			$sql.= "ticket_mdg = :ticket_mdg,";
			$sql.= "priority_level = :priority_level ";
			$sql.= "WHERE ";
			$sql.= "product_code = :product_code AND ";
			$sql.= "ticket_id = :ticket_id ";

			$stmt = $pdo->prepare($sql);

			$stmt->bindParam('target',$pData['target']);
			$stmt->bindParam('ticket_mdg',$pData['msg']);
			$stmt->bindParam('priority_level',$pData['priority_level']);
			$stmt->bindParam('product_code', $pData['product']);
			$stmt->bindParam('ticket_id',$pData['ticket_id']);

			$stmt->execute();

		}catch (PDOException $e){
			print('Connection failed:'.$e->getMessage());
			die();
		}
	}

	/*
	*
	* update ticket profile
	*
	* $pPID ... Target product code for ticket
	* $pTID ... Target ticket ID for ticket
	* $pSrcDiv ... category of requestor(1=customer 2=cowerker)
	* $pSrcName ... requestor's name
	* $pDataTarget ... target details of issue(ex.function name)
	* $pMsg ... notes
	* $pPriorityLevel ... priority level
	* $pUserAffect ... details of user affect
	*
	*/
	function updateProfile($pPID, $pTID, $pSrcDiv, $pSrcName, $pDataTarget, $pMsg, $pPriorityLevel, $pUserAffect ) {
		try{
			$pdo = new PDO(HOST.DBNAME, USER, PASS,array(PDO::ATTR_EMULATE_PREPARES => false,PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET `utf8`"));

			$sql = "UPDATE t_ticket SET ";
			$sql.= "src_div = :src_div,";
			$sql.= "src_name = :src_name,";
			$sql.= "target = :target,";
			$sql.= "ticket_msg = :ticket_msg,";
			$sql.= "priority_level = :priority_level, ";
			$sql.= "user_affect = :user_affect ";
			$sql.= "WHERE ";
			$sql.= "product_code = :product_code AND ";
			$sql.= "ticket_id = :ticket_id ";

			$stmt = $pdo->prepare($sql);

			if($pSrcDiv == 2) {
				// if category of requestor(cowerker), set null to src_name(requestor's name)
				$pSrcName = null;
			}

			/*
			print_r("pid=$pPID<BR>");
			print_r("vSrcDiv=$pTID<BR>");
			print_r("srcdiv=$pSrcDiv<BR>");
			print_r("vSrcName=$pSrcName<br>");
			print_r("vDataTarget=$pDataTarget<BR>");
			print_r("vMsg=$pMsg<BR>");
			print_r("vPriorityLevel=$pPriorityLevel<BR>");
			*/

			$stmt->bindParam('src_div',$pSrcDiv);
			$stmt->bindParam('src_name',$pSrcName);
			$stmt->bindParam('target',$pDataTarget);
			$stmt->bindParam('ticket_msg',$pMsg);
			$stmt->bindParam('priority_level',$pPriorityLevel);
			$stmt->bindParam('user_affect',$pUserAffect);
			$stmt->bindParam('product_code', $pPID);
			$stmt->bindParam('ticket_id',$pTID);

			$stmt->execute();

		}catch (PDOException $e){
			print('Connection failed:'.$e->getMessage());
			die();
		}
	}

	/*
	*
	* delete specified 1 record
	*
	* $pProductCode ... Target product code for ticket
	* $pTicketID ... Target ticket ID for ticket
	*
	*/
	function deleteData($pProductCode,$pTicketID) {
		try{
			$tsItem = new item_ticket_status;

			//DB接続
			$pdo = new PDO(HOST.DBNAME, USER, PASS,array(PDO::ATTR_EMULATE_PREPARES => false,PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET `utf8`"));

			// t_ticket
			$sql = "DELETE FROM t_ticket WHERE ";
			$sql.= "product_code = :product_code AND ";
			$sql.= "ticket_id = :ticket_id ";
			$stmt = $pdo->prepare($sql);
			$stmt->bindParam('product_code', $pProductCode);
			$stmt->bindParam('ticket_id',$pTicketID);
			$stmt->execute();

			// t_ticket_lines
			$sql = "DELETE FROM t_ticket_lines WHERE ";
			$sql.= "product_code = :product_code AND ";
			$sql.= "ticket_id = :ticket_id ";
			$stmt_lines = $pdo->prepare($sql);
			$stmt_lines->bindParam('product_code', $pProductCode);
			$stmt_lines->bindParam('ticket_id',$pTicketID);
			$stmt_lines->execute();

			// t_ticket_status
			$sql = "DELETE FROM t_ticket_status WHERE ";
			$sql.= "product_code = :product_code AND ";
			$sql.= "ticket_id = :ticket_id ";
			$stmt_status = $pdo->prepare($sql);
			$stmt_status->bindParam('product_code', $pProductCode);
			$stmt_status->bindParam('ticket_id',$pTicketID);
			$stmt_status->execute();

		}catch (PDOException $e){
			print('Connection failed:'.$e->getMessage());
			die();
		}
	}

	/*
	*
	* get the ticket details (for detail.php)
	*
	* $pProductCode ... Target product code for ticket
	* $pTicketID ... Target ticket ID for ticket
	*
	*/
	function getDetailRows($pProductCode, $pTicketID) {
		try{

			$commonItem = new item_common();

			if(DEBUG_MODE == True){
				error_log(">>> getDetailRows start.\n",3,$commonItem->getLogPath());
			}

			$pdo = new PDO(HOST.DBNAME, USER, PASS,array(PDO::ATTR_EMULATE_PREPARES => false,PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET `utf8`"));

			$sql  = "SELECT ";
			$sql .= "1 AS data_div, ";
			$sql .= "NULL AS status, ";
			$sql .= "last_update AS update_datetime, ";
			$sql .= "update_user, ";
			$sql .= "lines_no, ";
			$sql .= "comment ";
			$sql .= "FROM ";
			$sql .= "t_ticket_lines ";
			$sql .= "WHERE ";
			$sql .= "product_code = :pIDline AND ticket_id = :tIDline ";
			$sql .= "UNION ";
			$sql .= "SELECT ";
			$sql .= "0 AS data_div, ";
			$sql .= "status, ";
			$sql .= "update_datetime, ";
			$sql .= "update_user, ";
			$sql .= "NULL AS lines_no, ";
			$sql .= "NULL AS comment ";
			$sql .= "FROM ";
			$sql .= "t_ticket_status ";
			$sql .= "WHERE ";
			$sql .= "product_code = :pIDstat AND ticket_id = :tIDstat ";
			$sql .= "ORDER BY update_datetime,data_div ";

            $stmt = $pdo->prepare($sql);
			$stmt->bindParam('pIDline', $pProductCode);
			$stmt->bindParam('tIDline', $pTicketID);
			$stmt->bindParam('pIDstat', $pProductCode);
			$stmt->bindParam('tIDstat', $pTicketID);
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
	* switch visiblity
	*
	* $pProductCode ... Target product code for ticket
	* $pTicketID ... Target ticket ID for ticket
	* $pIsShow ... visible status (1=show 0=hidden)
	*
	*/
	function modifyShowStatus($pProductCode, $pTicketID, $pIsShow) {
		try{
			$pdo = new PDO(HOST.DBNAME, USER, PASS,array(PDO::ATTR_EMULATE_PREPARES => false,PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET `utf8`"));
			$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$sql  = "UPDATE t_ticket SET is_show = :is_show ";
			$sql .= "WHERE product_code = :product_code AND ";
			$sql .= "ticket_id = :ticket_id ";

			$stmt = $pdo->prepare($sql);

			$stmt->bindParam('is_show', $pIsShow);
			$stmt->bindParam('product_code', $pProductCode);
			$stmt->bindParam('ticket_id',$pTicketID);

			$stmt->execute();

		}catch (PDOException $e)
		{
			print('Connection failed:'.$e->getMessage());
			die();
		}
	}

	/*
	* get the existance of hidden tickets
	*/
	function getHasHiddenItem(){
		try{
			$pdo = new PDO(HOST.DBNAME, USER, PASS,array(PDO::ATTR_EMULATE_PREPARES => false,PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET `utf8`"));

			$sql = "SELECT COUNT(*) AS cnt FROM t_ticket ";
			$sql.= "WHERE is_show = 0 ";

			$stmt = $pdo->prepare($sql);
			
			$stmt->execute();
            $result = $stmt->fetchColumn(0);

			if($result == 0) {
				return False;
			} else {
				return True;
			}

		}catch (PDOException $e){
			print('Connection failed:'.$e->getMessage());
			die();
		}
	}
}
?>
