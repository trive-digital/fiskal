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
use Trive\Fiskal\Api\InvoiceRepositoryInterface as FiskalInvoiceRepositoryInterface;
use Trive\Fiskal\Api\LocationRepositoryInterface;
use Trive\Fiskal\Api\SequenceRepositoryInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Sales\Api\CreditmemoRepositoryInterface;
use Magento\Sales\Api\InvoiceManagementInterface;
use Magento\Sales\Api\CreditmemoManagementInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Tax\Helper\Data as TaxHelper;
use Magento\Tax\Api\TaxClassRepositoryInterface;
use Psr\Log\LoggerInterface;
use Trive\Fiskal\Model\Client\Invoice as InvoiceClient;
use Trive\Fiskal\Api\Data\InvoiceInterface;
use Magento\Framework\Data\Collection;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Sync
{
    /**
     * @var FiskalInvoiceRepositoryInterface
     */
    protected $fiskalInvoiceRepository;

    /**
     * @var LocationRepositoryInterface
     */
    protected $locationRepository;

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
     * @var Config
     */
    protected $config;

    /**
     * @var \Trive\Fiskal\Model\Client;
     */
    protected $client;

    /**
     * Sync constructor.
     *
     * @param FilterBuilder                    $filterBuilder
     * @param SearchCriteriaBuilder            $searchCriteriaBuilder
     * @param SortOrderBuilder                 $sortOrderBuilder
     * @param Config                           $config
     * @param FiskalInvoiceRepositoryInterface $fiskalInvoiceRepository
     * @param LocationRepositoryInterface      $locationRepository
     * @param SequenceRepositoryInterface      $sequenceRepository
     * @param InvoiceRepositoryInterface       $invoiceRepository
     * @param CreditmemoRepositoryInterface    $creditmemoRepository
     * @param InvoiceManagementInterface       $invoiceManagementInterface
     * @param CreditmemoManagementInterface    $creditmemoManagementInterface
     * @param TaxHelper                        $taxHelper
     * @param TaxClassRepositoryInterface      $taxClassRepository
     * @param LoggerInterface                  $logger
     * @param array                            $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        FilterBuilder $filterBuilder,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder,
        Config $config,
        FiskalInvoiceRepositoryInterface $fiskalInvoiceRepository,
        LocationRepositoryInterface $locationRepository,
        SequenceRepositoryInterface $sequenceRepository,
        InvoiceRepositoryInterface $invoiceRepository,
        CreditmemoRepositoryInterface $creditmemoRepository,
        InvoiceManagementInterface $invoiceManagementInterface,
        CreditmemoManagementInterface $creditmemoManagementInterface,
        DateTime $dateTime,
        TaxHelper $taxHelper,
        TaxClassRepositoryInterface $taxClassRepository,
        LoggerInterface $logger,
        $data = []
    ) {
        $this->filterBuilder = $filterBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->fiskalInvoiceRepository = $fiskalInvoiceRepository;

        $this->client = new InvoiceClient(
            $config,
            $fiskalInvoiceRepository,
            $locationRepository,
            $sequenceRepository,
            $invoiceRepository,
            $creditmemoRepository,
            $invoiceManagementInterface,
            $creditmemoManagementInterface,
            $dateTime,
            $taxHelper,
            $taxClassRepository,
            $filterBuilder,
            $searchCriteriaBuilder,
            $logger,
            $data
        );
    }

    /**
     * Sync invoices (can be of type invoice, creditmemo)
     *
     * @see InvoiceInterface
     *
     * @return void
     */
    public function execute()
    {
        $items = $this->getItems();
        foreach ($items as $invoice) {
            $this->client->saveRequest($invoice);
        }
    }

    /**
     * Get items to sync
     *
     * @return InvoiceInterface[]
     */
    private function getItems()
    {
        $this->searchCriteriaBuilder->addFilters(
            [
                $this->filterBuilder->setField('synced_at')->setConditionType('null')->create()
            ]
        );
        $this->searchCriteriaBuilder->addSortOrder(
            $this->sortOrderBuilder->setField('created_at')->setDirection(
                Collection::SORT_ORDER_ASC
            )->create()
        );
        $searchCriteria = $this->searchCriteriaBuilder->create();

        $result = $this->fiskalInvoiceRepository->getList($searchCriteria);

        return $result->getItems();
    }
}
