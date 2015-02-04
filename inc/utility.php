<?php
/*
 * BBBC : BigBlueButton Control
* (c) Cellule d'Appui Pédagogique, Université de Technologie de Compiègne
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU Affero General Public License as
* published by the Free Software Foundation, either version 3 of the
* License, or (at your option) any later version.
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU Affero General Public License for more details.
* You should have received a copy of the GNU Affero General Public License
* along with this program. If not, see <http://www.gnu.org/licenses/>.
*/


/*
 * Various somewhat autonomous functions
 * generaly reusable in various parts of BBBC
 */

require_once('config.php');

$messages_already_shown=false;
$successbuffer='';

// a simple message display system
// 2 kinds of messages : error and success
//   - error stop everything and display itself
//   - success display a sig 
//
// buffering system : if the message comes too early, we buffer it until the headers are sent 
function showerror($e) {
	require_once('inc/header.php');
	echo "<main>";
	showmessage($e, 'errormsg');
	echo "<a href=\"".$_SERVER["REQUEST_URI"]."\">Retour</a></main>";
	require_once('inc/footer.php');
	die();

}
function showsuccess($s) {
	global $messages_already_shown, $successbuffer;
	if ($messages_already_shown)
		showmessage($s, 'successmsg');
	else
		$successbuffer=$s;
}

function showbuffered() {
	global $messages_already_shown, $successbuffer;
	
	if ($successbuffer) {
		
		showmessage($successbuffer, 'successmsg');
	}
	$messages_already_shown=true;
}

function showmessage($m, $msgtype) {
	echo "<div class=\"$msgtype\">$m</div>";
} 

// site functions manage the BBBC local website checksums and URLs
function getsitesecret($role) {
	if ($role='admin') {
		return SITE_ADMIN_SECRET;
	}
	return SITE_USER_SECRET;
}

function gensitehash($confcreator, $confname, $role) {
	$secret=getsitesecret($role);
	return sha1($confcreator.$confname.$role.$secret);
}

function gensiteurl($confcreator, $id, $confname, $role, $guestname=NULL) {
	return (SITE_URL."redirect.php?confcreator=$confcreator&amp;id=$id&amp;confname=$confname&role=$role&amp;hash=".gensitehash($confcreator, $confname, $role).(($guestname!=NULL)?"&amp;guestname=$guestname":''));
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