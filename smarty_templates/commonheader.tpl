<!DOCTYPE html>
<html dir="ltr" lang="ja">
<head>
<meta charset="UTF-8">
<{if isset($reloadsec)}><meta http-equiv="Refresh" content="<{$reloadsec}>"><{/if}>
<meta name="description" content="<{$page_name}>">
<title><{$page_name}></title>
<link rel="stylesheet" href="style.css" type="text/css" media="screen">
</head>
<body>
<{if isset($user_name) }><{if $user_name != ''}><p style="float:right;"><span style="color:dimgray"><{$user_name}></span><{/if}><{/if}>
<!-- <br><a href="./man/" target="_blank">マニュアル</a> //--> </p>
<!-- message //-->
<{if isset($err_message)}><p style="color:firebrick;"><{$err_message|nl2br nofilter}></p><{/if}>
<{if isset($info_message)}><p style="color:royalblue;"><{$info_message|nl2br nofilter}></p><{/if}>

<h1><{$page_name|nl2br nofilter}></h1>
