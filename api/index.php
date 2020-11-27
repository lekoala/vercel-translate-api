<?php

use Slim\Http\Response;
use Slim\Factory\AppFactory;
use Slim\Http\ServerRequest;
use App\GoogleTranslateHelper;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$app->get('/translate/{to}[/{from}]', function (ServerRequest $request, Response $response, array $args) {
    $to = $args['to'];
    $from = $args['from'] ?? 'auto';

    try {
        $text = $request->getQueryParam('text');
        if (!$text) {
            throw new Exception("Pass a query parameter named text with a value");
        }
        $arr = [
            'data' => [
                'from' => $from,
                'to' => $to,
                'text' => $text,
                'translation' => GoogleTranslateHelper::translate($text, $to, $from)
            ],
        ];

        // Cache
        // @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cache-Control
        $secondsToCache = 60 * 60 * 24 * 365; // 1 y
        $response = $response->withHeader('Cache-Control', 's-maxage=' . $secondsToCache);
    } catch (Exception $ex) {
        $arr = [
            'error' => $ex->getMessage()
        ];
        $response = $response->withHeader('Cache-Control', 'no-cache');
    }
    $payload = json_encode($arr);
    $response->getBody()->write($payload);
    return $response
        ->withHeader('Content-Type', 'application/json');
});

$app->get('/', function (ServerRequest $request, Response $response, array $args) {
    $arr = [
        'welcome' => 'Please call /translate/{to}[/{from}]?text=your_url_encoded_text'
    ];
    $payload = json_encode($arr);
    $response->getBody()->write($payload);
    return $response
        ->withHeader('Content-Type', 'application/json');
});

$app->run();
