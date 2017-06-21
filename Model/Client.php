<?php
/**
 * Trive Fiskal API Library.
 *
 * @category  Trive
 * @package   Trive_Fiskal
 * @copyright 2017 Trive d.o.o (http://trive.digital)
 * @license   http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link      http://trive.digital
 */

namespace Trive\Fiskal\Model;

use Psr\Log\LoggerInterface;
use Trive\FiskalAPI\Client as API;
use Trive\FiskalAPI\Request;
use Trive\FiskalAPI\Request\BusinessLocationRequest;
use Trive\FiskalAPI\Request\InvoiceRequest;
use Magento\Framework\DataObject;

class Client extends DataObject
{
    /**
     * Logger.
     *
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Config object.
     *
     * @var Config
     */
    protected $config;

    /**
     * API Client.
     *
     * @var API;
     */
    protected $client;

    /**
     * Last request.
     *
     * @var string
     */
    protected $lastRequest;

    /**
     * Last response.
     *
     * @var string
     */
    protected $lastResponse;

    /**
     * Client constructor.
     *
     * @param Config          $config
     * @param LoggerInterface $logger
     * @param array           $data
     */
    public function __construct(
        Config $config,
        LoggerInterface $logger,
        $data = []
    ) {
        $this->config = $config;
        $this->logger = $logger;
        $this->client = new API(
            $this->config->getCertificate(),
            $this->config->getCertificatePassword(),
            false,
            $this->config->getDemo(),
            $this->config->getDebug()
        );

        parent::__construct($data);
    }

    /**
     * Get last request
     *
     * @return string
     */
    public function getLastRequest()
    {
        return $this->client->getLastRequest();
    }

    /**
     * Get last response
     *
     * @return string
     */
    public function getLastResponse()
    {
        $response = $this->client->getLastResponse();

        return $response;

    }

    /**
     * Get current year
     *
     * @return string
     */
    public function getCurrentYear()
    {
        $date = new \DateTime();

        return $date->format('Y');
    }

    /**
     * Get current date
     *
     * @return string
     */
    public function getCurrentDate()
    {
        $date = new \DateTime();

        return $date->format('d.m.Y');
    }

    /**
     * Get current datetime
     *
     * @return string
     */
    public function getCurrentDateTime()
    {
        $date = new \DateTime();

        return $date->format('d.m.Y\TH:i:s');
    }

    /**
     * Request success flag
     *
     * @return bool
     */
    public function isSuccessful()
    {
        return $this->client->isSuccessful();
    }

    /**
     * @param Request $request
     *
     * @return DataObject
     */
    public function sendRequest($request)
    {
        $this->client->sendRequest($request);

        $return = new DataObject(
            [
                'successful' => $this->isSuccessful(),
                'request'    => $this->getLastRequest(),
                'response'   => $this->getLastResponse()
            ]
        );

        if ($this->client->isDebug()) {
            $this->logger->debug($return->toString());
        }

        return $return;
    }

    /**
     * Send location request
     *
     * @param $businessLocation
     *
     * @return DataObject
     */
    public function locationRequest($businessLocation)
    {
        $request = new BusinessLocationRequest($businessLocation);

        return $this->sendRequest($request);
    }

    /**
     * Send invoice request
     *
     * @param $invoice
     *
     * @return DataObject
     */
    public function invoiceRequest($invoice)
    {
        $request = new InvoiceRequest($invoice);

        return $this->sendRequest($request);
    }
}