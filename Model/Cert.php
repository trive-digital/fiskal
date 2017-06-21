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

namespace Trive\Fiskal\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Filesystem;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * @method string getWebsiteId()
 * @method string getUpdatedAt()
 * @method string getContent()
 */
class Cert extends AbstractModel
{
    /**
     * Certificate base path
     */
    const BASEPATH_CERT = 'trive/fiskal/';

    /**
     * @var WriteInterface
     */
    protected $varDirectory;

    /**
     * @var EncryptorInterface
     */
    protected $encryptor;

    /**
     * @param Context            $context
     * @param Registry           $registry
     * @param Filesystem         $filesystem
     * @param EncryptorInterface $encryptor
     * @param AbstractResource   $resource
     * @param AbstractDb         $resourceCollection
     * @param array              $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Filesystem $filesystem,
        EncryptorInterface $encryptor,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->varDirectory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->encryptor = $encryptor;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Trive\Fiskal\Model\ResourceModel\Cert');
    }

    /**
     * Load model by website id
     *
     * @param int  $websiteId
     * @param bool $strictLoad
     *
     * @return $this
     */
    public function loadByWebsite($websiteId, $strictLoad = true)
    {
        $this->setWebsiteId($websiteId);
        $this->_getResource()->loadByWebsite($this, $strictLoad);

        return $this;
    }

    /**
     * Get path to certificate file, if file does not exist try to create it
     *
     * @return string
     * @throws LocalizedException
     */
    public function getCertPath()
    {
        if (!$this->getContent()) {
            throw new LocalizedException(__('The certificate does not exist.'));
        }

        $certFileName = sprintf('cert_%s_%s.pfx', $this->getWebsiteId(), strtotime($this->getUpdatedAt()));
        $certFile = self::BASEPATH_CERT.$certFileName;

        if (!$this->varDirectory->isExist($certFile)) {
            $this->createCertFile($certFile);
        }

        return $this->varDirectory->getAbsolutePath($certFile);
    }

    /**
     * Create physical certificate file based on DB data
     *
     * @param string $file
     *
     * @return void
     */
    protected function createCertFile($file)
    {
        if ($this->varDirectory->isDirectory(self::BASEPATH_CERT)) {
            $this->removeOutdatedCertFile();
        }
        $this->varDirectory->writeFile($file, $this->encryptor->decrypt($this->getContent()));
    }

    /**
     * Check and remove outdated certificate file by website
     *
     * @return void
     */
    protected function removeOutdatedCertFile()
    {
        $pattern = sprintf('cert_%s*', $this->getWebsiteId());
        $entries = $this->varDirectory->search($pattern, self::BASEPATH_CERT);
        foreach ($entries as $entry) {
            $this->varDirectory->delete($entry);
        }
    }

    /**
     * Delete assigned certificate file after delete object
     *
     * @return $this
     */
    public function afterDelete()
    {
        $this->removeOutdatedCertFile();

        return $this;
    }
}
