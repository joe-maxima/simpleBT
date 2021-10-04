<p>&nbsp;</p>

<{if $show_logout == True}>
	<form method="post" action="./index.php">
		<input type="hidden" name="mode" value="logout">
		<input class="button" type="submit" name="submit" value="Log out">
	</form>
<{/if}>

</body>
</html>
