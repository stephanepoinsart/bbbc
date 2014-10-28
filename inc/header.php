<!doctype html>
<html lang="fr">
<head>
	<meta charset="utf-8">
	<title><?php
	if (!isset($pagetitle))
		$pagetitle="BBB control";
	echo $pagetitle;
	?></title>
	<link rel="stylesheet" href="static/css/common.css">
	<?php if (isset($_GET['guestname'])) { ?>
	<link rel="stylesheet" href="static/css/fullscreen.css">
	<?php } else { ?>
	<link rel="stylesheet" href="static/css/manager.css">
	<?php } ?>
</head>
<?php flush(); ?>
<body>