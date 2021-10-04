<?php
error_reporting(E_ALL);
ini_set("display_errors", On);

session_start();

require_once('items/define.php');
require_once(ROOT_PATH.'items/common.php');
require_once(ROOT_PATH.'items/users.php');

if(DEBUG_MODE == True)
{
	require_once(ROOT_PATH.'items/dBug.php');
}

require_once(SMARTY_DIR . 'MySmartyClass.php');

$smarty = new MySmarty();
$commonItem = new item_common;
$user = new item_user;

$commonItem->writeLog("user.php start.");

$vErrorMsg = '';
$vInfoMsg = '';

// --- debug
if(DEBUG_MODE == True)
{
	print_r("requests:<BR>");
	new dBug($_REQUEST);
	print_r("<BR>");
	if(isset($_SESSION))
	{
		print_r("session:<BR>");
		new dBug($_SESSION);
		print_r("<BR>");
	}
}
// --- debug

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

// var_dump($_SESSION);

$lines = null;

if($vErrorMsg == '')
{
	// get the user name
	$user_name = $user->GetUsername();

	// print_r("user_name=".$user_name);

	$vUserCode = 0;
	$vUserID = '';
	$vUserPW = '';
	$vUserName = '';
	$vUserAuth = 0;
	$vMode = '';

	if(count($_POST) > 0)
	{
		// something posted
		// var_dump($_POST);

		if(isset($_POST['mode']))
		{
			$vMode = htmlspecialchars($_POST['mode']);

			if(isset($_POST['code']))
			{
				$vUserCode = htmlspecialchars($_POST['code']);
			}

			if(($vMode == 'add') || ($vMode == 'mod'))
			{
				// Checking input data
				$vUserID = htmlspecialchars($_POST['id']);
				$vUserPW = htmlspecialchars($_POST['pw']);
				$vUserName = htmlspecialchars($_POST['name']);
				if(isset($_POST['auth']))
				{
					$vUserAuth = 1;
				}
				else
				{
					$vUserAuth = 0;
				}

				if($vUserID == '')
				{
					$vErrorMsg .= "Input User ID\n";
				}
				if($vUserPW == '')
				{
					$vErrorMsg .= "Input Password\n";
				}
				if($vUserName == '')
				{
					$vErrorMsg .= "Input User name\n";
				}
				if($vErrorMsg == '')
				{
					$vIsDupulicatable = $user->getIsDuplicatable($vUserCode,$vUserID);
					if($vIsDupulicatable == True)
					{
						// user ID has been exists
						$vErrorMsg .= "This user ID has been exists\n";
					}
				}
			}

			if($vErrorMsg == '')
			{
				switch($vMode)
				{
					case "add":
						$user->addData($vUserID,$vUserPW,$vUserName,$vUserAuth);
						$vInfoMsg = "Add completed";
						break;
					case "mod":
						$user->updateData($vUserCode,$vUserID,$vUserPW,$vUserName,$vUserAuth);
						$vInfoMsg = "Update completed";
						break;
					case "del":
						$user->deleteData($vUserCode);
						$vInfoMsg = "Delete completed";
						break;
					default:
						print_r("undefined mode.");
						break;
				}
			}
		}
	}

	// get all data
	$lines = $user->getAllItems();
}

// var_dump($lines);

if(isset($_SESSION['user_name'])){
	$user_name = $_SESSION['user_name'];
	$isShowLogoutForm = True;
} else {
	$user_name = '';
	$isShowLogoutForm = False;
}

if($vIsLogin == True){
	/*
	var_dump($lines);
	*/

	$smarty->assign('page_name',"Manage users");
	$smarty->assign('data',$lines);
	$smarty->assign('user_name',$user_name);
	$smarty->assign('err_message',$vErrorMsg);
	$smarty->assign('info_message',$vInfoMsg);
	$smarty->assign('show_logout',True);
	$smarty->display("user.tpl");
}
else
{
	header('Location: '.SERVICE_ROOT.'index.php');
	exit();
}

?>
