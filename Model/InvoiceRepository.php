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
use Trive\Fiskal\Api\Data\InvoiceInterface;
use Trive\Fiskal\Api\Data\InvoiceSearchResultsInterfaceFactory;
use Trive\Fiskal\Api\InvoiceRepositoryInterface;
use Trive\Fiskal\Model\ResourceModel\Invoice\Collection;
use Trive\Fiskal\Model\ResourceModel\Invoice\CollectionFactory as InvoiceCollectionFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\CouldNotDeleteException;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class InvoiceRepository implements InvoiceRepositoryInterface
{
    /**
     * @var InvoiceFactory
     */
    private $invoiceFactory;

    /**
     * @var InvoiceCollectionFactory
     */
    private $invoiceCollectionFactory;

    /**
     * @var InvoiceSearchResultsInterfaceFactory
     */
    private $searchResultFactory;

    /**
     * InvoiceRepository constructor.
     *
     * @param InvoiceFactory                       $invoiceFactory
     * @param InvoiceCollectionFactory             $invoiceCollectionFactory
     * @param InvoiceSearchResultsInterfaceFactory $searchResultsFactory
     */
    public function __construct(
        InvoiceFactory $invoiceFactory,
        InvoiceCollectionFactory $invoiceCollectionFactory,
        InvoiceSearchResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->invoiceFactory = $invoiceFactory;
        $this->invoiceCollectionFactory = $invoiceCollectionFactory;
        $this->searchResultFactory = $searchResultsFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($identifier)
    {
        $invoice = $this->invoiceFactory->create();
        $invoice->getResource()->load($invoice, $identifier);
        if (!$invoice->getId()) {
            throw new NoSuchEntityException(__('Invoice not found.', $identifier));
        }

        return $invoice;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->invoiceCollectionFactory->create();

        $this->addFiltersToCollection($searchCriteria, $collection);
        $this->addSortOrdersToCollection($searchCriteria, $collection);
        $this->addPagingToCollection($searchCriteria, $collection);

        $collection->load();

        return $this->buildSearchResult($searchCriteria, $collection);
    }

    /**
     * {@inheritdoc}
     */
    public function save(InvoiceInterface $invoice)
    {
        try {
            $invoice->getResource()->save($invoice);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(
                __($e->getMessage())
            );
        }

        return $invoice;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(InvoiceInterface $invoice)
    {
        try {
            $invoice->getResource()->delete($invoice);
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
     * @return \Trive\Fiskal\Api\Data\InvoiceSearchResultsInterface
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
