<{include file="commonheader.tpl"}>

<p><a href="./index.php">Back to Index</a></p>

<form method="post" action="<{$smarty.const.PHP_SELF|escape}>">
	<input type="hidden" name="mode" value="add">
	<input type="text" name="name" value="" size="50" maxlength="50">
	<input class="button" type="submit" value="Add">
</form>

<hr class="style1">

<{if isset($data)}>
<table class="mainlist">
	<thead><tr>
		<th>#</th>
		<th>Product name</th>
		<th>Remove</th>
	</tr></thead>
	<tbody>
	<{foreach name=loop from=$data item=item key=key}>
	<tr>
		<td align="right" valign="middle"><{$item.product_code}></td>
		<td align="left" valign="middle">
			<form method="post" action="<{$smarty.const.PHP_SELF|escape}>">
				<input type="hidden" name="mode" value="mod">
				<input type="hidden" name="code" value="<{$item.product_code}>">
				<input type="text" name="name" value="<{$item.product_name}>" size="50" maxlength="50">
				<input class="button_in_table" type="submit" value="Update">
			</form>
		</td>
		<td align="left" valign="middle">
			<form method="post" action="<{$smarty.const.PHP_SELF|escape}>">
				<input type="hidden" name="mode" value="del">
				<input type="hidden" name="code" value="<{$item.product_code}>">
				<input class="button_in_table_red" type="submit" value="Remove">
			</form>
		</td>
	</tr>
	<{/foreach}>
	</tbody>

</table>

<{/if}>

<{include file="commonfooter.tpl"}>
