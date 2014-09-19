<?php
/*
 * Various somewhat autonomous functions
 * generaly reusable in various parts of BBBC
 */

require_once('config.php');

// just trivial message display commands
function showerror($e) {
	echo $e;
}
function showsuccess($s) {
	echo $s;
}

// site functions manage the BBBC local website checksums and URLs
function getsitesecret($role) {
	if ($role='admin') {
		return SITE_ADMIN_SECRET;
	}
	return SITE_USER_SECRET;
}

function gensitehash($confcreator, $id, $confname, $role) {
	$secret=getsitesecret($role);
	return sha1($confcreator.$id.$confname.$role.$secret);
}

function gensiteurl($confcreator, $id, $confname, $role, $guestname=NULL) {
	return (SITE_URL."redirect.php?confcreator=$confcreator&amp;id=$id&amp;confname=$confname&role=$role&amp;hash=".gensitehash($confcreator, $id, $confname, $role).(($guestname!=NULL)?"&amp;guestname=$guestname":''));
}

// BBB functions manage the BBB remote service checksums and URLs
function bbbmeetingID($confcreator, $confname) {
	return $confcreator."-".sha1($confname);
}

function finalizebbburl($command, $qs) {
	return BBB_URL."api/".$command."?".$qs."&checksum=".sha1($command.$qs.BBB_SECRET);
}

function genbbbcreateurl($confcreator, $confname, $role) {
	return finalizebbburl('create', "name=$confname&meetingID=".bbbmeetingID($confcreator, $confname)."&attendeePW=user&moderatorPW=admin");
}

function genbbbjoinurl($confcreator, $confname, $role, $guestname) {
	return finalizebbburl('join', "fullName=$guestname&meetingID=".bbbmeetingID($confcreator, $confname)."&password=$role");
}

?>