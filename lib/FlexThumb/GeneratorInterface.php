<?php

namespace FlexThumb;

/**
 * Interface for any class that knows how to generate a thumbnail
 * @package FlexThumb
 */
interface GeneratorInterface
{
    /**
     * @param $sourcePath string
     * @param $thumbnailType string
     * @param $targetPath string
     * @return null|mixed
     */
    public function generate($sourcePath, $thumbnailType, $targetPath);
}