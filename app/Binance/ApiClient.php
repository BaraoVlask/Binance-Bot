<?php

namespace App\Binance;

use App\Services\TelegramService;
use Binance\Util\Url;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Support\Facades\Log;
use OpenSSLAsymmetricKey;
use Psr\Log\AbstractLogger;
use Psr\Log\NullLogger;

abstract class ApiClient
{
    /**
     * The base url of the API server, e.g. https://api.binance.com
     *
     * @var string
     */
    private string $baseURL;


    /**
     * The API Key from Binance
     *
     * @var string
     */
    private string $key;

    /**
     * The API secret from Binance
     *
     * @var string
     */
    private string $secret;

    /**
     * RSA private key
     *
     * @var null|string|OpenSSLAsymmetricKey
     */
    private null|string|OpenSSLAsymmetricKey $privateKey;

    /**
     * The logger instance
     */
    private AbstractLogger $logger;

    /**
     * Timeout value in second, by default it's 0 (no timeout)
     *
     * @var int
     */
    private int $timeout;

    /**
     * If display the weight usage, false by default
     *
     * @var bool
     */
    private bool $showWeightUsage;

    /**
     * if display the whole response header, false by default
     *
     * @var bool
     */
    private bool $showHeader;

    /**
     * HTTP client instance
     */
    private ?Client $httpRequest = null;

    /**
     * @var null|string Request identification tag
     */
    private ?string $requestTag = null;

    public function __construct($args = array())
    {
        $this->baseURL = $args['baseURL'] ?? null;
        $this->key = $args['key'] ?? null;
        $this->secret = $args['secret'] ?? null;
        $this->logger = $args['logger'] ?? new NullLogger();
        $this->timeout = $args['timeout'] ?? 0;
        $this->showWeightUsage = $args['showWeightUsage'] ?? false;
        $this->showHeader = $args['showHeader'] ?? false;
        $this->privateKey = $args['privateKey'] ?? null;
        $this->buildClient($args['httpClient'] ?? null);
    }

    /**
     * @param string|null $requestTag
     * @return ApiClient
     */
    public function setRequestTag(?string $requestTag): ApiClient
    {
        $this->requestTag = $requestTag;
        return $this;
    }

    /**
     * Send HTTP request that don't require signature
     * This also can be used to send MARKET_DATA, which requires API key in the request header
     * @throws GuzzleException
     */
    protected function publicRequest($method, $path, array $params = [])
    {
        return $this->processRequest($method, $path, $params);
    }

    /**
     * Send HTTP request that require signature
     * @throws GuzzleException
     */
    protected function signRequest($method, $path, array $params = [])
    {
        $params['timestamp'] = round(microtime(true) * 1000);
        $query = Url::buildQuery($params);

        if ($this->privateKey) {
            openssl_sign($query, $binary_signature, $this->privateKey, OPENSSL_ALGO_SHA256);
            $params['signature'] = base64_encode($binary_signature);
        } else {
            $params['signature'] = hash_hmac('sha256', $query, $this->secret);
        }
        return $this->processRequest($method, $path, $params);
    }

    /**
     * @throws GuzzleException
     */
    private function processRequest($method, $path, $params = array()): array|null
    {
        try {
            $response = $this->httpRequest->request($method, $this->buildQuery($path, $params));


            $body = json_decode($response->getBody(), true);

            if ($this->showWeightUsage) {
                $weights = [];
                foreach ($response->getHeaders() as $name => $value) {
                    $name = strtolower($name);
                    if (str_starts_with($name, 'x-mbx-used-weight') ||
                        str_starts_with($name, 'x-mbx-order-count') ||
                        str_starts_with($name, 'x-sapi-used')) {
                        $weights[$name] = $value;
                    }
                }
                return [
                    'data' => $body,
                    'weight_usage' => $weights
                ];
            }

            if ($this->showHeader) {
                return [
                    'data' => $body,
                    'header' => $response->getHeaders()
                ];
            }

            return $body;
        } catch (ClientException|ServerException $exception) {
            $exceptionBody = json_decode((string)$exception->getResponse()->getBody(), true);

            $message = "$this->requestTag Error: {$exceptionBody['msg']}";
            TelegramService::sendMessage($message);
            if (config('app.debug')) {
                Log::error(
                    $message,
                    array_merge(
                        (array)$exception->getRequest(),
                        $exception->getTrace()
                    )
                );
            }
            return null;
        }
    }

    private function buildQuery($path, $params = array()): string
    {
        if (count($params) == 0) {
            return $path;
        }
        return $path . '?' . Url::buildQuery($params);
    }

    private function buildClient($httpRequest): void
    {
        $this->httpRequest = $httpRequest ??
            new Client([
                'base_uri' => $this->baseURL,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-MBX-APIKEY' => $this->key,
                    'User-Agent' => 'binance-connect-php'
                ],
                'timeout' => $this->timeout
            ]);
    }
}
