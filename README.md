BBBC : BigBlueButton Control
============================

BBBC is a BBB front-end written in php.

Features :
* multi-user with CAS auth (you must already have a working CAS somewhere)
* create and delete permanant confs
* generate moderator and attendee links

Install
-------
1. unzip content on a webserver path
2. create a new mysql database and import init.sql in it
3. copy config.template.php to config.php at tweak it for your environment

Authors
-------
Programmed by Stephane Poinsart <stephane.poinsart@utc.fr>
On behalf of Université de Technologie de Compiègne, http://www.utc.fr
Cellule d'Appui Pédaogique, Ingénierie des Contenus et Savoirs, http://ics.utc.fr


BBBC use the phpCAS library from ESUP-Portail consortium & the JA-SIG Collaborative.
See the headers of the files in the phpCAS/ directory for distribution authorization.
