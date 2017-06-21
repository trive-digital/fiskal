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
use Magento\Framework\Controller\ResultInterface;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Exception\NoSuchEntityException;

class Delete extends Action
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
     * MassDelete constructor.
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
     * @return ResultInterface
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $locationId = $this->getRequest()->getParam('location_id');
        try {
            $this->locationRepository->deleteById($locationId);
            $this->getMessageManager()->addSuccessMessage(__('You deleted the location.'));
            $this->dataPersistor->clear('trive_fiskal_location');
        } catch (NoSuchEntityException $exception) {
            $this->getMessageManager()->addErrorMessage($exception->getMessage());

            return $resultRedirect->setPath('*/*/edit', ['location_id' => $locationId]);
        } catch (\Exception $exception) {
            $this->getMessageManager()->addErrorMessage($exception->getMessage());

            return $resultRedirect->setPath('*/*/edit', ['location_id' => $locationId]);
        }

        return $resultRedirect->setPath('*/*/');
    }
}
