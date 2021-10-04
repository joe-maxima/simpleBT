<?php

session_start();

require_once('items/define.php');
require_once(ROOT_PATH.'items/common.php');
require_once(ROOT_PATH.'items/users.php');
require_once(ROOT_PATH.'items/ticket.php');
require_once(ROOT_PATH.'items/products.php');
require_once(ROOT_PATH.'items/priority.php');
require_once(ROOT_PATH.'items/users.php');
require_once(ROOT_PATH.'items/status.php');
require_once(ROOT_PATH.'items/ticket_status.php');
require_once(ROOT_PATH.'items/ticket_lines.php');
require_once(ROOT_PATH.'items/ticket_authority.php');

if(DEBUG_MODE == True)
{
	require_once(ROOT_PATH.'items/dBug.php');
}

// smarty extension
require_once(SMARTY_DIR . 'MySmartyClass.php');

// generate instance
$smarty = new MySmarty();
$commonItem = new item_common;
$productItem = new item_product;
$user = new item_user;
$ticketItem = new item_ticket;
$priorityItem = new item_priority;
$userItem = new item_user;
$statusItem = new item_status;
$ticketStatusItem = new item_ticket_status;
$ticketLineItem = new item_ticket_lines;
$ticketAuthorityItem = new item_ticket_authority;

// $commonItem->writeLog("detail.php start.");

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

// try to log in
$user_name = $user->GetUsername();
$data = '';

$pid = '';
$tid = '';
$outData = '';
$idx = 0;

if(count($_POST) > 0)
{
	// var_dump($_POST);

	// when something is posted
	// print_r("POST<BR>");

	if((isset($_GET['pid'])) && isset($_GET['tid']))
	{
		// when product_code and ticket_id are specified
		// print_r("pid/tid is exist<BR>");

		$pid = htmlspecialchars($_GET['pid']);
		$tid = htmlspecialchars($_GET['tid']);

		if($ticketItem->getIsExist($pid,$tid))
		{
			// when existing ticket record
			// print_r("existing ticket record<BR>");

			if(isset($_POST['target']))
			{
				// when the target is specified
				if($_POST['target'] == 'status')
				{
					// modify status
					// print_r("modify status<BR>");
					if($_POST['status'] == $ticketStatusItem->getLastStatus($pid,$tid))
					{
						// same as current status
						$vErrorMsg = 'It is not possible to update to the same "status".';
					}
					else
					{
						$vStatus = htmlspecialchars($_POST['status']);
						$statusItem->addData($pid, $tid, $vStatus);
						$vErrorMsg = 'Status updated.';
					}

				} 
				elseif( $_POST['target'] == 'line')
				{
					// post the comment
					// print_r("post the comment<BR>");
					$vMsg = htmlspecialchars($_POST['msg']);
					if($vMsg == '')
					{
						$vErrorMsg = 'You can not submit empty comment';
						
					} 
					else
					{
						$vLineNo = ( $ticketLineItem->getMaxLineNo($pid,$tid) + 1);
						$ticketLineItem->addData($pid,$tid,$vLineNo,$vMsg);
						$vErrorMsg = 'Comment posted';
					}
				}
				elseif( $_POST['target'] == 'authority')
				{
					// approve the ticket
					// print_r("approve the ticket<BR>");
					$ticketAuthorityItem->addData($pid,$tid);
					$vErrorMsg = 'Approved';
				}
				elseif( $_POST['target'] == 'profile')
				{
					// update profile
					/*
					print_r("update profile<BR>");
					var_dump($_POST);
					print_r("<BR>");
					*/
					$vSrcDiv = htmlspecialchars($_POST['src_div']);
					$vSrcName = htmlspecialchars($_POST['src_name']);
					$vDataTarget = htmlspecialchars($_POST['data_target']);
					$vMsg = htmlspecialchars($_POST['msg']);
					$vPriorityLevel = htmlspecialchars($_POST['priority_level']);
					$vUserAffect = htmlspecialchars($_POST['user_affect']);
					
					// checking the input
					$vErrorMsg = '';
					if( $vSrcDiv == 1 && $vSrcName == '' )
					{
						// the requestor is customer, but requestor name is blank
						$vErrorMsg .= "Input the requestor name<BR>";
					}
					if( $vDataTarget == '' )
					{
						$vErrorMsg .= "Input the target function name<BR>";
					}
					if ($vMsg == '' )
					{
						$vErrorMsg .= "Input the details of request<BR>";
					}
					
					/*
					print_r("pid=$pid<BR>");
					print_r("vSrcDiv=$tid<BR>");
					print_r("srcdiv=$vSrcDiv<BR>");
					print_r("vSrcName=$vSrcName<br>");
					print_r("vDataTarget=$vDataTarget<BR>");
					print_r("vMsg=$vMsg<BR>");
					print_r("vPriorityLevel=$vPriorityLevel<BR>");
					*/
					
					if( $vErrorMsg == '' )
					{
						$ticketItem->updateProfile($pid,$tid,$vSrcDiv,$vSrcName,$vDataTarget,$vMsg,$vPriorityLevel,$vUserAffect);
						$vErrorMsg = 'Profile updated';
					}
				}
			}
		}
	}
}

if(count($_GET) > 0)
{
	if((isset($_GET['pid'])) && isset($_GET['tid']))
	{
		// when spcified product_code and ticket_id
		$pid = htmlspecialchars($_GET['pid']);
		$tid = htmlspecialchars($_GET['tid']);
		$data = $ticketItem->getOne( $pid, $tid );

		/*
		var_dump($data);
		print_r("<BR>");
		*/

		if(count($data) == 1)
		{
			// find 1 record
			$tmpProduct = $productItem->getOne($data[0]['product_code']);
			// var_dump($tmpProduct);
			if(count($tmpProduct) == 1)
			{
				$data[0]['product_name'] = $tmpProduct[0]['product_name'];
			}
			else
			{
				$data[0]['product_name'] = 'Unknown';
			}

			$tmpPriority = $priorityItem->getOne($data[0]['priority_level']);
			if(count($tmpPriority) == 1)
			{
				$data[0]['priority_level_name'] = $tmpPriority[0]['level_name'];
			}
			else
			{
				$data[0]['priority_level_name'] = 'Unknown';
			}

			$data[0]['current_status'] = $ticketStatusItem->getLastStatus($data[0]['product_code'],$data[0]['ticket_id']);
			$data[0]['current_status_name'] = $statusItem->GetStatusNameByID($data[0]['current_status']);
			$data[0]['authority'] = $ticketAuthorityItem->getAuthorities($data[0]['product_code'],$data[0]['ticket_id']);
			$data[0]['need_authority'] = $ticketAuthorityItem->getISNeedAuthority($data[0]['product_code'],$data[0]['ticket_id']);

			$data[0]['detail_rows'] = $ticketItem->getDetailRows($data[0]['product_code'],$data[0]['ticket_id']);

			$outData = $data[0];

			/*
			print_r("outData.detail_rows=");
			var_dump($outData['detail_rows']);
			*/

			if( count($outData['detail_rows']) > 0)
			{
				foreach ($outData['detail_rows'] as $row)
				{
					// print_r("<BR>data_div=".$row['data_div']);
					
					$row['update_user_name'] = $userItem->GetUserNameByCode($row['update_user']);

					if(is_null($row['status']))
					{
						$row['status_name'] = null;
					}
					else
					{
						$row['status_name'] = $statusItem->GetStatusNameByID($row['status']);
					}

					// var_dump($row);

					$outData['detail_rows'][$idx] = $row;
					$idx++;

				}
			}

			//var_dump($outData);
			//var_dump($_SESSION);

		}

		/*
		var_dump($data);
		print_r("<BR>data_count=".count($data)."<BR>");
		*/
	}
}

// -- get the master data
// --- product
// $product_items = $commonItem->getAllProducts();
// var_dump($product_items);

// --- status
$status_items = $commonItem->getStatuses();
// var_dump($status_items);

// --- priority
$priority_items = $commonItem->getAllPriorities();

// var_dump($outData);

if( $outData == '' )
{
	$page_name = 'No data available';
	$vErrorMsg = 'The spcified data was not found. Search again.';
} else
{
	$page_name = 'Details';
	$smarty->assign('data',$outData);
	$smarty->assign('status_items', $status_items);
	$smarty->assign('priority_items',$priority_items);
}
$smarty->assign('page_name',$page_name);
$smarty->assign('user_name',$user_name);
$smarty->assign('err_message',$vErrorMsg);
$smarty->assign('info_message',$vInfoMsg);
$smarty->assign('pid',$pid);
$smarty->assign('tid',$tid);
$smarty->assign('show_logout',True);

$smarty->display("detail.tpl");

?>
