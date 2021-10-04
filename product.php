<?php

ini_set("display_errors", On);
error_reporting(E_ALL);

session_start();


require_once('items/define.php');
require_once(ROOT_PATH.'items/common.php');
require_once(ROOT_PATH.'items/users.php');
require_once(ROOT_PATH.'items/products.php');

if(DEBUG_MODE == True)
{
	require_once(ROOT_PATH.'items/dBug.php');
}

require_once(SMARTY_DIR . 'MySmartyClass.php');

$smarty = new MySmarty();
$commonItem = new item_common;
$productItem = new item_product;
$user = new item_user;

$commonItem->writeLog("product.php start.");

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

	$vProductCode = 0;
	$vProductName = '';

	if(count($_POST) > 0)
	{
		// something posts
		// var_dump($_POST);

		if(isset($_POST['mode']))
		{
			switch ($_POST['mode'])
			{
				case "add":
					if(isset($_POST['name']))
					{
						$vProductName = htmlspecialchars($_POST['name']);
						if($vProductName == '')
						{
							$vErrorMsg = "Input the product name";
						}
						else
						{
							// add product record
							$productItem->addData($vProductName);
							$vInfoMsg = "Add completed";
						}
					}
					break;
				case "mod":
					if(isset($_POST['code']) && isset($_POST['name']))
					{
						$vProductCode = htmlspecialchars($_POST['code']);
						$vProductName = htmlspecialchars($_POST['name']);
						if($vProductName == '')
						{
							$vErrorMsg = "Input the product name";
						}
						else
						{
							// update product record
							$productItem->updateData($vProductCode, $vProductName);
							$vInfoMsg = "Update completed";
						}
						
					}
					break;
				case "del":
					if(isset($_POST['code']))
					{
						$vProductCode = htmlspecialchars($_POST['code']);
						// delete product record
						$productItem->deleteData($vProductCode);
						$vInfoMsg = "Delete completed";
					}
				break;
			}
		}
	}

	// get all product data
	$lines = $productItem->getAllItems();
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

	$smarty->assign('page_name',"Manage the products");
	$smarty->assign('data',$lines);
	$smarty->assign('user_name',$user_name);
	$smarty->assign('err_message',$vErrorMsg);
	$smarty->assign('info_message',$vInfoMsg);
	$smarty->assign('show_logout',True);
	$smarty->display("product.tpl");
}
else
{
	header('Location: '.SERVICE_ROOT.'index.php');
	exit();
}

?>
