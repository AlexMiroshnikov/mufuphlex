<?php
$ts = microtime(true);
require_once './bootstrap.php';

$redisInteractor->getRedisUtil()->flushDb();

$indexer = new \Mufuphlex\ReSearcher\Indexer($redisInteractor);
$indexer->setFilter(function($token){
	if ($token == 'http' OR $token == 'https' OR $token == 'www')
	{
		return false;
	}
	return true;
});

$ads = array(
	array(
		'id' => 1,
		'title' => 'This is a text title with the phrase',
		'description' => 'And this is a description',
		'url' => 'https://www.github.com',
		'phrases' => array(
			array(
				'id' => 1,
				'phrase' => 'mua xe máy'
			),
			array(
				'id' => 2,
				'phrase' => 'buy a motorbike'
			),
			array(
				'id' => 3,
				'phrase' => 'the phrase'
			)
		)
	)
);

foreach ($ads as $ad)
{
	$phrases = $ad['phrases'];
	$ad = InteractorAdvert::create($ad);
	$indexer->addObject($ad);

	foreach ($phrases as $phrase)
	{
		$phrase = InteractorPhrase::create($phrase);
		$indexer->addObject($phrase);
	}
}

echo "\nTook ".(round(microtime(true) - $ts, 5)).' s, '.round(memory_get_peak_usage(true)/1000, 1)." kb\n";