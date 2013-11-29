#!/bin/bash

export SCRIPT_NAME=/fpm_status
export SCRIPT_FILENAME=/fpm_status
TIMESTAMP="TIMESTAMP: "`date +%Y-%m-%d\ %k:%M:%S`

if [[ $1 == "frontend" ]]
then
        LISTEN=127.0.0.1:9000
fi
if [[ $1 == "backend" ]]
then
        LISTEN=127.0.0.1:9003
fi
if [[ $1 == "api" ]]
then
        LISTEN=127.0.0.1:9001
fi

if [[ $2 == "full" || $3 == "full" ]]
then
        export QUERY_STRING+="full&"
fi
if [[ $2 == "json" || $3 == "json" ]]
then
        export QUERY_STRING+="json&"
fi

export REQUEST_METHOD=GET

CGI2FCGI=/usr/bin/cgi-fcgi

if [ ! -x ${CGI2FCGI} ]
then
        echo "cgi-fcgi does not appear to be installed:"
        echo -e "\t* On debian this can be installed with: $ sudo apt-get install libfcgi0ldbl\n"
        exit 1
fi

if [ !  ${LISTEN} ]
then
        echo "${LISTEN} does not appear to be a socket"
        exit 1
fi

${CGI2FCGI} -bind -connect ${LISTEN}
echo ${TIMESTAMP}

