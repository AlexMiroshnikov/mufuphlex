<?php
setlocale(LC_ALL, 'vi_VN.utf-8');
setlocale(LC_NUMERIC, 'en_US.utf-8');
mb_internal_encoding('utf-8');
mb_regex_encoding('utf-8');

require_once '../../../../vendor/autoload.php';
require_once './interaction.php';

$connectConfig = array(
	'namespace' => 'Example:',
	'db' => 3
);

$redisInteractor = new \Mufuphlex\ReSearcher\RedisInteractor($connectConfig);
$tokenizer = new \Mufuphlex\ReSearcher\TokenizerVn();