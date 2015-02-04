BBBC : BigBlueButton Control
============================

Features :
* multi-user with CAS auth (you must already have a working CAS somewhere)
* create and delete permanant confs
* generate moderator and attendee links

Install
-------
1. unzip content on a webserver path
2. create a new mysql database and import init.sql in it
3. copy config.template.php to config.php and tweak it for your environment
4. to get accurate results with the lattency test, set it to ping your own bbb server :
  * copy upload-to-bbb/ping.jsp to a page where your bbb server will handle JSP (it can be : /var/lib/tomcat6/webapps/demo/ping.jsp )
  * change the hardcoded url at the end of static/js/fullscreen.js to point to your own server and not the UTC server.

Authors
-------
This program was created by :
Cellule d'Appui Pédaogique, Ingénierie des Contenus et Savoirs, http://ics.utc.fr
Université de Technologie de Compiègne, http://www.utc.fr

Developer contact : Stephane Poinsart <stephane.poinsart@utc.fr>


License
-------
This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as
published by the Free Software Foundation, either version 3 of the
License, or (at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU Affero General Public License for more details.
You should have received a copy of the GNU Affero General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.


3rd party License
-----------------
BBBC use the phpCAS library from ESUP-Portail consortium & the JA-SIG Collaborative.
See the headers of the files in the phpCAS/ directory for distribution authorization.
