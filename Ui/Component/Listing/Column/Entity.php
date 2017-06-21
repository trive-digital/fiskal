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

namespace Trive\Fiskal\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\Escaper;
use Trive\Fiskal\Api\Data\InvoiceInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Sales\Api\CreditmemoRepositoryInterface;
use Magento\Framework\UrlInterface;

class Entity extends Column
{
    /**
     * @var InvoiceRepositoryInterface
     */
    protected $invoiceRepository;

    /**
     * @var CreditmemoRepositoryInterface
     */
    protected $creditmemoRepository;

    /**
     * Url Builder
     *
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var Escaper
     */
    protected $escaper;

    /**
     * Entity constructor.
     *
     * @param ContextInterface              $context
     * @param UiComponentFactory            $uiComponentFactory
     * @param InvoiceRepositoryInterface    $invoiceRepository
     * @param CreditmemoRepositoryInterface $creditmemoRepository
     * @param UrlInterface                  $urlBuilder
     * @param Escaper                       $escaper
     * @param array                         $components
     * @param array                         $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        InvoiceRepositoryInterface $invoiceRepository,
        CreditmemoRepositoryInterface $creditmemoRepository,
        UrlInterface $urlBuilder,
        Escaper $escaper,
        array $components = [],
        array $data = []
    ) {
        $this->invoiceRepository = $invoiceRepository;
        $this->creditmemoRepository = $creditmemoRepository;
        $this->urlBuilder = $urlBuilder;
        $this->escaper = $escaper;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $identifier = $item[InvoiceInterface::ENTITY_ID];
                $type = $item[InvoiceInterface::ENTITY_TYPE];

                $object = null;
                $url = null;
                if ($type == InvoiceInterface::ENTITY_TYPE_INVOICE) {
                    $object = $this->invoiceRepository->get($identifier);
                    $url = $this->urlBuilder->getUrl(
                        'sales/order_invoice/view',
                        ['invoice_id' => $object->getEntityId()]
                    );
                } elseif ($type == InvoiceInterface::ENTITY_TYPE_CREDITMEMO) {
                    $object = $this->creditmemoRepository->get($identifier);
                    $url = $this->urlBuilder->getUrl(
                        'sales/order_creditmemo/view',
                        ['creditmemo_id' => $object->getEntityId()]
                    );
                }

                if ($object) {
                    $item[$this->getData('name')] = sprintf(
                        '<a href="%s" target="_blank">%s [#%s]</a>',
                        $url,
                        ucfirst($type),
                        $object->getIncrementId()
                    );
                }
            }
        }

        return $dataSource;
    }
}
