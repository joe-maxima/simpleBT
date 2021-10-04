<{include file="commonheader.tpl"}>

<p><a href="./index.php">Back to Index</a></p>

<table class="mainlist">
	<thead><tr>
		<th>Additional information</th>
	</tr></thead>
	<tr><td>
		<form method="post" action="<{$smarty.const.PHP_SELF|escape}>">
			<input type="hidden" name="mode" value="add">
			User ID<input type="text" name="id" value="" size="20" maxlength="20">&nbsp;
			Password<input type="password" name="pw" value="" size="20" maxlength="20">&nbsp;
			User name<input type="text" name="name" value="" size="20" maxlength="20">&nbsp;
			<input type="checkbox" name="auth" style="width:15px;">as Administrator
	</td></tr>
	<tr><td>
		<input class="button" type="submit" value="Add">
		</form>
	</td></tr>
</table>

<hr class="style1">

<{if isset($data)}>
<table class="mainlist">
	<thead><tr>
		<th>User ID</th>
		<th>Password</th>
		<th>User name</th>
		<th>as Administrator</th>
		<th>&nbsp;</th>
		<th>Remove</th>
	</tr></thead>
	<tbody>
	<{foreach name=loop from=$data item=item key=key}>
	<tr>
		<td align="left" valign="middle">
			<{if $smarty.session.user_code == $item.user_code}>
				<{$item.user_id}>
			<{else}>
				<form method="post" action="<{$smarty.const.PHP_SELF|escape}>">
				<input type="hidden" name="mode" value="mod">
				<input type="hidden" name="code" value="<{$item.user_code}>">
				<input type="text" name="id" value="<{$item.user_id}>" size="20" maxlength="20">
			<{/if}>
		</td><td>
			<{if $smarty.session.user_code == $item.user_code}>
				*****
			<{else}>
				<input type="password" name="pw" value="<{$item.user_pw}>" size="20" maxlength="20">
			<{/if}>
		</td><td>
			<{if $smarty.session.user_code == $item.user_code}>
				<{$item.user_name}>
			<{else}>
				<input type="text" name="name" value="<{$item.user_name}>" size="20" maxlength="20">
			<{/if}>
		</td><td>
			<{if $smarty.session.user_code == $item.user_code}>
				<{if $item.authority == 1}><center>＊</center><{else}>&nbsp;<{/if}>
			<{else}>
				<input type="checkbox" name="auth" value="admin" <{if $item.authority == 1}>checked<{/if}>
			<{/if}>
		</td>
		<{if $smarty.session.user_code == $item.user_code}>
		<td colspan="2">
			Can not be changed or deleted by you.
		</td>
		<{else}>
		<td>
			<input class="button_in_table" type="submit" value="Update">
			</form>
		</td>
		<td align="left" valign="middle">
			<form method="post" action="<{$smarty.const.PHP_SELF|escape}>">
				<input type="hidden" name="mode" value="del">
				<input type="hidden" name="code" value="<{$item.user_code}>">
				<input class="button_in_table_red" type="submit" value="Remove">
			</form>
		</td>
		<{/if}>
	</tr>
	<{/foreach}>
	</tbody>

</table>

<{/if}>

<{include file="commonfooter.tpl"}>
