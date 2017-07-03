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

namespace Trive\Fiskal\Cron;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Trive\Fiskal\Model\Config;
use Magento\Store\Model\StoreManagerInterface;
use Trive\Fiskal\Api\SequenceRepositoryInterface;
use Trive\Fiskal\Api\Data\SequenceInterface;
use Trive\Fiskal\Api\Data\SequenceInterfaceFactory;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Sequence
{
    /**
     * @var FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var SortOrderBuilder
     */
    protected $sortOrderBuilder;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var SequenceRepositoryInterface
     */
    protected $sequenceRepository;

    /**
     * @var SequenceInterfaceFactory $sequenceFactory
     */
    protected $sequenceFactory;

    /**
     * Sequence constructor.
     *
     * @param FilterBuilder               $filterBuilder
     * @param SearchCriteriaBuilder       $searchCriteriaBuilder
     * @param SortOrderBuilder            $sortOrderBuilder
     * @param Config                      $config
     * @param StoreManagerInterface       $storeManager
     * @param SequenceRepositoryInterface $sequenceRepository
     * @param SequenceInterfaceFactory    $sequenceFactory
     */
    public function __construct(
        FilterBuilder $filterBuilder,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder,
        Config $config,
        StoreManagerInterface $storeManager,
        SequenceRepositoryInterface $sequenceRepository,
        SequenceInterfaceFactory $sequenceFactory
    ) {
        $this->filterBuilder = $filterBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->config = $config;
        $this->storeManager = $storeManager;
        $this->sequenceRepository = $sequenceRepository;
        $this->sequenceFactory = $sequenceFactory;
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
     * Create sequences for each store
     *
     * @return $this
     */
    private function createSequences()
    {
        foreach ($this->storeManager->getStores() as $store) {
            $this->config->setStoreId($store->getId());

            /** @var SequenceInterface $sequence */
            $sequence = $this->sequenceFactory->create();
            $sequence->setLocationCode($this->config->getLocationCode());
            $sequence->setYear($this->getCurrentYear());
            $this->sequenceRepository->save($sequence);
        }

        return $this;
    }

    /**
     * Generate sequences for each location
     *
     * @see \Trive\Fiskal\Api\Data\InvoiceInterface
     *
     * @return void
     */
    public function execute()
    {
        $this->createSequences();
    }
}
