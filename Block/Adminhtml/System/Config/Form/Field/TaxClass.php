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

namespace Trive\Fiskal\Block\Adminhtml\System\Config\Form\Field;

use Magento\Framework\View\Element\Html\Select;
use Magento\Framework\View\Element\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Tax\Model\TaxClass\Source\Product;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class TaxClass extends Select
{
    /**
     * Store manager
     *
     * @var array
     */
    private $storeManager;

    /**
     * Tax classes cache
     *
     * @var array
     */
    private $taxClasses;

    /**
     * @var \Magento\Tax\Model\TaxClass\Source\Product
     */
    protected $productTaxClassSource;

    /**
     * TaxClass constructor.
     *
     * @param Context               $context
     * @param StoreManagerInterface $storeManager
     * @param Product               $productTaxClassSource
     * @param array                 $data
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        Product $productTaxClassSource,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->storeManager = $storeManager;
        $this->productTaxClassSource = $productTaxClassSource;
    }

    /**
     * Retrieve current store ids from scope
     *
     * @return array
     */
    protected function getStoreIds()
    {
        $storeIds = [0];
        if ($this->getRequest()->getParam('website')) {
            $storeIds = $this->storeManager->getWebsite($this->getRequest()->getParam('website'))->getStoreIds();
        }

        return $storeIds;
    }

    /**
     * Retrieve allowed tax classes
     *
     * @param int $taxClassCode return name by payment method code
     *
     * @return array|string
     */
    protected function getTaxClasses($taxClassCode = null)
    {
        if ($this->taxClasses === null) {
            $this->taxClasses = [];
            foreach ($this->productTaxClassSource->getAllOptions(false) as $item) {
                if (!isset($this->taxClasses[$item['value']])) {
                    $this->taxClasses[$item['value']] = $item['label'];
                }
            }
        }
        if ($taxClassCode !== null) {
            return isset($this->taxClasses[$taxClassCode]) ? $this->taxClasses[$taxClassCode] : null;
        }

        return $this->taxClasses;
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {
            foreach ($this->getTaxClasses() as $code => $title) {
                $this->addOption($code, addslashes($title));
            }
        }

        return parent::_toHtml();
    }
}
