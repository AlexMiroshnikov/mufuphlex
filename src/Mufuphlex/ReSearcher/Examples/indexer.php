<?php
$ts = microtime(true);
require_once './bootstrap.php';

$indexer = new \Mufuphlex\ReSearcher\Indexer($redisInteractor);

$ads = array(
	array(
		'id' => 1,
		'title' => 'This is a text title',
		'description' => 'And this is a description',
		'url' => 'https://github.com',
		'phrases' => array(
			array(
				'id' => 1,
				'phrase' => 'mua xe máy'
			),
			array(
				'id' => 2,
				'phrase' => 'buy a motorbike'
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

echo "\nTook ".(round(microtime(true) - $ts, 5)).' ms, '.round(memory_get_peak_usage(true)/1000, 1)." kb\n";