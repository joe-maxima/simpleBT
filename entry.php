<?php

ini_set("display_errors", On);
error_reporting(E_ALL);

session_start();

require_once('items/define.php');
require_once(ROOT_PATH.'items/common.php');
require_once(ROOT_PATH.'items/users.php');
require_once(ROOT_PATH.'items/ticket.php');

if(DEBUG_MODE == True)
{
	require_once(ROOT_PATH.'items/dBug.php');
}

// 拡張Smarty.classの呼び出し
require_once(SMARTY_DIR . 'MySmartyClass.php');

$smarty = new MySmarty();
$commonItem = new item_common;
$user = new item_user;
$ticketItem = new item_ticket;

$commonItem->writeLog("entry.php start.");

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

// log in and get the user name
$user_name = $user->GetUsername();
$mode = '';
$data = null;
$srcDiv = null;
$srcName = null;
$tmp = null;

if(isset($_POST['mode']))
{
	/*
	var_dump($_POST);
	print_r("<BR>");
	*/

	/*
	$data['mode'] = $_POST['mode'];
	$data['product'] = $_POST['product'];
	$data['target'] = $_POST['target'];
	$data['msg'] = $_POST['msg'];
	$data['priority_level'] = $_POST['priority_level'];
	$data['status'] = $_POST['status'];
	*/
	$tmp = htmlspecialchars($_POST['mode']);
	$data['mode'] = $tmp;
	$tmp = htmlspecialchars($_POST['product']);
	$data['product'] = $tmp;
	$tmp = htmlspecialchars($_POST['target']);
	$data['target'] = $tmp;
	$tmp = htmlspecialchars($_POST['msg']);
	$data['msg'] = $tmp;
	$tmp = htmlspecialchars($_POST['user_affect']);
	$data['user_affect'] = $tmp;
	$tmp = htmlspecialchars($_POST['priority_level']);
	$data['priority_level'] = $tmp;
	$tmp = htmlspecialchars($_POST['status']);
	$data['status'] = $tmp;

	
	// checking input data
	if($data['target'] == '')
	{
		$vErrorMsg .= "[Target function] has not been entered\n";
	}
	if($data['msg'] == '')
	{
		$vErrorMsg .= "[Details of request] has not been entered\n";
	}
	if(isset($_POST['src_div']))
	{
		// posted requestor
		$srcDiv = htmlspecialchars($_POST['src_div']);
		if($srcDiv == 1)
		{
			// requestor is customer
			$srcName = htmlspecialchars($_POST['src_name']);
			if($srcName == '')
			{
				$vErrorMsg .= "[Reqeuestor] has not been entered\n";
			}
		}
		else
		{
			// requestor is cowerker or unknown
			$srcName = '';
		}
		$data['src_div'] = $srcDiv;
		$data['src_name'] = $srcName;
	}
	else
	{
		// missed requestor
		$vErrorMsg .= "Select [Requestor]\n";
		$data['src_div'] = 0;
		$srcName = htmlspecialchars($_POST['src_name']);
		$data['src_name'] = $srcName;
	}

	/*
	var_dump($data);
	print_r("<BR>");
	*/

	if($vErrorMsg == '')
	{
		// print_r("No error<BR>");
		if($data['mode'] == 'add')
		{
			// print_r("add<BR>");
			$ticketItem->addData($data);
			$vInfoMsg = "Input data has been added\n";
			$data = null;

		} elseif ($data['mode'] == 'edit')
		{
			// edit
			// I was going to make one, but it's no longer needed.
		}
	}
}

if( (isset($_GET['pid'])) && (isset($_GET['tid'])) ) 
{
	// product_id and ticket_id in query strings (for update)
	$mode = 'edit';
	$page_name = 'Edit data';
} else 
{
	// product_id and ticket_id NOT in query string(for add)
	$mode = 'add';
	$page_name = 'Add data';
}

// get the master data
// product
$product_items = $commonItem->getAllProducts();
// priority
$priority_items = $commonItem->getAllPriorities();
// status
$status_items = $commonItem->getStatuses();

// var_dump($product_items);
// var_dump($data);

$smarty->assign('page_name',$page_name);
$smarty->assign('user_name',$user_name);
$smarty->assign('err_message',$vErrorMsg);
$smarty->assign('info_message',$vInfoMsg);
$smarty->assign('mode',$mode);
$smarty->assign('data',$data);
$smarty->assign('product_items',$product_items);
$smarty->assign('priority_items',$priority_items);
$smarty->assign('show_logout',True);
$smarty->assign('status_items',$status_items);

if($mode = 'add')
{
	$smarty->display("entry_add.tpl");
} else 
{
	$smarty->display("entry_edit.tpl");
}

?>
