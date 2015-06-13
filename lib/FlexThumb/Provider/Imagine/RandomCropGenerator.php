<?php

namespace FlexThumb\Provider\Imagine;

use Imagine\Image\Box;
use Imagine\Image\Point;

class RandomCropGenerator extends DefaultThumbGenerator
{
    /**
     * @var array
     */
    protected $defaultThumbOptions = array(
        'dry_run' => false, // just simulate thumb generation, do not save anything in filesystem
        'save_options' => array(), // imagine save options like quality and file format
        'chmod' => 0755, // if thumb path does not exist it will be created using those chmods
        'strict_size' => true // throw exception if target size is larger than source file
    );

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

        $targetWidth = (int) $opt['width'];
        $targetHeight = (int) $opt['height'];

        $mp = $this->imagine->open($sourcePath);
        $sourceSize = $mp->getSize();

        $isHighEnough = $sourceSize->getHeight() >= $targetHeight;
        $isWideEnough = $sourceSize->getWidth() >= $targetWidth;

        if ($opt['strict_size'])
        {
            if (!$isHighEnough)
            {
                throw new \RuntimeException(sprintf(
                    'Target height of thumbnail (%s) is higher than height of source file (%s). Processing file: %s',
                    $targetHeight, $sourceSize->getHeight(), $sourcePath
                ));
            }

            if (!$isWideEnough)
            {
                throw new \RuntimeException(sprintf(
                    'Target width of thumbnail (%s) is higher than width of source file (%s). Processing file: %s',
                    $targetWidth, $sourceSize->getWidth(), $sourcePath
                ));
            }
        }

        if ($isWideEnough)
        {
            $randX = mt_rand(0, 1000) / 1000;
            $x = (int) ($randX * ($sourceSize->getWidth() - $targetWidth));

        } else
        {
            $targetWidth = $sourceSize->getWidth();
            $x = 0;
        }

        if ($isHighEnough)
        {
            $randY = mt_rand(0, 1000) / 1000;
            $y = (int) ($randY * ($sourceSize->getHeight() - $targetHeight));

        } else
        {
            $targetHeight = $sourceSize->getHeight();
            $y = 0;
        }

        $targetPoint = new Point($x, $y);
        $targetSize = new Box($targetWidth, $targetHeight);
        $mp->crop($targetPoint, $targetSize);

        if ($opt['dry_run'])
        {
            // just return image manipulator object to let user show generated results on the fly
            return $mp;
        }

        $this->ensureDirExistence(dirname($targetPath), $opt['chmod']);
        $mp->save($targetPath, $opt['save_options']);
        return $mp;
    }
}