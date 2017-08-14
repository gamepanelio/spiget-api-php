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
     */
    public function __construct($userAgent = self::DEFAULT_USER_AGENT)
    {
        $this->uri = UriFactoryDiscovery::find()->createUri(self::API_HOST)
            ->withHost(self::API_HOST)
            ->withScheme(self::API_SCHEME)
            ->withPath(self::API_BASE);
        $this->httpClient = HttpClientDiscovery::find();
        $this->userAgent = $userAgent;
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
     * @param bool $decodeJson
     * @return array|StreamInterface
     */
    private function sendRequest(RequestInterface $request, $decodeJson = true)
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

            if ($decodeJson) {
                return json_decode($response->getBody(), true);
            } else {
                return $response->getBody();
            }
        } catch (TransferException $e) {
            throw ApiCommunicationException::wrap($e);
        }
    }

    /**
     * @param array $parameters
     * @return array
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
     * @return array
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
     * @return array
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
     * @return array
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
     * @return array
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
     * @return array
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
     * @return array
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
     * @return array
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
     * @return array
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
     * @return array
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
     * @return array
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
     * @return array
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
     * @return array
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
     * @return array
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
     * @return array
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
     * @return array
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
     * @return array
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

        return $this->sendRequest($request, false);
    }

    /**
     * @param $query
     * @param array $parameters
     * @return array
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
     * @return array
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
     * @return array
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
     * @return array
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
     * @return array
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
     * @return array
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
     * @return array
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
