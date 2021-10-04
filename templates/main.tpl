<{include file="commonheader.tpl"}>

<p><a href="./entry.php">Create new ticket</a>
	<{if $has_hidden_items == True}>&nbsp;<a href="./index.php?show=all">Show hidden tickets</a><{/if}>
	<{if $smarty.session.authority == 1}>&nbsp;<a href="./product.php">Manage products</a><{/if}>
	<{if $smarty.session.authority == 1}>&nbsp;<a href="./user.php">Manage users</a><{/if}>
</p>

<table class="mainlist">
	<thead><tr>
		<th>&nbsp;</th>
		<th>#</th>
		<th>Product</th>
		<th>Created at</th>
		<th>Created by</th>
		<th>Requestor</th>
		<th>Target functions</th>
		<th>Issue details</th>
		<th>User affects</th>
		<th>Priority level</th>
		<th>Current status</th>
		<th>Last comment at</th>
		<th>Authorities</th>
		<{if $smarty.session.authority == 1}>
		<th>Show / Hide</th>
		<{/if}>
	</tr></thead>
	<tbody>
	<{foreach name=loop from=$lines item=item key=key}>
	<tr><td><a href="./detail.php?pid=<{$item.product_code}>&tid=<{$item.ticket_id}>">Details</a></td>
		<td align="right"><{$item.ticket_id}></td>
		<td align="center"><{$item.product_name}></td>
		<td align="right"><{$item.request_date}></td>
		<td align="center"><{$item.request_user_name}></td>
		<td align="center"><{if $item.src_div == 1}><{$item.src_name}><{elseif $item.src_div == 2}>Coworker<{else}>（Unknown）<{/if}></td>
		<td><{$item.target}></td>
		<td><{$item.ticket_msg|nl2br nofilter}></td>
		<td><{$item.user_affect|nl2br nofilter}></td>
		<td align="center"><{$item.priority_level_name}></td>
		<td align="center"><{if $item.status_color != ''}><span style="color:<{$item.status_color}>;"><{/if}><{$item.status_name}><{if $item.status_color != ''}></span><{/if}></td>
		<td align="right"><{$item.lastupdate|default:'(Not updated)'}><BR>
			<{if $item.lastusercode != null}><{if $item.lastusercode != $smarty.session.user_code}><span style="color:red;"><{/if}><{/if}>
				<{$item.lastcomment}>
			<{if $item.lastusercode != null}><{if $item.lastusercode != $smarty.session.user_code}></span><{/if}><{/if}>
			</td>
		<td align="right">
			<{if $item.authorized_count == $item.authorize_count}>
				<{if $item.authorized_count == 0}><span class="authorized_incomplete">
				<{else}><span class="authorized_complete">
				<{/if}>
			<{else}>
				<{if $item.authorized_count == 0}><span class="authorized_incomplete">
				<{else}><span class="authorized_progress">
				<{/if}>
			<{/if}>
			<{$item.authorized_count}>&nbsp;/&nbsp;<{$item.authorize_count}>
			</span>
		</td>
		<{if $smarty.session.authority == 1}>
		<td>
			<form method="post" action="<{$smarty.server.REQUEST_URI}>">
				<input type="hidden" name="ticket_id" value="<{$item.ticket_id}>">
				<input type="hidden" name="product_code" value="<{$item.product_code}>">
				<{if $item.is_show == 1}>
					<input type="hidden" name="mode" value="hide">
					<input class="button_in_table" type="submit" name="submit" value="Hide">
				<{else}>
					<input type="hidden" name="mode" value="show">
					<input class="button_in_table" type="submit" name="submit" value="Show">
				<{/if}>
			</form>
		</td>
		<{/if}>
	</tr>
	<{/foreach}>
	</tbody>

</table>

<{include file="commonfooter.tpl"}>
