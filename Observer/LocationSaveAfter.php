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
use Psr\Log\LoggerInterface;
use Trive\Fiskal\Api\Data\SequenceInterfaceFactory;
use Trive\Fiskal\Api\SequenceRepositoryInterface;
use Magento\Framework\Event\Observer;
use Trive\Fiskal\Api\Data\SequenceInterface;

class LocationSaveAfter implements ObserverInterface
{
    /**
     * @var SequenceInterfaceFactory
     */
    protected $sequenceDataFactory;

    /**
     * @var SequenceRepositoryInterface
     */
    protected $sequenceRepository;

    /**
     * LocationSaveAfter constructor.
     *
     * @param LoggerInterface             $logger
     * @param SequenceInterfaceFactory    $sequenceDataFactory
     * @param SequenceRepositoryInterface $sequenceRepository
     * @param array                       $data
     */
    public function __construct(
        LoggerInterface $logger,
        SequenceInterfaceFactory $sequenceDataFactory,
        SequenceRepositoryInterface $sequenceRepository,
        $data = []
    ) {
        $this->sequenceDataFactory = $sequenceDataFactory;
        $this->sequenceRepository = $sequenceRepository;
    }

    /**
     * Get current year
     *
     * @return string
     */
    protected function getCurrentYear()
    {
        $date = new \DateTime();

        return $date->format('Y');
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

        if($location->isObjectNew()) {
            $this->generateSequence($location->getId());
        }

        return $this;
    }

    /**
     * Generate sequence for location
     *
     * @param $locationId
     *
     * @return SequenceInterface
     */
    protected function generateSequence($locationId)
    {
        /** @var SequenceInterface $sequence */
        $sequence = $this->sequenceDataFactory->create();
        $sequence->setLocationId($locationId);
        $sequence->setYear($this->getCurrentYear());

        $this->sequenceRepository->save($sequence);

        return $sequence;
    }
}
