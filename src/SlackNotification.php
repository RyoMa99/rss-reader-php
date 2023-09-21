<?php

declare(strict_types=1);

namespace App;

require_once(__DIR__ . '/../vendor/autoload.php');

use GuzzleHttp\Client;

final class SlackNotification
{
  private \GuzzleHttp\Client $client;
  private string $channel;
  private string $token;

  final public function __construct(string $channel, string $token)
  {
    $this->channel = $channel;
    $this->token = $token;
    $this->client = new Client([
      'base_uri' => 'https://slack.com/api/',
    ]);
  }

  public function send(string $title, string $text): int
  {
    $res = $this->client->post('chat.postMessage', [
      'headers' => [
        'Content-Type' => 'application/json',
        'Authorization' => "Bearer {$this->token}",
      ],
      'json' => [
        'channel' => $this->channel,
        'text' => $title,
        'attachments' => json_encode(
          [
            'type' => 'section',
            'text' => [
              'type' => 'plain_text',
              'text' => $text
            ]
          ]
        ),
      ],
    ]);

    return $res->getStatusCode();
  }
}
