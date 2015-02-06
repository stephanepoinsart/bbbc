<?php


// must be an already existing mysql database
// preloaded with init.sql
define("DB_LOGIN", "bbbc");
define("DB_PASSWORD", "bbbc");
define("DB_NAME", "bbbc");
define("DB_HOST", "localhost");

// site informations are used by BBBC only

// trailing slash is mandatory
define("SITE_URL", "http://localhost/bbbc/");

// Internal codes used to validate URLs
// Choose any for those before production use but...
// if you change it, every existing moderator and attendee links will change,
// and old ones that users may already be using will become invalid
define("SITE_ADMIN_SECRET", "CHANGEME");
define("SITE_USER_SECRET", "ANDCHANGEMETOO");


// to get those values, run on the bbb server :
// bbb-conf --secret
//
// see https://code.google.com/p/bigbluebutton/wiki/API for details
define("BBB_URL", "http://bbb.utc.fr/bigbluebutton/");
define("BBB_SECRET", "0123456789abcde0123456789abcde");


// Full Hostname of your CAS Server
$cas_host = 'cas.utc.fr';
// Context of the CAS Server
$cas_context = '/cas';
// Port of your CAS server. Normally for a https server it's 443
$cas_port = 443;



?>