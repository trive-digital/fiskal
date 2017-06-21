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

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\NoSuchEntityException;
use Trive\Fiskal\Api\Data\SequenceInterface;
use Trive\Fiskal\Api\Data\SequenceSearchResultsInterfaceFactory;
use Trive\Fiskal\Api\SequenceRepositoryInterface;
use Trive\Fiskal\Model\ResourceModel\Sequence\Collection;
use Trive\Fiskal\Model\ResourceModel\Sequence\CollectionFactory as SequenceCollectionFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\CouldNotDeleteException;

class SequenceRepository implements SequenceRepositoryInterface
{
    /**
     * @var SequenceFactory
     */
    private $sequenceFactory;

    /**
     * @var SequenceCollectionFactory
     */
    private $sequenceCollectionFactory;

    /**
     * @var SequenceSearchResultsInterfaceFactory
     */
    private $searchResultFactory;

    /**
     * SequenceRepository constructor.
     *
     * @param SequenceFactory                       $sequenceFactory
     * @param SequenceCollectionFactory             $sequenceCollectionFactory
     * @param SequenceSearchResultsInterfaceFactory $searchResultFactory
     */
    public function __construct(
        SequenceFactory $sequenceFactory,
        SequenceCollectionFactory $sequenceCollectionFactory,
        SequenceSearchResultsInterfaceFactory $searchResultFactory
    ) {
        $this->sequenceFactory = $sequenceFactory;
        $this->sequenceCollectionFactory = $sequenceCollectionFactory;
        $this->searchResultFactory = $searchResultFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($identifier)
    {
        $sequence = $this->sequenceFactory->create();
        $sequence->getResource()->load($sequence, $identifier);
        if (!$sequence->getId()) {
            throw new NoSuchEntityException(__('Sequence not found.', $identifier));
        }

        return $sequence;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->sequenceCollectionFactory->create();

        $this->addFiltersToCollection($searchCriteria, $collection);
        $this->addSortOrdersToCollection($searchCriteria, $collection);
        $this->addPagingToCollection($searchCriteria, $collection);

        $collection->load();

        return $this->buildSearchResult($searchCriteria, $collection);
    }

    /**
     * {@inheritdoc}
     */
    public function save(SequenceInterface $sequence)
    {
        try {
            $sequence->getResource()->save($sequence);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(
                __($e->getMessage())
            );
        }

        return $sequence;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(SequenceInterface $sequence)
    {
        try {
            $sequence->getResource()->delete($sequence);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(
                __($e->getMessage())
            );
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($identifier)
    {
        $model = $this->getById($identifier);
        $this->delete($model);

        return true;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @param Collection              $collection
     */
    private function addFiltersToCollection(SearchCriteriaInterface $searchCriteria, Collection $collection)
    {
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            $fields = $conditions = [];
            foreach ($filterGroup->getFilters() as $filter) {
                $fields[] = $filter->getField();
                $conditions[] = [$filter->getConditionType() => $filter->getValue()];
            }
            $collection->addFieldToFilter($fields, $conditions);
        }
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @param Collection              $collection
     */
    private function addSortOrdersToCollection(SearchCriteriaInterface $searchCriteria, Collection $collection)
    {
        foreach ((array)$searchCriteria->getSortOrders() as $sortOrder) {
            $direction = $sortOrder->getDirection() == SortOrder::SORT_ASC ? 'asc' : 'desc';
            $collection->addOrder($sortOrder->getField(), $direction);
        }
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @param Collection              $collection
     */
    private function addPagingToCollection(SearchCriteriaInterface $searchCriteria, Collection $collection)
    {
        $collection->setPageSize($searchCriteria->getPageSize());
        $collection->setCurPage($searchCriteria->getCurrentPage());
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @param Collection              $collection
     *
     * @return mixed
     */
    private function buildSearchResult(SearchCriteriaInterface $searchCriteria, Collection $collection)
    {
        $searchResults = $this->searchResultFactory->create();

        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }
}
