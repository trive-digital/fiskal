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
use Magento\Store\Model\StoreManagerInterface;

class Store extends Column
{
    /**
     * @var Escaper
     */
    protected $escaper;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Entity constructor.
     *
     * @param ContextInterface      $context
     * @param UiComponentFactory    $uiComponentFactory
     * @param StoreManagerInterface $storeManager
     * @param Escaper               $escaper
     * @param array                 $components
     * @param array                 $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        StoreManagerInterface $storeManager,
        Escaper $escaper,
        array $components = [],
        array $data = []
    ) {
        $this->storeManager = $storeManager;
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
                $identifier = $item[InvoiceInterface::STORE_ID];
                $store = $this->storeManager->getStore($identifier);
                $name = [$store->getWebsite()->getName(), $store->getGroup()->getName(), $store->getName()];

                $item[$this->getData('name')] = implode('<br/>', $name);
            }
        }

        return $dataSource;
    }
}
