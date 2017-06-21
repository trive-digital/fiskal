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

namespace Trive\Fiskal\Model\System\Config\Backend;

use Magento\Framework\App\Config\Value;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Trive\Fiskal\Model\CertFactory;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Directory\ReadInterface;
use Magento\Framework\Exception\LocalizedException;

class Cert extends Value
{
    /**
     * @var CertFactory
     */
    protected $certFactory;

    /**
     * @var EncryptorInterface
     */
    protected $encryptor;

    /**
     * @var ReadInterface
     */
    protected $tmpDirectory;

    /**
     * @param Context              $context
     * @param Registry             $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface    $cacheTypeList
     * @param CertFactory          $certFactory
     * @param EncryptorInterface   $encryptor
     * @param Filesystem           $filesystem
     * @param AbstractResource     $resource
     * @param AbstractDb           $resourceCollection
     * @param array                $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        CertFactory $certFactory,
        EncryptorInterface $encryptor,
        Filesystem $filesystem,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->certFactory = $certFactory;
        $this->encryptor = $encryptor;
        $this->tmpDirectory = $filesystem->getDirectoryRead(DirectoryList::SYS_TMP);
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * Process additional data before save config
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeSave()
    {
        $value = $this->getValue();

        if (!empty($value['value'])) {
            $this->setValue($value['value']);
        }

        if (is_array($value) && !empty($value['delete'])) {
            $this->setValue('');
            $this->certFactory->create()->loadByWebsite($this->getScopeId())->delete();
        }

        if (empty($value['tmp_name'])) {
            return $this;
        }

        $tmpPath = $this->tmpDirectory->getRelativePath($value['tmp_name']);

        if ($tmpPath && $this->tmpDirectory->isExist($tmpPath)) {
            if (!$this->tmpDirectory->stat($tmpPath)['size']) {
                throw new LocalizedException(__('The certificate file is empty.'));
            }
            $this->setValue($value['name']);
            $content = $this->encryptor->encrypt($this->tmpDirectory->readFile($tmpPath));
            $this->certFactory->create()->loadByWebsite($this->getScopeId())->setContent($content)->save();
        }

        return $this;
    }

    /**
     * Process object after delete data
     *
     * @return $this
     */
    public function afterDelete()
    {
        $this->certFactory->create()->loadByWebsite($this->getScopeId())->delete();

        return $this;
    }
}
