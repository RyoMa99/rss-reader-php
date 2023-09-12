<?php

declare(strict_types=1);

use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class IndexTest extends TestCase
{
  private string $yamlFileName = 'test.yaml';

  public function setUp(): void
  {
    vfsStream::newFile($this->yamlFileName)
      ->withContent(<<<EOF
- name: JSer.info
url: https://jser.info/rss/
- name: 株式会社メドレー
url: https://developer.medley.jp/rss.xml
EOF)
      ->at(vfsStream::setup('.'));
  }

  public function test(): void
  {
    importYamlFile("test");
  }
}
