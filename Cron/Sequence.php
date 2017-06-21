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
use Trive\Fiskal\Api\LocationRepositoryInterface;
use Trive\Fiskal\Api\SequenceRepositoryInterface;
use Trive\Fiskal\Api\Data\SequenceInterfaceFactory;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Sequence
{
    /**
     * @var LocationRepositoryInterface
     */
    protected $locationRepository;

    /**
     * @var SequenceRepositoryInterface
     */
    protected $sequenceRepository;

    /**
     * @var SequenceInterfaceFactory $sequenceFactory
     */
    protected $sequenceFactory;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var SortOrderBuilder
     */
    private $sortOrderBuilder;

    /**
     * Sequence constructor.
     *
     * @param FilterBuilder               $filterBuilder
     * @param SearchCriteriaBuilder       $searchCriteriaBuilder
     * @param SortOrderBuilder            $sortOrderBuilder
     * @param LocationRepositoryInterface $locationRepository
     * @param SequenceRepositoryInterface $sequenceRepository
     * @param SequenceInterfaceFactory    $sequenceFactory
     */
    public function __construct(
        FilterBuilder $filterBuilder,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder,
        LocationRepositoryInterface $locationRepository,
        SequenceRepositoryInterface $sequenceRepository,
        SequenceInterfaceFactory $sequenceFactory
    ) {
        $this->filterBuilder = $filterBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->locationRepository = $locationRepository;
        $this->sequenceRepository = $sequenceRepository;
        $this->sequenceFactory = $sequenceFactory;
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

    /**
     * Create sequences for each location
     *
     * @return $this
     */
    private function createSequences()
    {
        $searchResults = $this->locationRepository->getList($this->searchCriteriaBuilder->create())->getItems();

        foreach (array_keys($searchResults) as $locationId) {
            $sequence = $this->sequenceFactory->create();
            $sequence->setLocationId($locationId);
            $sequence->setYear($this->getCurrentYear());
            $this->sequenceRepository->save($sequence);
        }

        return $this;
    }
}
