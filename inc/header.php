<!doctype html>
<html lang="fr">
<head>
	<meta charset="utf-8">
	<title><?php echo $pagetitle ?></title>
	<?php if (isset($_GET['guestname'])) { ?>
	<link rel="stylesheet" href="static/fullscreen.css">

	<?php } else { ?>
	<link rel="stylesheet" href="static/main.css">
	<?php } ?>
</head>
<?php flush(); ?>
<body>