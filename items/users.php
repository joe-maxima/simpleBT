<?php
// users.php
if(defined("ROOT_PATH")){
	require_once(ROOT_PATH.'items/define.php');
} else {
	require_once('items/define.php');
}
require_once(ROOT_PATH.'items/common.php');

if(DEBUG_MODE == True){
	require_once(ROOT_PATH.'items/dBug.php');
}

class item_user {

	/*
	*
	* log in / log out
	*
	*/
	function Login($pLoginID, $pLoginPW, $pIsLogin) 
	{
		try
		{

			$pLoginID = htmlspecialchars($pLoginID,ENT_QUOTES);
			$pLoginPW = htmlspecialchars($pLoginPW,ENT_QUOTES);

			$commonItem = new item_common();

			if(DEBUG_MODE == True) {
				error_log(">>> Login start.\n",3,$commonItem->getLogPath());
				error_log("pLoginID=[".$pLoginID."]\n",3,$commonItem->getLogPath());
				error_log("pLoginPW=[".$pLoginPW."]\n",3,$commonItem->getLogPath());
				error_log("pIsLogin=[".$pIsLogin."]\n",3,$commonItem->getLogPath());
			}
			
			if($pIsLogin == True) {
				// the user is trying log in
				if(DEBUG_MODE == True) {
					error_log("Login-function start.\n",3,$commonItem->getLogPath());
				}

				$pdo = new PDO(HOST.DBNAME, USER, PASS,array(PDO::ATTR_EMULATE_PREPARES => false,PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET `utf8`"));
				// $pdo = new PDO('mysql:host=' . HOST . ';dbname=' . DBNAME . ';charset=utf8', USER, PASS);

				$sql  = "SELECT * FROM t_user WHERE user_id = :user_id AND user_pw = :user_pw AND is_delete = 0 ";
				$stmt = $pdo->prepare($sql);
				
				$stmt->bindParam('user_id', $pLoginID);
				$stmt->bindParam('user_pw', $pLoginPW);

				$stmt->execute();
				$result = $stmt->fetchAll();
				
				if(count($result)>0) {

					if(DEBUG_MODE == True) {
						error_log("user_name=[".$result[0]['user_name']."]\n",3,$commonItem->getLogPath());
					}

					if(isset($_SESSION['user_id'])) {
						$pre_sess_user_id = $_SESSION['user_id'];
					} else {
						$pre_sess_user_id = '';
					}
					
					// regenerate session
					session_regenerate_id(true);
					// generate one-time ticket
					$oneticket = md5(uniqid(mt_rand(), TRUE));
					$_SESSION['ticket'] = $oneticket;
					// set user information in session variables
					$_SESSION['user_id'] = $pLoginID;
					$_SESSION['login_pw'] = $pLoginPW;
					$_SESSION['user_name'] = $result[0]['user_name'];
					$_SESSION['user_code'] = $result[0]['user_code'];
					$_SESSION['authority'] = $result[0]['authority'];
					
					// clear error message
					unset($_SESSION['err_msg']);
					
					if($pre_sess_user_id == '') {
						$commonItem->writeLog($result[0]['user_name']." Logged in.<BR>");
					}
					
					return True;
				} else {
					// the specified user is not exist
					$_SESSION['err_msg'] = "Log in ID or Password is incorrected";

					if(DEBUG_MODE == True)
					{
						error_log("<<<Login finished result=False.\n",3,$commonItem->getLogPath());
					}

					return False;
				}
			} else {
				// the user trying log out
				if(DEBUG_MODE == True) {
					error_log("Logout-function start.\n",3,$commonItem->getLogPath());
				}

				$_SESSION = array();
				// session_destroy();
				// $this->initSession();

				if(DEBUG_MODE == True) {
					error_log("<<<Login finished result=True.\n",3,$commonItem->getLogPath());
				}

				return True;
			}

		}catch (PDOException $e) {
			print('Connection failed:'.$e->getMessage());
			die();
		}
	}

	// trying log in and get user name
	function GetUsername() 
	{
		
		// set the login status to False
		$vIsLogin = False;
		$result = '';

		// var_dump($_SESSION);

		if( (isset($_SESSION['user_id'])) && (isset($_SESSION['login_pw'])) ) {
			// log in when the session exists
			$vIsLogin = $this->Login($_SESSION['user_id'],$_SESSION['login_pw'],True);
		}

		if($vIsLogin == True) {
			// login success
			return $_SESSION['user_name'];
		} else {
			// not logged in
			$this->Logout();
		}

	}

	// log out
	function Logout()
	{
		$_SESSION = array();
		$url = SERVICE_ROOT."index.php";
		$data = array(
			'mode' => 'logout'
			);
		$content = http_build_query($data);
		$options = array('http' => array(
			'method' => 'POST',
			'content' => $content
		));
		// $contents = file_get_contents($url, false, stream_context_create($options));
		session_destroy();
		header('location: '.$url);
		exit();
	}

	// get the user name from spcified code
	function GetUserNameByCode($pCode) 
	{
		try{
			$commonItem = new item_common();

			$pdo = new PDO(HOST.DBNAME, USER, PASS,array(PDO::ATTR_EMULATE_PREPARES => false,PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET `utf8`"));

			$sql  = "SELECT user_name FROM t_user WHERE user_code = :code ";

			$stmt = $pdo->prepare($sql);
			$stmt->bindParam('code', $pCode);
			$stmt->execute();
            $result = $stmt->fetchColumn(0);

			// var_dump($result);

			return $result;

		}catch (PDOException $e){
			print('Connection failed:'.$e->getMessage());
			die();
		}
	}

	/*
	*
	* get all user records
	*
	*/
	function getAllItems() {
		try{

			$commonItem = new item_common();

			if(DEBUG_MODE == True){
				error_log(">>> getAllItems start.\n",3,$commonItem->getLogPath());
			}

			$pdo = new PDO(HOST.DBNAME, USER, PASS,array(PDO::ATTR_EMULATE_PREPARES => false,PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET `utf8`"));

			$sql  = "SELECT * FROM t_user WHERE is_delete = 0 ORDER BY user_code ";

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
	* get the maximum user code
	*
	*/
	function getMaxCode($pdo) {
		try{
			$sql = "SELECT MAX(user_code) AS maxcode FROM t_user ";
			
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
	* get whether data with the same user ID exists
	* 	when $pCode == 0, it's only that exists same user ID (for add record)
	* 	when $pCode ! = 0, there is data with the same user ID, but there is a possibility that targeted for updating (for update record)
	*
	*/
	function getIsDuplicatable($pCode,$pID) {
		try{
			$pdo = new PDO(HOST.DBNAME, USER, PASS,array(PDO::ATTR_EMULATE_PREPARES => false,PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET `utf8`"));
			$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$sql = "SELECT user_code FROM t_user WHERE user_id = :user_id ";
			
			// print_r("code=".$pCode."<BR>");
			// print_r("id=".$pID."<BR>");

			$stmt = $pdo->prepare($sql);
			$stmt->bindParam('user_id', $pID);

			$stmt->execute();
			$vCode = $stmt->fetchColumn();

			if($pCode == 0) {
				// for add
				// print_r("add<BR>");
				// var_dump($vCode);

				if($vCode == False)	{
					// not duplicated data
					return False;
				} else {
					// this ID is already exists
					return True;
				}
			} else {
				// for update
				if($vCode == False) {
					// target ID is not exists
					return False;
				} else {
					// "data to be processed" or "irrelevant data"
					if($pCode == $vCode) {
						// data to be processed, return "not exists"
						return False;
					} else {
						// irrelevand data, return "already exists"
						return True;
					}
				}
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
	*/
	function addData($pID,$pPW,$pName,$pAuth) {
		try{
			$pdo = new PDO(HOST.DBNAME, USER, PASS,array(PDO::ATTR_EMULATE_PREPARES => false,PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET `utf8`"));
			$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			// get the maximum user code and increment
			$uid = ( $this->getMaxCode($pdo) + 1);

			$sql = "INSERT INTO t_user ";
			$sql.= "( user_code,user_id,user_pw,user_name,is_delete,authority )";
			$sql.= " VALUES ";
			$sql.= "( :user_code,:user_id,:user_pw,:user_name,0,:authority )";

			$stmt = $pdo->prepare($sql);

			$stmt->bindParam('user_code', $uid);
			$stmt->bindParam('user_id',$pID);
			$stmt->bindParam('user_pw',$pPW);
			$stmt->bindParam('user_name',$pName);
			$stmt->bindParam('authority',$pAuth);

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
	*/
	function updateData($pCode,$pID,$pPW,$pName,$pAuth) {
		try{
			$pdo = new PDO(HOST.DBNAME, USER, PASS,array(PDO::ATTR_EMULATE_PREPARES => false,PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET `utf8`"));

			$sql = "UPDATE t_user SET ";
			$sql.= "user_id = :user_id,";
			$sql.= "user_pw = :user_pw,";
			$sql.= "user_name = :user_name,";
			$sql.= "authority = :authority ";
			$sql.= "WHERE ";
			$sql.= "user_code = :user_code ";

			$stmt = $pdo->prepare($sql);

			$stmt->bindParam('user_id',$pID);
			$stmt->bindParam('user_pw',$pPW);
			$stmt->bindParam('user_name',$pName);
			$stmt->bindParam('authority',$pAuth);
			$stmt->bindParam('user_code', $pCode);

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
	*/
	function deleteData($pCode) {
		try{
			$pdo = new PDO(HOST.DBNAME, USER, PASS,array(PDO::ATTR_EMULATE_PREPARES => false,PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET `utf8`"));

			$sql = "UPDATE t_user SET is_delete = 1 WHERE ";
			$sql.= "user_code = :user_code ";
			$stmt = $pdo->prepare($sql);
			$stmt->bindParam('user_code', $pCode);
			$stmt->execute();

		}catch (PDOException $e){
			print('Connection failed:'.$e->getMessage());
			die();
		}
	}
}
?>
