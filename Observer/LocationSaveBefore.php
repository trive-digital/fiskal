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

namespace Trive\Fiskal\Observer;

use Magento\Framework\Event\ObserverInterface;
use Trive\Fiskal\Model\Config;
use Psr\Log\LoggerInterface;
use Trive\Fiskal\Model\Client\Location as LocationClient;
use Trive\Fiskal\Model\Client;
use Magento\Framework\Event\Observer;

class LocationSaveBefore implements ObserverInterface
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Client
     */
    protected $client;

    /**
     * LocationSaveBefore constructor.
     *
     * @param Config                      $config
     * @param LoggerInterface             $logger
     * @param array                       $data
     */
    public function __construct(
        Config $config,
        LoggerInterface $logger,
        $data = []
    ) {
        $this->config = $config;
        $this->client = new LocationClient($this->config, $logger, $data);
    }

    /**
     * Generate sequence for saved location
     *
     * @param Observer $observer
     *
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $location = $observer->getEvent()->getLocation();

        $response = $this->client->saveRequest($location);
        if ($response->getSuccessful()) {
            $location->setSyncedAt($this->client->getCurrentDateTime());
        }

        return $this;
    }
}
