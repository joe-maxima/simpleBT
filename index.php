<?php
ini_set("display_errors", On);
error_reporting(E_ALL);

session_start();

require_once('items/define.php');
require_once(ROOT_PATH.'items/common.php');
require_once(ROOT_PATH.'items/users.php');
require_once(ROOT_PATH.'items/ticket.php');
require_once(ROOT_PATH.'items/ticket_authority.php');
require_once(ROOT_PATH.'items/ticket_lines.php');

if(DEBUG_MODE == True){
	require_once(ROOT_PATH.'items/dBug.php');
}

require_once(SMARTY_DIR . 'MySmartyClass.php');

$smarty = new MySmarty();
$commonItem = new item_common;
$user = new item_user;
$ticketItem = new item_ticket;
$ticketAuthorityItem = new item_ticket_authority;
$ticketLineItem = new item_ticket_lines;

// $commonItem->writeLog("index.php start.");


// --- debug
if(DEBUG_MODE == True){
	print_r("requests:<BR>");
	new dBug($_REQUEST);
	print_r("<BR>");
	if(isset($_SESSION)){
		print_r("session:<BR>");
		new dBug($_SESSION);
		print_r("<BR>");
	}
}
// --- debug

// set false to log in status
$vIsLogin = False;
// clear error message
$vErrorMsg = '';
// clear list data
$lines = null;
// clear user name
$user_name = '';
// initialize status of hidden item exists
$vHasHidden = False;
// initialize status of SHOW hidden item
$vIsShowAll = False;
// initialize status of show current status only
$vStatus = null;

$vErrorMsg = '';
$vInfoMsg = '';

if(count($_SESSION) != 0){
	// session variables is exists
	if(isset($_SESSION['user_id']) AND isset($_SESSION['login_pw'])){
		// if user id and password in session, try to log in again
		$vIsLogin = $user->Login($_SESSION['user_id'],$_SESSION['login_pw'],True);

		if($vIsLogin == True){
			// log in success
			$commonItem->writeLog("login completed.");
		} else {
			// log in failed
			$vErrorMsg = "Log in again";
			$commonItem->writeLog("login incorrected.");
		}
	}
} else {
	// session variables is NOT exists
	$vErrorMsg="";
}

if($vErrorMsg == '')
{
	if(count($_POST) != 0){
		// something posted
		$commonItem->writeLog("post data exists.");
		if(DEBUG_MODE == True){
			print_r("post data exists.<BR>");
			new dBug($_POST);
			print_r("<BR>");
		}
		
		if(isset($_POST['mode'])){
			// mode data posted
			switch ($_POST['mode'])
			{
				case "hide":
				case "show":
					// show or hide the specified ticket
					$commonItem->writeLog("calling:hide.");
					if(isset($_POST['ticket_id']) && isset($_POST['product_code']))
					{
						if($_POST['mode'] == "hide")
						{
							$vIsShow = 0;
							$vInfoMsg = 'The ticket was hidden';
						}
						else
						{
							$vIsShow = 1;
							$vInfoMsg = 'The ticket was shown';
						}
						$ticketItem->modifyShowStatus($_POST['product_code'],$_POST['ticket_id'], $vIsShow);
					}
					break;
				case "login":
					// log in
					$commonItem->writeLog("calling:login.");
					if(isset($_POST['id']) && isset($_POST['pw']) ){
						// posted log in ID and Password
						$vIsLogin = $user->Login($_POST['id'],$_POST['pw'],True);

						if($vIsLogin == True){
							// log in success
							$commonItem->writeLog("login completed.");
						} else {
							// log in failed
							if(isset($_SESSION['err_msg'])){
								// set error message from session
								$vErrorMsg = $_SESSION['err_msg'];
								// clear error message in session
								unset($_SESSION['err_msg']);
							} else {
								$vErrorMsg = "User ID or Password incorrected.";
							}
							$commonItem->writeLog("login incorrected.");
						}

					} else {
						$vErrorMsg = 'User ID or Password not found.';
						$commonItem->writeLog("ID or Password not found.");
					}
					break;
				case "logout":
					$vErrorMsg = 'You are logged out';
					$commonItem->writeLog("calling:logout.");
					$_SESSION = array();
					$lines = null;
					$vIsLogin = False;
					$commonItem->writeLog("logout completed.");
					break;
			}
		}
	}
}

if(count($_GET) > 0)
{
	if(isset($_GET['show']))
	{
		if($_GET['show'] = 'all')
		{
			// switch to all show
			$vIsShowAll = True;
		}
	}

	if(isset($_GET['status']))
	{
		// filtering data by the status
		$vStatus = htmlspecialchars($_GET['status']);
	}
}

if($vIsLogin == True)
{
	// get row data
	$lines = $ticketItem->getListLine($vIsShowAll,$vStatus);
}

// var_dump($lines);

if(is_array($lines))
{
	if(count($lines) > 0)
	{
		$idx = 0;
		$tmp_authorize = '';

		// var_dump($lines);

		// preparing list data
		foreach ($lines as $row)
		{
			$tmp_authorize = $ticketAuthorityItem->getAuthorityStatusMessage($row['product_code'],$row['ticket_id']);
			$row['authorize_count'] = $tmp_authorize['authorize_count'];
			$row['authorized_count'] = $tmp_authorize['authorized_count'];
			if($row['lastno'] == null)
			{
				$tmp_line = null;
			}
			else
			{
				$tmp_line = $ticketLineItem->getOne($row['product_code'],$row['ticket_id'],$row['lastno']);
			}
			if($tmp_line == null)
			{
				$row['lastupdate'] = null;
				$row['lastcomment'] = null;
				$row['lastusercode'] = null;
			}
			else
			{
				// var_dump($tmp_line);
				$row['lastupdate'] = $tmp_line[0]['last_update'];
				$row['lastcomment'] = $user->GetUserNameByCode($tmp_line[0]['update_user']);
				$row['lastusercode'] = $tmp_line[0]['update_user'];
			}
			$lines[$idx] = $row;
			$idx++;
		}
	}
	// get the hidden items exist
	$vHasHidden = $ticketItem->getHasHiddenItem();
}
// var_dump($lines);


// var_dump($_SESSION);

if(isset($_SESSION['user_name'])){
	$user_name = $_SESSION['user_name'];
	$isShowLogoutForm = True;
} else {
	$user_name = '';
	$isShowLogoutForm = False;
}

// print_r("hashidden=".$vHasHidden);

$smarty->assign('user_name',$user_name);
$smarty->assign('show_logout',$isShowLogoutForm);
$smarty->assign('has_hidden_items',$vHasHidden);

$smarty->assign('err_message',$vErrorMsg);
$smarty->assign('info_message',$vInfoMsg);

// var_dump($lines);

if($vIsLogin == True){
	// show the list data
	$smarty->assign('page_name',"SimpleBT");
	$smarty->assign('lines',$lines);
	$smarty->assign('reloadsec',60);
	$smarty->display("main.tpl");
} else {
	$smarty->assign('page_name',"SimpleBT Log in");
	$smarty->display("index.tpl");
}

?>
