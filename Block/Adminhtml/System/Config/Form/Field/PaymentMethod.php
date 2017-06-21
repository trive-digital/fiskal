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
use Magento\Payment\Api\PaymentMethodListInterface;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class PaymentMethod extends Select
{

    /**
     * Store manager
     *
     * @var array
     */
    private $storeManager;

    /**
     * Payment methods cache
     *
     * @var array
     */
    private $paymentMethods;

    /**
     * @var PaymentMethodListInterface
     */
    protected $paymentMethodRepository;

    /**
     * PaymentMethod constructor.
     *
     * @param Context                    $context
     * @param StoreManagerInterface      $storeManager
     * @param PaymentMethodListInterface $paymentMethodRepository
     * @param array                      $data
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        PaymentMethodListInterface $paymentMethodRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->storeManager = $storeManager;
        $this->paymentMethodRepository = $paymentMethodRepository;
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
     * Retrieve allowed payment methods
     *
     * @param int $paymentMethodCode return name by payment method code
     *
     * @return array|string
     */
    protected function getPaymentMethods($paymentMethodCode = null)
    {
        if ($this->paymentMethods === null) {
            $this->paymentMethods = [];
            foreach ($this->getStoreIds() as $storeId) {
                foreach ($this->paymentMethodRepository->getActiveList($storeId) as $item) {
                    if (!isset($this->paymentMethods[$item->getCode()])) {
                        $this->paymentMethods[$item->getCode()] = $item->getTitle();
                    }
                }
            }
        }
        if ($paymentMethodCode !== null) {
            return isset($this->paymentMethods[$paymentMethodCode]) ? $this->paymentMethods[$paymentMethodCode] : null;
        }

        return $this->paymentMethods;
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
            foreach ($this->getPaymentMethods() as $code => $title) {
                $this->addOption($code, addslashes($title));
            }
        }

        return parent::_toHtml();
    }
}
