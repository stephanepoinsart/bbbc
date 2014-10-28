#! /bin/bash


if [ $# -ne 1 ] ; then
	echo "Usage: `basename $0` <username ssh sur icsdata.utc>"
	exit 1
fi

type rsync >/dev/null 2>&1 || {
	echo "rsync program not found, must be installed to continue"
	exit 1
}

type ssh >/dev/null 2>&1 || {
	echo "ssh program not found, must be installed to continue"
	exit 1
}

rsync -rlvz -e ssh --exclude 'inc/config.php' . $1@icsdata.utc:/data/public_html/bbbc
