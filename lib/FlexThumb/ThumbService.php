<?php

namespace FlexThumb;

use FlexThumb\Extension\PatternableServiceTrait;

class ThumbService
{
    use PatternableServiceTrait;

    /**
     * @var GeneratorInterface
     */
    protected $generator;

    /**
     * @var string
     */
    protected $lastTargetPath;

    /**
     * @param GeneratorInterface $generator
     */
    public function __construct(GeneratorInterface $generator)
    {
        $this->generator = $generator;
    }

    /**
     * @param array $patternVariables
     * @param $thumbnailType
     * @param GeneratorInterface $generator
     * @return mixed
     */
    public function generateUsingPattern(array $patternVariables, $thumbnailType, GeneratorInterface $generator = null)
    {
        $sourcePath = $this->prepareSourcePath($patternVariables);
        $targetPath = $this->prepareTargetPath($patternVariables);
        $result = $this->generate($sourcePath, $thumbnailType, $targetPath, $generator);
        return $result;
    }

    /**
     * @param $sourcePath string
     * @param $thumbnailType string
     * @param $targetPath string
     * @param GeneratorInterface $customGenerator
     * @return mixed|null
     */
    public function generate($sourcePath, $thumbnailType, $targetPath, GeneratorInterface $customGenerator = null)
    {
        $generator = (null === $customGenerator) ? $this->generator : $customGenerator;
        $result = $generator->generate($sourcePath, $thumbnailType, $targetPath);
        $this->lastTargetPath = $targetPath;
        return $result;
    }

    /**
     * @param bool $throwExceptionIfNull
     * @return string
     */
    public function getLastTargetPath($throwExceptionIfNull = true)
    {
        if (null === $this->lastTargetPath && $throwExceptionIfNull)
        {
            throw new \RuntimeException(sprintf(
                'Cannot return last target path, because it was not set previously! ' .
                'Probably there was no thumbnails generated and you are trying to get path to thumb to soon.'
            ));
        }

        return $this->lastTargetPath;
    }

}