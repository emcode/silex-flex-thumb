<?php

namespace FlexThumb\Extension;

trait ConfigurableGeneratorTrait
{
    /**
     * @var array
     */
    protected $defaultOptions = array();

    /**
     * @var string[]
     */
    protected $requiredOptions = array();

    /**
     * @var array
     */
    protected $thumbnailTypes;

    /**
     * @param array $runtimeOptions
     * @param $thumbTypeName
     * @return array
     */
    public function resolveThumbnailTypeOptions(array $runtimeOptions, $thumbTypeName)
    {
        foreach($this->defaultOptions as $optionName => $defaultValue)
        {
            if (!isset($runtimeOptions[$optionName]))
            {
                $runtimeOptions[$optionName] = $defaultValue;
            }
        }

        foreach($this->requiredOptions as $optionName)
        {
            if (!isset($runtimeOptions[$optionName]))
            {
                throw new \RuntimeException(sprintf(
                    'Configuration of "%s" thumbnail type does not contain required config option "%s".',
                    $thumbTypeName, $optionName
                ));
            }
        }

        return $runtimeOptions;
    }

    public function getThumbnailType($thumbnailTypeName)
    {
        if (!isset($this->thumbnailTypes[$thumbnailTypeName]))
        {
            throw new \RuntimeException(sprintf(
                'Requested thumbnail type "%s" is not configured! Available types: %s',
                $thumbnailTypeName, implode(', ', array_keys($this->thumbnailTypes))
            ));
        }

        return $this->thumbnailTypes[$thumbnailTypeName];
    }

    /**
     * @return array
     */
    public function getDefaultOptions()
    {
        return $this->defaultOptions;
    }

    /**
     * @param array $defaultOptions
     */
    public function setDefaultOptions(array $defaultOptions)
    {
        $this->defaultOptions = $defaultOptions;
    }

    /**
     * @return string[]
     */
    public function getRequiredOptions()
    {
        return $this->requiredOptions;
    }

    /**
     * @param string[] $requiredOptions
     */
    public function setRequiredOptions(array $requiredOptions)
    {
        $this->requiredOptions = $requiredOptions;
    }

    /**
     * @return array
     */
    public function getThumbnailTypes()
    {
        return $this->thumbnailTypes;
    }

    /**
     * @param array $thumbnailTypes
     */
    public function setThumbnailTypes(array $thumbnailTypes)
    {
        $this->thumbnailTypes = $thumbnailTypes;
    }
}
