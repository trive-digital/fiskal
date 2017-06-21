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

namespace Trive\Fiskal\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Trive\Fiskal\Api\Data\LocationInterface;

interface LocationRepositoryInterface
{
    /**
     * @param int $identifier
     *
     * @return \Trive\Fiskal\Api\Data\LocationInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($identifier);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Trive\Fiskal\Api\Data\LocationSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * @param \Trive\Fiskal\Api\Data\LocationInterface $location
     *
     * @return \Trive\Fiskal\Api\Data\LocationInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException If there is a problem with the input
     * @throws \Magento\Framework\Exception\NoSuchEntityException If a ID is sent but the entity does not exist
     */
    public function save(LocationInterface $location);

    /**
     * @param \Trive\Fiskal\Api\Data\LocationInterface $location
     *
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException If there is a problem with the input
     */
    public function delete(LocationInterface $location);

    /**
     * @param int $locationId
     *
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException If a ID is sent but the entity does not exist
     * @throws \Magento\Framework\Exception\CouldNotDeleteException If there is a problem with the input
     */
    public function deleteById($locationId);
}