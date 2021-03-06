<?php
/**
 * MediaExtension.php
 *
 * @since 07/09/14
 * @author Gerhard Seidel <gseidel.message@googlemail.com>
 */

namespace Enhavo\Bundle\MediaBundle\Twig;

use BaconStringUtils\Slugifier;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Templating\EngineInterface;
use Enhavo\Bundle\MediaBundle\Entity\File;

class MediaExtension extends \Twig_Extension
{
    protected $engine;
    protected $container;

    /**
     * @var EntityManager
     */
    protected $em;

    public function __construct($container, $em)
    {
        $this->container = $container;
        $this->em = $em;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('media_url', array($this, 'getMediaUrl')),
            new \Twig_SimpleFunction('media_filename', array($this, 'getMediaUrl')),
            new \Twig_SimpleFunction('media_slug', array($this, 'getMediaSlug')),
            new \Twig_SimpleFunction('media_parameter', array($this, 'getMediaParameter')),
        );
    }

    /**
     * @return EngineInterface
     */
    public function getEngine() {
        if($this->engine == null) {
            $this->engine = $this->container->get('templating');
        }
        return $this->engine;
    }

    public function getMediaUrl(File $file, $width = null, $height = null)
    {
        $path = '';
        if($file) {
            $path .= '/file/'.$file->getId();

            if($width) {
                $path .= '/'.$width;
                if($height) {
                    $path .= '/'.$height;
                }
            }

            $slug = $file->getSlug();
            if (empty($slug)) {
                $filename = $file->getFilename();
                if (!empty($filename)) {
                    $slug = $this->getSlugifiedFilename($filename);
                }
            }
            if (!empty($slug)) {
                $path .= '/' . $slug;
            }
        }

        return $path;
    }

    public function getMediaFilename(File $file)
    {
        return $file->getFilename();
    }

    public function getMediaSlug(File $file)
    {
        return $file->getSlug();
    }

    public function getMediaParameter(File $file, $parameterName)
    {
        return $file->getParameter($parameterName);
    }

    private function getSlugifiedFilename($text)
    {
        $slugifier = new Slugifier();
        $filePathinfo = pathinfo($text);

        $result = $slugifier->slugify($filePathinfo['filename']) . '.' . $filePathinfo['extension'];

//        $result = strtolower($text);
//        $result = preg_replace('/\s+/g', '-', $result);         // Replace spaces with -
//        $result = preg_replace('/[^\w\-\.]+/g', '', $result);   // Remove all non-word chars, keep . to preserve extension
//        $result = preg_replace('/\-\-+/g', '-', $result);       // Replace multiple - with single -
//        $result = preg_replace('/^-+/', '', $result);           // Trim - from start of text
//        $result = preg_replace('/-+$/', '', $result);           // Trim - from end of text

        return $result;
    }

    public function getName()
    {
        return 'enhavo_media_media_extension';
    }
} 
