<?php

namespace FlexThumb\Provider\Imagine;

use FlexThumb\Extension\ConfigurableGeneratorTrait;
use FlexThumb\GeneratorInterface;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\ImagineInterface;

class DefaultThumbGenerator implements GeneratorInterface
{
    use ConfigurableGeneratorTrait;

    /**
     * We are not be able to override fields defined in ConfigurableGeneratorTrait,
     * so we are defining custom default values here and then assigning those values
     * to target fields in constructor of this class - not pretty I know
     * @var array
     */
    protected $defaultThumbOptions = array(
        'dry_run' => false, // just simulate thumb generation, do not save anything in filesystem
        'save_options' => array(), // imagine save options like quality and file format
        'filter' => ImageInterface::FILTER_UNDEFINED,
        'mode' => ImageInterface::THUMBNAIL_OUTBOUND,
        'chmod' => 0755 // if thumb path does not exist it will be created using those chmods
    );

    protected $requiredThumbOptions = array(
        'width', 'height'
    );

    /**
     * @var ImagineInterface
     */
    protected $imagine;

    /**
     * @param ImagineInterface $imagine
     * @param array $thumbnailTypes
     */
    public function __construct(ImagineInterface $imagine, array $thumbnailTypes)
    {
        $this->imagine = $imagine;
        $this->setThumbnailTypes($thumbnailTypes);
        $this->setDefaultOptions($this->defaultThumbOptions);
        $this->setRequiredOptions($this->requiredThumbOptions);
    }

    /**
     * @param string $sourcePath
     * @param string $thumbnailType
     * @param string $targetPath
     * @return \Imagine\Image\ManipulatorInterface
     */
    public function generate($sourcePath, $thumbnailType, $targetPath)
    {
        $thumbType = $this->getThumbnailType($thumbnailType);
        $opt = $this->resolveThumbnailTypeOptions($thumbType, $thumbType);

        $size = new Box($opt['width'], $opt['height']);
        $manipulator = $this->imagine->open($sourcePath)
                                     ->thumbnail($size, $opt['mode'], $opt['filter']);

        if ($opt['dry_run'])
        {
            // return image object to let user show generated results on the fly
            return $manipulator;
        }

        $this->ensureDirExistence(dirname($targetPath), $opt['chmod']);
        $manipulator->save($targetPath, $opt['save_options']);
        return $manipulator;
    }

    /**
     * @param $path string
     * @param $chmod integer
     */
    public function ensureDirExistence($path, $chmod)
    {
        if (is_dir($path))
        {
            return;
        }

        $creationSuccess = mkdir($path, $chmod, true);

        if (!$creationSuccess)
        {
            throw new \RuntimeException(sprintf(
                'Could not create path: "%s". Probably because of insufficient dir permissions of your PHP user.',
                $path
            ));
        }
    }
}