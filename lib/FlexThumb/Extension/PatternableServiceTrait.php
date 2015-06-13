<?php

namespace FlexThumb\Extension;

trait PatternableServiceTrait
{
    /**
     * @var string
     */
    protected $sourcePattern;

    /**
     * @var string
     */
    protected $targetPattern;

    /**
     * @var string
     */
    protected $tokenDetectionRegex = '/(\%s[a-zA-Z0-9\_]+\%s)/';

    /**
     * @var array
     */
    protected $detectedSourceTokens;

    /**
     * @var array
     */
    protected $detectedTargetTokens;

    /**
     * @param array $variables
     * @return string
     */
    public function prepareSourcePath(array $variables)
    {
        return $this->preparePath($this->sourcePattern, $variables, $this->detectedSourceTokens);
    }

    /**
     * @param array $variables
     * @return string
     */
    public function prepareTargetPath(array $variables)
    {
        return $this->preparePath($this->targetPattern, $variables, $this->detectedTargetTokens);
    }

    /**
     * @param $pathPattern string
     * @param array $tokenizedVariables array
     * @param array $requiredTokens array
     * @return string
     */
    public function preparePath($pathPattern, array $tokenizedVariables, array $requiredTokens)
    {
        if (empty($pathPattern))
        {
            throw new \RuntimeException(sprintf('%s %s',
                'Cannot determine file path, because path pattern is empty!',
                'Make sure you set pattern using setSourcePattern() and setTargetPattern() methods.'
            ));
        }

        $missingTokens = array_diff($requiredTokens, array_keys($tokenizedVariables));

        if (!empty($missingTokens))
        {
            throw new \RuntimeException(sprintf(
                'Cannot prepare thumbnail file path because of missing variables. %s token/s defined in path pattern were not provided: %s',
                count($missingTokens),  implode(', ', $missingTokens)
            ));
        }

        $path = strtr($pathPattern, $tokenizedVariables);
        return $path;
    }

    /**
     * Convenience method for preparing strtr tokens from regular assoc array
     *
     * @param array $assoc
     * @param string $tokenPrefix
     * @param null $tokenPostfix
     * @return array
     */
    public function tokenizeAssoc(array $assoc, $tokenPrefix = '%', $tokenPostfix = null)
    {
        if (null === $tokenPostfix)
        {
            $tokenPostfix = $tokenPrefix;
        }

        $tokens = [];

        foreach($assoc as $key => $value)
        {
            $tokenName = $tokenPrefix . $key . $tokenPostfix;
            $tokens[$tokenName] = (string) $value;
        }

        return $tokens;
    }

    /**
     * @param $pattern string
     * @param $tokenPrefix string
     * @param $tokenPostfix string
     * @return string[] Tokens that are within pattern
     */
    public function discoverTokens($pattern, $tokenPrefix, $tokenPostfix)
    {
        $regex = sprintf($this->tokenDetectionRegex, $tokenPrefix, $tokenPostfix);
        $matchingResult = [];
        $result = preg_match_all($regex, $pattern, $matchingResult);

        if (false === $result)
        {
            throw new \RuntimeException(sprintf(
                'Could not proceed with token detection! Regex matching failed. Regex pattern: "%s". Regex subject: "%s"',
                $regex, $pattern
            ));
        }

        $detectedTokens = $matchingResult[0];
        return $detectedTokens;
    }

    /**
     * @return string
     */
    public function getSourcePattern()
    {
        return $this->sourcePattern;
    }

    /**
     * @param string $sourcePattern
     * @param string $tokenPrefix
     * @param null|string $tokenPostfix
     * @return $this
     */
    public function setSourcePattern($sourcePattern, $tokenPrefix = '%', $tokenPostfix = null)
    {
        if (null === $tokenPostfix)
        {
            $tokenPostfix = $tokenPrefix;
        }

        $this->sourcePattern = $sourcePattern;
        $this->detectedSourceTokens = $this->discoverTokens($sourcePattern, $tokenPrefix, $tokenPostfix);
        return $this;
    }

    /**
     * @return string
     */
    public function getTargetPattern()
    {
        return $this->targetPattern;
    }

    /**
     * @param string $targetPattern
     * @param string $tokenPrefix
     * @param null|string $tokenPostfix
     * @return $this
     */
    public function setTargetPattern($targetPattern, $tokenPrefix = '%', $tokenPostfix = null)
    {
        if (null === $tokenPostfix)
        {
            $tokenPostfix = $tokenPrefix;
        }

        $this->targetPattern = $targetPattern;
        $this->detectedTargetTokens = $this->discoverTokens($targetPattern, $tokenPrefix, $tokenPostfix);
        return $this;
    }
}
