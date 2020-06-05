<?php

namespace TanmayMishu\LaravelFunnel;

final class RequestParameter
{
    /**
     * @var string|array
     */
    public $params;

    /**
     * RequestParameter constructor.
     *
     * @param $requestParams
     */
    public function __construct($requestParams)
    {
        $this->params = $requestParams;
    }

    /**
     * Checks if the value is an array or a comma-delimited list.
     *
     * @return bool
     */
    public function isMultiValue(): bool
    {
        return $this->isArray() || $this->isCommaDelimited();
    }

    /**
     * Checks if the value is an array.
     *
     * @return bool
     */
    public function isArray(): bool
    {
        return is_array($this->params);
    }

    /**
     * Checks if the value is a comma-delimited list.
     *
     * @return bool
     */
    public function isCommaDelimited(): bool
    {
        if (! is_string($this->params)) {
            return false;
        }

        return count(explode(',', $this->params)) > 1;
    }

    /**
     * Convert the value to an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        if ($this->isCommaDelimited()) {
            return explode(',', $this->params);
        }

        if (! is_array($this->params)) {
            throw new \RuntimeException('Could not convert to array. Param is neither an array, nor comma-delimited.');
        }

        return $this->params;
    }

    /**
     * Makes the single value string param like-friendly.
     *
     * @return string
     */
    public function toLikeFriendly(): string
    {
        if (! is_string($this->params)) {
            throw new \RuntimeException('Could not convert to like-friendly. Param is not a string.');
        }

        return '%'.$this->params.'%';
    }
}
