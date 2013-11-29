php-fpm-monitoring
==================

PHP-FPM monitoring

Some scripts we have created at tastykhana to monitor php-fpm pools 

1. fpm.sh used to track php-fpm status using cgi-fcgi used to track pools like frontend, backend and api
2. fpm-check.php service run by cron to check php-fpm status using script above and restarting services if max-children are reached.

supervisord is used to keep fpm process running all the time

supervisorctl start php-fpm:
supervisorctl stop php-fpm:
supervisorctl restart php-fpm:
 
