<{include file="commonheader.tpl"}>

<p><a href="./index.php">Back to Index</a></p>

<{if isset($data)}>
	<h2 style="color:dimgray">Profile</h2>
	<form method="post" action="<{$smarty.const.PHP_SELF|escape}>">
	<input type="hidden" name="target" value="profile">
		<table class="inputform">
			<tr>
				<th scope="row">Product</th>
				<td><{$data['product_name']}></td>
				<!-- <td>&nbsp;</td> //-->
			</tr><tr>
				<th scope="row">Requestor</th>
				<!-- <td><{if $data['src_div'] == 1}><{$data['src_name']}><{elseif $data['src_div'] == 2}>（起票者）<{else}>（不明）<{/if}></td> //-->
				<td><input class="radio" type="radio" name="src_div" value="2" <{if $data['src_div'] == 2}>checked<{/if}>>(You)
					<input class="radio" type="radio" name="src_div" value="1" <{if $data['src_div'] == 1}>checked<{/if}>>Customer
					&nbsp;Customer name<input type="text" class="long" name="src_name" maxlength="20" value="<{$data['src_name']|default:''}>">
				</td>
			</tr><tr>
				<th scope="row">Target functions</th>
				<!-- <td><{$data['target']|default:''}></td> //-->
				<td><input type="text" class="long" name="data_target" maxlength="20" value="<{$data['target']|default:''}>"></td>
			</tr><tr>
				<th scope="row">Details</th>
				<!-- <td><{$data['ticket_msg']|default:''|nl2br nofilter}></td> //-->
				<td><textarea name="msg" cols=80 rows=15 maxlength="255"><{$data['ticket_msg']|default:''}></textarea></td>
			</tr><tr>
				<th scope="row">User affects</th>
				<td><textarea name="user_affect" cols=80 rows=15 maxlength="255"><{$data['user_affect']|default:''}></textarea></td>
			</tr><tr>
				<th scope="row">Priority level</th>
				<!-- <td><{$data['priority_level_name']}></td> //-->
				<td><select name="priority_level">
					<{foreach name=pri_lvl from=$priority_items item=rItem key=rKey}>
						<option value="<{$rItem.level}>" <{if isset($data)}><{if ($data['priority_level']) == $rItem.level}>SELECTED<{/if}><{/if}>><{$rItem.level_name}></option>
					<{/foreach}>
					</select>
				</td>
			</tr><tr>
				<th scope="row">Current status</th>
				<td><{$data['current_status_name']}></td>
				<!-- <td>&nbsp;</td> //-->
			</tr><tr>
				<th scope="row">Authority</th>
				<td><{if count($data['authority']) > 0}>
						<{foreach name=loop from=$data['authority'] item=item key=key}>
							<{$item.update_datetime}>&nbsp;<span class="authority_name"><{$item.user_name}></span><BR>
						<{/foreach}>
					<{else}>
						<span class="authority_empty">No authorities</span>
					<{/if}>
				</td>
				<!-- <td>&nbsp;</td> //-->
			</tr>
		</table>
		<br>
		<input class="button_long" type="submit" value="Update profile">
	</form>

	<{if $data['need_authority'] == True }>
		<!-- [S] authority //-->
		<hr class="style1">
		<h2 style="color:dimgray">Necessary of your approve</h2>
		<form method="post" action="<{$smarty.const.PHP_SELF|escape}>">
			<input type="hidden" name="target" value="authority">
			<input class="button" type="submit" value="Approve">
		</form>
		<!-- [E] authority //-->
	<{/if}>

	<{if count($data['detail_rows']) > 0 }>
		<!-- [S] timeline //-->
		<hr class="style1">
		<h2 style="color:dimgray">Timelines</h2>

		<{foreach name=loop from=$data['detail_rows'] item=item key=key}>
			<p><span class="timeline_notice">
				<{$item.update_datetime}>&nbsp;<{$item.update_user_name}>&nbsp;
				<{if $item.data_div == 0}>
					did set to status <span class="timeline_status"><{$item.status_name}></span></span>
				<{else}>
					posted comment:<br></span><{$item.comment|nl2br nofilter}>
				<{/if}>
			</p>
		<{/foreach}>
		<!-- [E] timeline //-->
	<{/if}>

	<!-- [S] status_modify //-->
	<hr class="style1">
	<h2 style="color:dimgray">Update status</h2>
	<form method="post" action="<{$smarty.const.PHP_SELF|escape}>">
		<input type="hidden" name="target" value="status">
		<select name="status">
			<{foreach name=status from=$status_items item=sItem key=sKey}>
				<option value="<{$sItem.status_id}>" <{if isset($data)}><{if ($data['current_status']) == $sItem.status_id}>SELECTED<{/if}><{/if}>><{$sItem.status_name}></option>
			<{/foreach}>
		</select>
		&nbsp;
		<input class="button" type="submit" value="Update">
	</form>
	<!-- [E] status_modify //-->

	<!-- [S] send comment //-->
	<hr class="style1">
	<h2 style="color:dimgray">Post a comment</h2>
	<form method="post" action="<{$smarty.const.PHP_SELF|escape}>">
		<input type="hidden" name="target" value="line">
		<textarea name="msg" cols=80 rows=4 maxlength="255"></textarea>
		<br>
		<input class="button" type="submit" value="Post">
	</form>
	<!-- [E] send comment //-->

<{/if}>

<hr class="style1">

<{include file="commonfooter.tpl"}>
