<?php
namespace PhpInsights;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;

class InsightsCaller
{

    const STRATEGY_MOBILE = 'mobile';

    const STRATEGY_DESKTOP = 'desktop';

    const GI_API_ENDPOINT = 'https://www.googleapis.com/pagespeedonline/v2/runPagespeed?url=%s&strategy=%s&key=%s&locale=%s&screenshot=%s';

    /** @var string */
    private $apiKey;

    /** @var string */
    private $locale;

    /** @var bool */
    private $captureScreenshot;

    /** @var Client */
    private $client;

    /**
     * InsightsCaller constructor.
     *
     * @param string $apiKey
     * @param string $locale
     */
    public function __construct($apiKey, $locale = 'en')
    {
        $this->client = new Client();
        $this->apiKey = $apiKey;
        $this->locale = $locale;
        $this->captureScreenshot = true;

    }

    /**
     * @param string $url
     * @param string $strategy
     *
     * @return InsightsResponse
     */
    public function __invoke($url, $strategy = self::STRATEGY_MOBILE)
    {
        return $this->getResponse($url, $strategy);
    }


    /**
     * @param string $url
     * @param string $strategy
     *
     * @return InsightsResponse
     *
     * @throws ApiRequestException
     */
    public function getResponse($url, $strategy = 'mobile')
    {
        $apiEndpoint = $this->createApiEndpointUrl($url, $strategy);

        try {
            $response = $this->client->request('GET', $apiEndpoint);
        } catch (TransferException $e) {
            throw new ApiRequestException($e->getMessage());
        }

        return InsightsResponse::fromResponse($response);

    }

    /**
     * @return boolean
     */
    public function isCaptureScreenshot()
    {
        return $this->captureScreenshot;
    }

    /**
     * @param boolean $captureScreenshot
     */
    public function setCaptureScreenshot($captureScreenshot)
    {
        $this->captureScreenshot = $captureScreenshot;
    }


    /**
     * @param string $url
     * @param string $strategy
     *
     * @return string
     */
    protected function createApiEndpointUrl($url, $strategy = 'mobile')
    {
        $screenshot = ($this->isCaptureScreenshot()) ? 'true' : 'false';

        return sprintf(self::GI_API_ENDPOINT, $url, $strategy, $this->apiKey, $this->locale, $screenshot);
    }


}