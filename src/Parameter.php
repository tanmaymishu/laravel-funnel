<?php

namespace TanmayMishu\LaravelFunnel;

final class Parameter
{
    /**
     * @var string|array
     */
    private $rawValue;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string|array
     */
    private $value;

    /**
     * @var bool
     */
    private $searchable;

    /**
     * Parameter constructor.
     *
     * @param $name
     * @param $searchable
     * @param  null  $rawValue
     */
    public function __construct($name, $searchable, $rawValue = null)
    {
        $this->setName($name)
            ->setSearchable($searchable)
            ->setRawValue($rawValue ?? request($name))
            ->setValue($this->formatValue());
    }

    /**
     * Format the parameter's value. If the the operator is like/LIKE
     * then surround the value with `%` and if the parameter is an
     * array or comma-delimited list, then convert it to array.
     *
     * @return string|array
     */
    public function formatValue()
    {
        if ($this->isSearchable() && $this->isArray()) {
            return $this->toLikeFriendlyArray();
        }

        if ($this->isSearchable()) {
            return $this->toLikeFriendly();
        }

        if ($this->isMultiValue()) {
            return $this->toArray();
        }

        return $this->rawValue;
    }

    /**
     * Get the searchable.
     *
     * @return bool
     */
    public function isSearchable(): bool
    {
        return $this->searchable;
    }

    /**
     * Set the searchable.
     *
     * @param  bool  $searchable
     * @return Parameter
     */
    public function setSearchable(bool $searchable): Parameter
    {
        $this->searchable = $searchable;
        return $this;
    }

    /**
     * Checks if the value is an array.
     *
     * @return bool
     */
    public function isArray(): bool
    {
        return is_array($this->rawValue);
    }

    /**
     * Map the array to like-friendly array.
     *
     * @return array
     */
    public function toLikeFriendlyArray(): array
    {
        if (! $this->isArray()) {
            throw new \RuntimeException('Could not map non-array element to array.');
        }

        return array_map(function ($value) {
            return '%'.$value.'%';
        }, $this->rawValue);
    }

    /**
     * Make the single value string param like-friendly.
     *
     * @return string
     */
    public function toLikeFriendly(): string
    {
        if (! is_string($this->rawValue)) {
            throw new \RuntimeException('Could not convert to like-friendly. Param is not a string.');
        }

        return '%'.$this->rawValue.'%';
    }

    /**
     * Check if the value is an array or a comma-delimited list.
     *
     * @return bool
     */
    public function isMultiValue(): bool
    {
        return $this->isArray() || $this->isCommaDelimited();
    }

    /**
     * Check if the value is a comma-delimited list.
     *
     * @return bool
     */
    public function isCommaDelimited(): bool
    {
        if (! is_string($this->rawValue)) {
            return false;
        }

        return count(explode(',', $this->rawValue)) > 1;
    }

    /**
     * Convert the value to an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        if ($this->isCommaDelimited()) {
            return explode(',', $this->rawValue);
        }

        if (! $this->isArray()) {
            throw new \RuntimeException('Could not convert to array. Param is neither an array, nor comma-delimited.');
        }

        return $this->rawValue;
    }

    /**
     * Get the "normalized" value.
     *
     * @return array|string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set the value.
     *
     * @param $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get the "raw" parameter value.
     *
     * @return array|string
     */
    public function getRawValue()
    {
        return $this->rawValue;
    }

    /**
     * Set the "raw" parameter value.
     * @param  array|string  $rawValue
     * @return Parameter
     */
    public function setRawValue($rawValue)
    {
        $this->rawValue = $rawValue;
        return $this;
    }

    /**
     * Get the parameter name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the parameter name.
     *
     * @param  string  $name
     * @return Parameter
     */
    public function setName(string $name): Parameter
    {
        $this->name = $name;
        return $this;
    }
}
