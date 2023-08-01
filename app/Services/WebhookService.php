<?php

namespace App\Services;

use GuzzleHttp\Client;

class WebhookService
{
    public $client;
    public $header;
    public $url;

    public function __construct()
    {
        $this->client = new Client();
        $this->header = config("services.webhook.header");
    }

    public function send(string $action, $data)
    {
        try {
            if (empty($this->url)) {
                throw new \InvalidArgumentException('Missing Webhook Url');
            }

            $result = $this->client->request(
                'POST',
                $this->url . '/api/webhook',
                [
                    'json' => [
                        'action' => $action,
                        'data' => $data
                    ],
                    'headers' => $this->header
                ]
            );
            return json_decode($result->getBody()->__toString());
        } catch (\Throwable $th) {
            report($th);
            return false;
        }
    }

    public function setHeader(array $header = [])
    {
        if (!empty($header)) {
            $this->header = array_merge($this->header, $header);
        }
        return $this;
    }

    public function setUrl(string $url)
    {
        $this->url = $url;
        return $this;
    }
}
