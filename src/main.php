<?php

declare(strict_types=1);

require_once(__DIR__ . '/../vendor/autoload.php');

use Carbon\CarbonImmutable;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

$logger = new Logger(__FILE__);
$logger->pushHandler(new StreamHandler('php://stdout', Level::Debug));

$configFilePath = getenv('CONFIG_FILE_PATH') ?: __DIR__ . '/' . '../config.yaml';
try {
  $configData = Yaml::parseFile($configFilePath);
} catch (ParseException $e) {
  $logger->error($e->getMessage());
  exit(1);
}

$urlFilePath = getenv('URL_FILE_PATH') ?: __DIR__ . '/' . '../url.yaml';
try {
  $yamlData = Yaml::parseFile($urlFilePath);
} catch (ParseException $e) {
  $logger->error($e->getMessage());
  exit(1);
}

$now = CarbonImmutable::now();
$publishPageList = [];
foreach (array_keys($yamlData) as $type) {
  switch ($type) {
    case ('rss'):
      foreach ($yamlData['rss'] as $site) {
        $logger->info(sprintf('check: %s', $site['name']));

        $rss = simplexml_load_file($site['url']);

        foreach ($rss->channel->item as $item) {
          $pubDate = new CarbonImmutable($item->pubDate);
          if ($pubDate->lte($now) && $pubDate->gte($now->subHours(3))) {
            $publishPageList[] = [
              'title' => $site['name'],
              'url' => "<{$item->link} | {$item->title}>",
            ];
          }
        }
      }
    case ('atom'):
      foreach ($yamlData['atom'] as $site) {
        $logger->info(sprintf('check: %s', $site['name']));

        $rss = simplexml_load_file($site['url']);

        foreach ($rss->entry as $item) {
          $pubDate = new CarbonImmutable($item->updated);
          if ($pubDate->lte($now) && $pubDate->gte($now->subHours(3))) {
            $publishPageList[] = [
              'title' => $site['name'],
              'url' => "<{$item->link} | {$item->title}>",
            ];
          }
        }
      }
  }
}

$logger->info(sprintf('publish page count: %d', count($publishPageList)));
$client = new App\SlackNotification($configData['SLACK_CHANNEL'], $configData['SLACK_TOKEN']);
foreach ($publishPageList as $publishPage) {
  $client->send($publishPage['title'], $publishPage['url']);
}

$logger->info("fin");
