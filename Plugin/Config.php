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

namespace Trive\Fiskal\Plugin;

use Trive\Fiskal\Model\Config as FiskalConfig;
use Magento\Config\Model\Config as StoreConfig;
use Trive\Fiskal\Api\Data\SequenceInterfaceFactory;
use Trive\Fiskal\Api\Data\SequenceInterface;
use Trive\Fiskal\Api\SequenceRepositoryInterface;

class Config
{
    /**
     * Fiskal config
     *
     * @var FiskalConfig
     */
    protected $fiskalConfig;

    /**
     * @var SequenceInterfaceFactory
     */
    protected $sequenceFactory;

    /**
     * @var SequenceRepositoryInterface
     */
    protected $sequenceRepository;

    /**
     * Config constructor.
     *
     * @param FiskalConfig                $fiskalConfig
     * @param SequenceInterfaceFactory    $sequenceFactory
     * @param SequenceRepositoryInterface $sequenceRepository
     */
    public function __construct(
        FiskalConfig $fiskalConfig,
        SequenceInterfaceFactory $sequenceFactory,
        SequenceRepositoryInterface $sequenceRepository
    ) {
        $this->fiskalConfig = $fiskalConfig;
        $this->sequenceFactory = $sequenceFactory;
        $this->sequenceRepository = $sequenceRepository;
    }

    /**
     * Get current year
     *
     * @return string
     */
    private function getCurrentYear()
    {
        $date = new \DateTime();

        return $date->format('Y');
    }

    /**
     * Generate sequence for location
     *
     * @param string $locationCode
     *
     * @return SequenceInterface
     */
    private function createSequences($locationCode)
    {
        /** @var SequenceInterface $sequence */
        $sequence = $this->sequenceFactory->create();
        $sequence->setLocationCode($locationCode);
        $sequence->setYear($this->getCurrentYear());
        try {
            $this->sequenceRepository->save($sequence);
        } catch (\Exception $e) {
        }

        return $sequence;
    }

    /**
     * Generate invoice sequence on config save
     *
     * @param StoreConfig $subject
     *
     * @return mixed
     */
    public function afterSave(
        StoreConfig $subject
    ) {
        $this->fiskalConfig->setStoreId($subject->getStore());
        $this->createSequences($this->fiskalConfig->getLocationCode());

        return $subject;
    }
}
