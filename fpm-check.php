<?php
date_default_timezone_set('Asia/Calcutta');
$descriptorspec = array(
   0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
   1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
   2 => array("file", "/tmp/fpm-output.txt", "a") // stderr is a file to write to
);

$cmd = 'fpm-monitor frontend';
$proc = proc_open($cmd,$descriptorspec,$pipes);
$timeout = 30;
$memcache = new Memcache;
$memcache->addServer('localhost', 11211);

if (isset($stdin))
{
   fwrite($pipes[0],$stdin);
}
fclose($pipes[0]);

stream_set_timeout($pipes[1], 60);
//stream_set_timeout($pipes[2], 0);

$stdout = '';
$start = round(microtime(true));

while ($data = fread($pipes[1], 4096))
{
    $meta = stream_get_meta_data($pipes[1]);
    if (round(microtime(true))-$start>$timeout) {
    	if($memcache->get('fpmstatus') == 0) {
	        /* restart fpm*/
	        file_put_contents('/var/log/monitor.log',date('Y-m-d H:i:s').' '.'FRONTEND DOWN: restarting services...'.PHP_EOL.PHP_EOL,FILE_APPEND | LOCK_EX);
	        $memcache->set('fpmstatus',1);
	        $fpm_cmd = 'supervisorctl restart php-fpm:';
	        $output = shell_exec($fpm_cmd);
	        file_put_contents('/var/log/monitor.log',date('Y-m-d H:i:s').' '.'FRONTEND UP: '.$output.PHP_EOL.PHP_EOL,FILE_APPEND | LOCK_EX);
	        break;
    	}else {
    		exit;
    	}
    }else {
    	$memcache->set('fpmstatus',0);
    }
    if ($meta['timed_out']) continue;
    $stdout .= $data;
}

$stdout .= stream_get_contents($pipes[1]);
//$stderr = stream_get_contents($pipes[2]);
$return = proc_close($proc);
echo $stdout;

?>
~     