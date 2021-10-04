<{include file="commonheader.tpl"}>

<p><a href="./index.php">Back to Index</a></p>

<form method="post" action="<{$smarty.const.PHP_SELF|escape}>">
	<input type="hidden" name="mode" value="<{$mode}>">
	<table class="inputform">
		<tr>
			<th scope="row">Product</th>
			<td><select name="product">
				<{foreach name=products from=$product_items item=pItem key=pKey}>
					<option value="<{$pItem.product_code}>" <{if isset($data)}><{if ($data['product']) == $pItem.product_code}>SELECTED<{/if}><{/if}>><{$pItem.product_name}></option>
				<{/foreach}>
				</select>
			</td>
		</tr><tr>
			<th scope="row">Requestor</th>
			<td>
				<input class="radio" type="radio" name="src_div" value="2" <{if isset($data)}><{if $data['src_div'] == 2}>checked<{/if}><{/if}>>(You)
				<input class="radio" type="radio" name="src_div" value="1" <{if isset($data)}><{if $data['src_div'] == 1}>checked<{/if}><{/if}>>Customer
				&nbsp;Customer name<input type="text" class="long" name="src_name" maxlength="20" value="<{$data['src_name']|default:''}>">
			</td>
		</tr><tr>
			<th scope="row">Target functions</th>
			<td><input type="text" class="long" name="target" maxlength="20" value="<{$data['target']|default:''}>"></td>
		</tr><tr>
			<th scope="row">Details</th>
			<td><textarea name="msg" cols=80 rows=4 maxlength="255"><{$data['msg']|default:''}></textarea></td>
		</tr><tr>
			<th scope="row">User affects</th>
			<td><textarea name="user_affect" cols=80 rows=4 maxlength="255"><{$data['user_affect']|default:''}></textarea></td>
		</tr><tr>
			<th scope="row">Priority level</th>
			<td><select name="priority_level">
				<{foreach name=pri_lvl from=$priority_items item=rItem key=rKey}>
					<option value="<{$rItem.level}>" <{if isset($data)}><{if ($data['priority_level']) == $rItem.level}>SELECTED<{/if}><{/if}>><{$rItem.level_name}></option>
				<{/foreach}>
				</select>
			</td>
		</tr><tr>
			<th scope="row">Status</th>
			<td><select name="status">
				<{foreach name=status from=$status_items item=sItem key=sKey}>
					<option value="<{$sItem.status_id}>" <{if isset($data)}><{if ($data['status']) == $sItem.status_id}>SELECTED<{/if}><{/if}>><{$sItem.status_name}></option>
				<{/foreach}>
				</select><br>
			</td>
		</tr><tr>
			<td colspan="2">
				<input class="button" type="submit" value="Submit">
				<input class="button" type="reset" value="Reset">
			</td>
		</tr>
	</table>
</form>


<{include file="commonfooter.tpl"}>
