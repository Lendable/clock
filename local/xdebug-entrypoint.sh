#!/bin/sh

set -e

if [ "${XDEBUG_ENABLED:-0}" = "1" ]; then
	XDEBUG_HOST=${XDEBUG_HOST:-$(route -n | egrep "^0.0.0.0" | awk '{print $2}')}

	if [ "${XDEBUG_HOST}" = "" ]; then
		echo "XDebug remote host could not be configured";
		exit 1;
	fi

	XDEBUG_PORT="${XDEBUG_PORT:-9003}"

	echo "Configuring PHP for remote XDebug server ${XDEBUG_PORT}"
	echo "zend_extension=xdebug.so
xdebug.mode = debug
xdebug.start_with_request = 1
xdebug.client_host = ${XDEBUG_HOST}
xdebug.client_port = ${XDEBUG_PORT}
xdebug.idekey = ${XDEBUG_IDE_KEY:-PHPSTORM}
xdebug.max_nesting_level = 999999" > /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

	echo '#!/bin/sh
php -dxdebug.mode\=off "$@"' > /usr/local/bin/noxdebug

	chmod 755 /usr/local/bin/noxdebug

else
	echo "Disabling XDebug per configuration"
fi

/bin/sh
