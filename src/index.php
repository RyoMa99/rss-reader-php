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

$urlFilePath = getenv('URL_FILE_PATH') ?: '../url.yaml';
var_dump(importYamlFile($urlFilePath));
// fetch
// 更新差分のみ抽出
// slackに通知する
$logger->info("fin");


/**
 * 引数に与えられたパスのyamlファイルを読み込みその中身を返す
 *
 * @param string $filePath
 * @return array{name: string, url: string}[]
 */
function importYamlFile(string $filePath): array
{
  try {
    return Yaml::parseFile($filePath);
  } catch (ParseException $e) {
    global $logger;
    $logger->error($e->getMessage());
    exit(1);
  }
}
