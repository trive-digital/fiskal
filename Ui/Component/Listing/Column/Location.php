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
use Trive\Fiskal\Api\LocationRepositoryInterface;

class Location extends Column
{
    /**
     * @var LocationRepositoryInterface
     */
    protected $locationRepository;

    /**
     * @var Escaper
     */
    protected $escaper;

    /**
     * Location constructor.
     *
     * @param ContextInterface            $context
     * @param UiComponentFactory          $uiComponentFactory
     * @param LocationRepositoryInterface $locationRepository
     * @param Escaper                     $escaper
     * @param array                       $components
     * @param array                       $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        LocationRepositoryInterface $locationRepository,
        Escaper $escaper,
        array $components = [],
        array $data = []
    ) {
        $this->locationRepository = $locationRepository;
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
                $identifier = $item[InvoiceInterface::LOCATION_ID];
                $location = $this->locationRepository->getById($identifier);

                if ($location && $location->getId()) {
                    $item[$this->getData('name')] = $location->getCode();
                }
            }
        }

        return $dataSource;
    }
}
