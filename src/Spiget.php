<?php

namespace GamePanelio\SpigetApi;

use GamePanelio\SpigetApi\Exception\ApiCommunicationException;
use Http\Client\Exception\TransferException;
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Discovery\StreamFactoryDiscovery;
use Http\Discovery\UriFactoryDiscovery;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

class Spiget
{
    const DEFAULT_USER_AGENT = 'GamePanelio_Spiget_API_Library';

    const API_SCHEME = 'https';
    const API_HOST = 'api.spiget.org';
    const API_BASE = '/v2/';

    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * @var UriInterface
     */
    private $uri;

    /**
     * @var string
     */
    private $userAgent;

    /**
     * Spiget constructor.
     * @param string $userAgent
     * @param HttpClient|null $httpClient
     */
    public function __construct($userAgent = self::DEFAULT_USER_AGENT, HttpClient $httpClient = null)
    {
        if (!$httpClient instanceof HttpClient) {
            $httpClient = HttpClientDiscovery::find();
        }

        $this->uri = UriFactoryDiscovery::find()->createUri(self::API_HOST)
            ->withHost(self::API_HOST)
            ->withScheme(self::API_SCHEME)
            ->withPath(self::API_BASE);
        $this->userAgent = $userAgent;
        $this->httpClient = $httpClient;
    }

    /**
     * @return RequestInterface
     */
    private function createRequest()
    {
        return MessageFactoryDiscovery::find()
            ->createRequest(
                'GET',
                $this->uri,
                [
                    'User-Agent' => $this->userAgent
                ]
            );
    }

    /**
     * @param $body
     * @return StreamInterface
     */
    private function createStreamFor($body)
    {
        return StreamFactoryDiscovery::find()->createStream($body);
    }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    private function sendRequest(RequestInterface $request)
    {
        try {
            $response = $this->httpClient->sendRequest($request);

            if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300) {
                throw new ApiCommunicationException(
                    sprintf('The request resulted in a non-success HTTP code %s; ', $response->getStatusCode()) .
                    $response->getBody()->getContents(),
                    $response->getStatusCode()
                );
            }

            return $response;
        } catch (TransferException $e) {
            throw ApiCommunicationException::wrap($e);
        }
    }

    /**
     * @param ResponseInterface $response
     * @param bool $assoc
     * @param int $depth
     * @param int $options
     * @return mixed
     */
    public function getResponseBodyFromJson(ResponseInterface $response, $assoc = true, $depth = 512, $options = 0)
    {
        return json_decode($response->getBody(), $assoc, $depth, $options);
    }

    /**
     * @param array $parameters
     * @return ResponseInterface
     */
    public function getAuthorList($parameters = [])
    {
        $request = $this->createRequest()
            ->withMethod('GET')
            ->withUri(
                $this->uri->withPath(self::API_BASE . 'authors')
                    ->withQuery(http_build_query($parameters))
            );

        return $this->sendRequest($request);
    }

    /**
     * @param $author
     * @return ResponseInterface
     */
    public function getAuthorDetails($author)
    {
        $request = $this->createRequest()
            ->withMethod('GET')
            ->withUri(
                $this->uri->withPath(self::API_BASE . 'authors/' . urlencode($author))
            );

        return $this->sendRequest($request);
    }

    /**
     * @param $author
     * @param array $parameters
     * @return ResponseInterface
     */
    public function getAuthorResources($author, $parameters = [])
    {
        $request = $this->createRequest()
            ->withMethod('GET')
            ->withUri(
                $this->uri->withPath(self::API_BASE . 'authors/' . urlencode($author) . '/resources')
                    ->withQuery(http_build_query($parameters))
            );

        return $this->sendRequest($request);
    }

    /**
     * @param $author
     * @param array $parameters
     * @return ResponseInterface
     */
    public function getAuthorReviews($author, $parameters = [])
    {
        $request = $this->createRequest()
            ->withMethod('GET')
            ->withUri(
                $this->uri->withPath(self::API_BASE . 'authors/' . urlencode($author) . '/reviews')
                    ->withQuery(http_build_query($parameters))
            );

        return $this->sendRequest($request);
    }

    /**
     * @param array $parameters
     * @return ResponseInterface
     */
    public function getCategoryList($parameters = [])
    {
        $request = $this->createRequest()
            ->withMethod('GET')
            ->withUri(
                $this->uri->withPath(self::API_BASE . 'categories')
                    ->withQuery(http_build_query($parameters))
            );

        return $this->sendRequest($request);
    }

    /**
     * @param $category
     * @return ResponseInterface
     */
    public function getCategoryDetails($category)
    {
        $request = $this->createRequest()
            ->withMethod('GET')
            ->withUri(
                $this->uri->withPath(self::API_BASE . 'categories/' . urlencode($category))
            );

        return $this->sendRequest($request);
    }

    /**
     * @param array $parameters
     * @return ResponseInterface
     */
    public function getCategoryResources($category, $parameters = [])
    {
        $request = $this->createRequest()
            ->withMethod('GET')
            ->withUri(
                $this->uri->withPath(self::API_BASE . 'categories/' . urlencode($category) . '/resources')
                    ->withQuery(http_build_query($parameters))
            );

        return $this->sendRequest($request);
    }

    /**
     * @param array $parameters
     * @return ResponseInterface
     */
    public function getResourcesList($parameters = [])
    {
        $request = $this->createRequest()
            ->withMethod('GET')
            ->withUri(
                $this->uri->withPath(self::API_BASE . 'resources')
                    ->withQuery(http_build_query($parameters))
            );

        return $this->sendRequest($request);
    }

    /**
     * @param $versions
     * @param array $parameters
     * @return ResponseInterface
     */
    public function getResourcesForVersions($versions, $parameters = [])
    {
        $request = $this->createRequest()
            ->withMethod('GET')
            ->withUri(
                $this->uri->withPath(self::API_BASE . 'resources/for/versions/' . urlencode($versions))
                    ->withQuery(http_build_query($parameters))
            );

        return $this->sendRequest($request);
    }

    /**
     * @param array $parameters
     * @return ResponseInterface
     */
    public function getNewResources($parameters = [])
    {
        $request = $this->createRequest()
            ->withMethod('GET')
            ->withUri(
                $this->uri->withPath(self::API_BASE . 'resources/new')
                    ->withQuery(http_build_query($parameters))
            );

        return $this->sendRequest($request);
    }

    /**
     * @param $resource
     * @return ResponseInterface
     */
    public function getResourceDetails($resource)
    {
        $request = $this->createRequest()
            ->withMethod('GET')
            ->withUri(
                $this->uri->withPath(self::API_BASE . 'resources/' . urlencode($resource))
            );

        return $this->sendRequest($request);
    }

    /**
     * @param $resource
     * @return ResponseInterface
     */
    public function getResourceAuthor($resource)
    {
        $request = $this->createRequest()
            ->withMethod('GET')
            ->withUri(
                $this->uri->withPath(self::API_BASE . 'resources/' . urlencode($resource) . '/author')
            );

        return $this->sendRequest($request);
    }

    /**
     * @param $resource
     * @param array $parameters
     * @return ResponseInterface
     */
    public function getResourceDownload($resource, $parameters = [])
    {
        $request = $this->createRequest()
            ->withMethod('GET')
            ->withUri(
                $this->uri->withPath(self::API_BASE . 'resources/' . urlencode($resource) . '/download')
                    ->withQuery(http_build_query($parameters))
            );

        return $this->sendRequest($request, false);
    }

    /**
     * @param $resource
     * @param array $parameters
     * @return ResponseInterface
     */
    public function getResourceReviews($resource, $parameters = [])
    {
        $request = $this->createRequest()
            ->withMethod('GET')
            ->withUri(
                $this->uri->withPath(self::API_BASE . 'resources/' . urlencode($resource) . '/reviews')
                    ->withQuery(http_build_query($parameters))
            );

        return $this->sendRequest($request);
    }

    /**
     * @param $resource
     * @param array $parameters
     * @return ResponseInterface
     */
    public function getResourceUpdates($resource, $parameters = [])
    {
        $request = $this->createRequest()
            ->withMethod('GET')
            ->withUri(
                $this->uri->withPath(self::API_BASE . 'resources/' . urlencode($resource) . '/updates')
                    ->withQuery(http_build_query($parameters))
            );

        return $this->sendRequest($request);
    }

    /**
     * @param $resource
     * @param array $parameters
     * @return ResponseInterface
     */
    public function getResourceVersions($resource, $parameters = [])
    {
        $request = $this->createRequest()
            ->withMethod('GET')
            ->withUri(
                $this->uri->withPath(self::API_BASE . 'resources/' . urlencode($resource) . '/versions')
                    ->withQuery(http_build_query($parameters))
            );

        return $this->sendRequest($request);
    }

    /**
     * @param $resource
     * @param $version
     * @return ResponseInterface
     */
    public function getResourceVersionDownload($resource, $version = 'latest')
    {
        $request = $this->createRequest()
            ->withMethod('GET')
            ->withUri(
                $this->uri->withPath(
                    self::API_BASE . 'resources/' . urlencode($resource) . '/versions/' . urlencode($version) . '/download'
                )
            );

        return $this->sendRequest($request);
    }

    /**
     * @param $query
     * @param array $parameters
     * @return ResponseInterface
     */
    public function getAuthorSearch($query, $parameters = [])
    {
        $request = $this->createRequest()
            ->withMethod('GET')
            ->withUri(
                $this->uri->withPath(self::API_BASE . 'search/authors/' . urlencode($query))
                    ->withQuery(http_build_query($parameters))
            );

        return $this->sendRequest($request);
    }

    /**
     * @param $query
     * @param array $parameters
     * @return ResponseInterface
     */
    public function getResourceSearch($query, $parameters = [])
    {
        $request = $this->createRequest()
            ->withMethod('GET')
            ->withUri(
                $this->uri->withPath(self::API_BASE . 'search/resources/' . urlencode($query))
                    ->withQuery(http_build_query($parameters))
            );

        return $this->sendRequest($request);
    }

    /**
     * @return ResponseInterface
     */
    public function getApiStatus()
    {
        $request = $this->createRequest()
            ->withMethod('GET')
            ->withUri(
                $this->uri->withPath(self::API_BASE . 'status')
            );

        return $this->sendRequest($request);
    }

    /**
     * @return ResponseInterface
     */
    public function getWebhookEvents()
    {
        $request = $this->createRequest()
            ->withMethod('GET')
            ->withUri(
                $this->uri->withPath(self::API_BASE . 'webhook/events')
            );

        return $this->sendRequest($request);
    }

    /**
     * @param $webhookId
     * @return ResponseInterface
     */
    public function getWebhookStatus($webhookId)
    {
        $request = $this->createRequest()
            ->withMethod('GET')
            ->withUri(
                $this->uri->withPath(self::API_BASE . 'webhook/status/' . urlencode($webhookId))
            );

        return $this->sendRequest($request);
    }

    /**
     * @param array $parameters
     * @return ResponseInterface
     */
    public function registerWebhook($parameters = [])
    {
        $request = $this->createRequest()
            ->withMethod('POST')
            ->withUri(
                $this->uri->withPath(self::API_BASE . 'webhook/register')
            )
            ->withHeader('Content-Type', 'application/x-www-form-urlencoded')
            ->withBody(
                $this->createStreamFor(http_build_query($parameters))
            );

        return $this->sendRequest($request);
    }

    /**
     * @param $webhookId
     * @param $secret
     * @return ResponseInterface
     */
    public function deleteWebhook($webhookId, $secret)
    {
        $request = $this->createRequest()
            ->withMethod('DELETE')
            ->withUri(
                $this->uri->withPath(
                    self::API_BASE . 'webhook/delete/' . urlencode($webhookId) . '/' . urlencode($secret)
                )
            );

        return $this->sendRequest($request);
    }
}
