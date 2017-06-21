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

namespace Trive\Fiskal\Controller\Adminhtml\Location;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Trive\Fiskal\Model\LocationFactory;
use Trive\Fiskal\Api\LocationRepositoryInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Trive\Fiskal\Api\Data\LocationInterface;

class Save extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Trive_Fiskal::fiskal_location';

    /**
     * @var LocationFactory $locationFactory
     */
    private $locationFactory;

    /**
     * @var LocationRepositoryInterface $locationRepository
     */
    private $locationRepository;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * Save constructor.
     *
     * @param Context                     $context
     * @param LocationFactory             $locationFactory
     * @param LocationRepositoryInterface $locationRepository
     * @param DataPersistorInterface      $dataPersistor
     */
    public function __construct(
        Context $context,
        LocationFactory $locationFactory,
        LocationRepositoryInterface $locationRepository,
        DataPersistorInterface $dataPersistor
    ) {
        $this->locationFactory = $locationFactory;
        $this->locationRepository = $locationRepository;
        $this->dataPersistor = $dataPersistor;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        // check if data sent
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $locationId = $this->getRequest()->getParam('location_id');

            if (empty($data['location_id'])) {
                $data['location_id'] = null;
            }

            /** @var LocationInterface $location */
            $location = $this->locationFactory->create();
            if ($locationId) {
                $location->getResource()->load($location, $locationId);
            }

            if (!$location->getId() && $locationId) {
                $this->messageManager->addErrorMessage(__('This location no longer exists.'));

                return $resultRedirect->setPath('*/*/');
            }

            if ($data['use_full_address']) {
                $location->setStreet($data['street']);
                $location->setHouseNumber($data['house_number']);
                $location->setHouseNumberSuffix($data['house_number_suffix']);
                $location->setZipCode($data['zip_code']);
                $location->setSettlement($data['settlement']);
                $location->setCity($data['city']);
                $location->setOtherType(null);
            } else {
                $location->setOtherType($data['other_type']);
                $location->setStreet(null);
                $location->setHouseNumber(null);
                $location->setHouseNumberSuffix(null);
                $location->setZipCode(null);
                $location->setSettlement(null);
                $location->setCity(null);
            }

            $location->setCode($data['code']);
            $location->setPaymentDeviceCode($data['payment_device_code']);
            $location->setBusinessHours($data['business_hours']);
            $location->setValidFrom($data['valid_from']);

            try {
                $this->locationRepository->save($location);
                $this->getMessageManager()->addSuccessMessage(__('You saved the location.'));
                $this->dataPersistor->clear('trive_fiskal_location');
            } catch (LocalizedException $exception) {
                $this->getMessageManager()->addErrorMessage($exception->getMessage());
                $this->dataPersistor->set('trive_fiskal_location', $data);

                return $resultRedirect->setPath('*/*/edit', ['location_id' => $location->getId()]);
            } catch (\Exception $exception) {
                $this->getMessageManager()->addErrorMessage($exception->getMessage());
                $this->dataPersistor->set('trive_fiskal_location', $data);

                return $resultRedirect->setPath('*/*/edit', ['location_id' => $location->getId()]);
            }

            // check if 'Save and Continue'
            if ($this->getRequest()->getParam('back')) {
                return $resultRedirect->setPath('*/*/edit', ['location_id' => $location->getId()]);
            }
        }

        return $resultRedirect->setPath('*/*/');
    }
}
