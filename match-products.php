<?php

spl_autoload_register(function($className) {
    $className = str_replace("\\", DIRECTORY_SEPARATOR, $className);
    include_once $_SERVER['DOCUMENT_ROOT'] . 'src/' . $className . '.php';
});

if (empty($argv[1])) {
    throw new \RuntimeException('Path to products file required as first script argument.');
}

if (empty($argv[2])) {
    throw new \RuntimeException('Path to rules file required as second script argument.');
}

$products = getJsonContent($argv[1]);
$rules = getJsonContent($argv[2]);

$matchStart = microtime(true);

$matchStrategyFactory = new \ProductMatcher\Strategy\ProductMatchStrategyFactory();
$strategy = $matchStrategyFactory->create(\ProductMatcher\Strategy\FilterMatchedByGeneralRulesStrategy::class);
$matchedProducts = $strategy->match($products, $rules);

$matchTime = (microtime(true) - $matchStart);

echo "Script time: $matchTime sec".PHP_EOL;

$memoryPeakInKB = round(memory_get_peak_usage() / 1024);
echo "Memory peak: $memoryPeakInKB KB" . PHP_EOL;

echo "Products: ".PHP_EOL;
echo json_encode($matchedProducts, JSON_PRETTY_PRINT);

function getJsonContent(string $filePath): array
{
    $content = file_get_contents($filePath);
    $content = json_decode($content, true);
    if (!is_array($content) || json_last_error() !== JSON_ERROR_NONE) {
        throw new \RuntimeException("Invalid file content. Expected Json in $filePath.");
    }

    return $content;
}
