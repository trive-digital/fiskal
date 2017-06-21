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
use Magento\Framework\Message\ManagerInterface;
use Trive\Fiskal\Model\Client\Location as LocationClient;
use Magento\Framework\Event\Observer;

class LocationDeleteBefore implements ObserverInterface
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var \Trive\Fiskal\Model\Client;
     */
    protected $client;

    /**
     * Message manager interface
     *
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * LocationDeleteBefore constructor.
     *
     * @param Config           $config
     * @param LoggerInterface  $logger
     * @param ManagerInterface $messageManager
     * @param array            $data
     */
    public function __construct(
        Config $config,
        LoggerInterface $logger,
        ManagerInterface $messageManager,
        $data = []
    ) {
        $this->config = $config;
        $this->messageManager = $messageManager;
        $this->client = new LocationClient($this->config, $logger, $data);
    }

    public function execute(Observer $observer)
    {
        $location = $observer->getEvent()->getData('location');
        $this->client->deleteRequest($location);

        return $this;
    }
}
