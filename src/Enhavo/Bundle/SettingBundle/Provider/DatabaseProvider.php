<?php
/**
 * Created by PhpStorm.
 * User: jungch
 * Date: 26/08/16
 * Time: 12:09
 */

namespace Enhavo\Bundle\SettingBundle\Provider;

use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Doctrine\ORM\EntityManagerInterface;
use Enhavo\Bundle\SettingBundle\Entity\Setting;
use Enhavo\Bundle\AppBundle\Filesystem\Filesystem;

class DatabaseProvider implements ProviderInterface
{
    const CACHE_FILE_NAME = 'setting_array.json';

    /**
     * @var KernelInterface
     */
    protected $kernel;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var FactoryInterface
     */
    protected $settingFactory;

    /**
     * @var Filesystem
     */
    protected $fileSystem;

    public function __construct(KernelInterface $kernel, EntityManagerInterface $em, FactoryInterface $settingFactory, Filesystem $fileSystem)
    {
        $this->kernel = $kernel;
        $this->em = $em;
        $this->settingFactory = $settingFactory;
        $this->fileSystem = $fileSystem;
    }

    public function init()
    {
        $settingArrayFromCache = $this->getSettingArrayFromCache();
        foreach ($settingArrayFromCache as $key => $settingFromCache){
            $settingFromDatabase = $this->getSettingFromDatabase($key);
            if ($settingFromDatabase === null and $this->isCacheEntryValid($settingFromCache)){
                $settingToDatabase = $this->convertToObject($key, $settingFromCache);
                $this->persistToDatabase($settingToDatabase);
            }
        }
    }

    public function getSetting($key)
    {
        $setting = $this->getSettingFromDatabase($key);
        if ($setting === null) {
            $setting = $this->getSettingFromCache($key);
            if ($this->isCacheEntryValid($setting)) {
                $this->persistToDatabase($setting);
            }
        }
        return $setting;
    }

    protected function getSettingFromDatabase($key)
    {
        return $this->em->getRepository('EnhavoSettingBundle:Setting')->findOneBy(array('key' => $key));
    }

    protected function getSettingArrayFromCache()
    {
        $cacheFilePath = sprintf('%s/%s', $this->kernel->getCacheDir(), self::CACHE_FILE_NAME);
        $settingArray = json_decode($this->fileSystem->readFile($cacheFilePath), $assoc=true);
        return array_filter($settingArray);
    }

    protected function getSettingFromCache($key)
    {
        $settingArray = $this->getSettingArrayFromCache();

        if (array_key_exists($key, $settingArray)){
            return $this->convertToObject($key, $settingArray[$key]);
        }
        return null;
    }

    protected function persistToDatabase($object){
        $this->em->persist($object);
        $this->em->flush();
    }

    protected function isCacheEntryValid($cacheEntry)
    {
        if(is_array($cacheEntry)) {
            if(!array_key_exists('type', $cacheEntry)) {
                return false;
            }
            if(!array_key_exists('label', $cacheEntry)) {
                return false;
            }
            return true;
        }

        if($cacheEntry instanceof Setting) {
            if($cacheEntry->getType() === null) {
                return false;
            }
            if($cacheEntry->getLabel() === null) {
                return false;
            }
            return true;
        }
        return false;
    }

    protected function convertToObject($key, $array)
    {
        $object = $this->settingFactory->createNew();
        $object->setKey($key);
        if(array_key_exists('type', $array)) {
            $object->setType($array['type']);
        }
        if(array_key_exists('label', $array)) {
            $object->setLabel($array['label']);
        }
        if(array_key_exists('value', $array)) {
            $object->setValue($array['value']);
        }
        if(array_key_exists('translationDomain', $array)) {
            $object->setTranslationDomain($array['translationDomain']);
        }
        return $object;
    }
}
