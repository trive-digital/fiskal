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

namespace Trive\Fiskal\Model\Client;

use Trive\Fiskal\Model\Client;
use Trive\Fiskal\Model\Config;
use Psr\Log\LoggerInterface;
use Trive\FiskalAPI\Business\Address;
use Trive\FiskalAPI\Business\AddressData;
use Trive\FiskalAPI\Business\BusinessLocation;
use Trive\Fiskal\Api\Data\LocationInterface;
use Magento\Framework\DataObject;

class Location extends Client
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Address;
     */
    protected $address;

    /**
     * @var AddressData;
     */
    protected $addressData;

    /**
     * @var BusinessLocation;
     */
    protected $businessLocation;

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
        $this->address = new Address();
        $this->addressData = new AddressData();
        $this->businessLocation = new BusinessLocation();

        parent::__construct($config, $logger, $data);
    }

    /**
     * Save location
     *
     * @param LocationInterface $location
     *
     * @return DataObject
     */
    public function saveRequest($location)
    {
        if ($location->getUseFullAddress()) {
            $this->address->setStreet($location->getStreet())
                          ->setHouseNumber($location->getHouseNumber())
                          ->setHouseNumberSuffix($location->getHouseNumberSuffix())
                          ->setZipCode($location->getZipCode())
                          ->setSettlement($location->getSettlement())
                          ->setCity($location->getCity());
            $this->addressData->setAddress($this->address);
        }

        if (!$location->getUseFullAddress()) {
            $this->addressData->setOtherTypeOfBusinessLocation($location->getOtherType());
        }

        $this->businessLocation->setOib($this->config->getOib())->setAddressData($this->addressData)->setDateOfUsage(
            $this->getCurrentDate()
        )->setBusinessLocationCode($location->getCode())->setBusinessHours($location->getBusinessHours());

        return $this->locationRequest($this->businessLocation);
    }

    /**
     * Delete location
     *
     * @param LocationInterface $location
     *
     * @return DataObject
     */
    public function deleteRequest($location)
    {
        if ($location->getUseFullAddress()) {
            $this->address->setStreet($location->getStreet())
                          ->setHouseNumber($location->getHouseNumber())
                          ->setHouseNumberSuffix($location->getHouseNumberSuffix())
                          ->setZipCode($location->getZipCode())
                          ->setSettlement($location->getSettlement())
                          ->setCity($location->getCity());
            $this->addressData->setAddress($this->address);
        }

        if (!$location->getUseFullAddress()) {
            $this->addressData->setOtherTypeOfBusinessLocation($location->getOtherType());
        }

        $this->businessLocation->setOib($this->config->getOib())
                               ->setAddressData($this->addressData)
                               ->setDateOfUsage(
                                   $this->getCurrentDate()
                               )
                               ->setBusinessLocationCode($location->getCode())
                               ->setBusinessHours($location->getBusinessHours())
                               ->setClosingCode(BusinessLocation::CLOSING_CODE_CLOSED);

        return $this->locationRequest($this->businessLocation);
    }
}