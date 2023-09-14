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

$urlFilePath = getenv('URL_FILE_PATH') ?: __DIR__ . '/' . '../url.yaml';
try {
  $yamlData = Yaml::parseFile($urlFilePath);
} catch (ParseException $e) {
  $logger->error($e->getMessage());
  exit(1);
}

$now = CarbonImmutable::now();
$publishPageList = array();
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
              'title' => $item->title,
              'url' => $item->link
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
              'title' => $item->title,
              'url' => $item->link
            ];
          }
        }
      }
  }
}

// slackに通知する

$logger->info("fin");
