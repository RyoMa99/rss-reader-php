<?php

declare(strict_types=1);

require_once(__DIR__ . '/../vendor/autoload.php');

use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

$logger = new Logger(__FILE__);
$logger->pushHandler(new StreamHandler('php://stdout', Level::Debug));

// urlをyamlから読み込む
try {
  $urlFilePath = getenv('URL_FILE_PATH') ?: '../url.yaml';
  $value = Yaml::parseFile($urlFilePath);
  var_dump($value);
} catch (ParseException $e) {
  $logger->error($e->getMessage());
}

// fetch

// 更新差分のみ抽出

// slackに通知する
