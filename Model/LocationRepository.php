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
use Trive\Fiskal\Api\Data\LocationInterface;
use Trive\Fiskal\Api\Data\LocationSearchResultsInterfaceFactory;
use Trive\Fiskal\Api\LocationRepositoryInterface;
use Trive\Fiskal\Model\ResourceModel\Location\Collection;
use Trive\Fiskal\Model\ResourceModel\Location\CollectionFactory as LocationCollectionFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\CouldNotDeleteException;

class LocationRepository implements LocationRepositoryInterface
{
    /**
     * @var LocationFactory
     */
    private $locationFactory;

    /**
     * @var LocationCollectionFactory
     */
    private $locationCollectionFactory;

    /**
     * @var LocationSearchResultsInterfaceFactory
     */
    private $searchResultFactory;

    /**
     * LocationRepository constructor.
     *
     * @param LocationFactory                       $locationFactory
     * @param LocationCollectionFactory             $locationCollectionFactory
     * @param LocationSearchResultsInterfaceFactory $searchResultFactory
     */
    public function __construct(
        LocationFactory $locationFactory,
        LocationCollectionFactory $locationCollectionFactory,
        LocationSearchResultsInterfaceFactory $searchResultFactory
    ) {
        $this->locationFactory = $locationFactory;
        $this->locationCollectionFactory = $locationCollectionFactory;
        $this->searchResultFactory = $searchResultFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($locationId)
    {
        $location = $this->locationFactory->create();
        $location->getResource()->load($location, $locationId);
        if (!$location->getId()) {
            throw new NoSuchEntityException(__('Location not found.', $locationId));
        }

        return $location;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->locationCollectionFactory->create();

        $this->addFiltersToCollection($searchCriteria, $collection);
        $this->addSortOrdersToCollection($searchCriteria, $collection);
        $this->addPagingToCollection($searchCriteria, $collection);

        $collection->load();

        return $this->buildSearchResult($searchCriteria, $collection);
    }

    /**
     * {@inheritdoc}
     */
    public function save(LocationInterface $location)
    {
        try {
            $location->getResource()->save($location);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(
                __($e->getMessage())
            );
        }

        return $location;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(LocationInterface $location)
    {
        try {
            $location->getResource()->delete($location);
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
