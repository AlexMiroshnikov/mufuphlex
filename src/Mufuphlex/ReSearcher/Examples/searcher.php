<?php
$ts = microtime(true);
require_once './bootstrap.php';

$searcher = new \Mufuphlex\ReSearcher\Searcher($redisInteractor, $tokenizer);

$settings = array(
	new \Mufuphlex\ReSearcher\SearcherResultSettings(array('type' => 'ad', 'resultClass' => 'InteractorAdvert')),
	new \Mufuphlex\ReSearcher\SearcherResultSettings(array('type' => 'ph', 'resultClass' => 'InteractorPhrase'))
);

$args = $argv;
array_shift($args);
$searchs = array();

foreach ($args as $arg)
{
	$searchs[] = trim($arg, "'");
}

foreach ($searchs as $term)
{
	echo "\nSearched for '".$term."'";
	$results = $searcher->search($term, $settings);
	echo "\nResults (".$searcher->getResultCount()."):\n";

	foreach ($results as $type => $typedResults)
	{
		echo "\n type ".$type." (".$searcher->getResultCountByType($type)." found)\n";

		foreach ($typedResults as $result)
		{
			echo "\t#".$result->getId()." => ".implode(' ', $result->getTokens())."\t (".round($result->getScore(), 3).")\n";
		}
	}
}

echo "\nTook ".(round(microtime(true) - $ts, 5)).' s, '.round(memory_get_peak_usage(true)/1000, 1)." kb\n";