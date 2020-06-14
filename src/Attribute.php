<?php

namespace TanmayMishu\LaravelFunnel;

final class Attribute
{
    /**
     * @var string
     */
    private $name;

    /**
     * Attribute constructor.
     *
     * @param  string  $name
     */
    public function __construct(string $name)
    {
        $this->setName($name);
    }

    /**
     * Get the relation name from the relation string.
     *
     * @return string
     */
    public function extractRelation(): string
    {
        if (! $this->hasRelation()) {
            throw new \RuntimeException('Trying to extract relation from non-relation string.');
        }

        $exploded = explode('.', $this->name);

        return implode('.', array_slice(
            $exploded, 0, count($exploded) - 1, true
        ));
    }

    /**
     * Check whether the attribute contains a relation.
     *
     * @return bool
     */
    public function hasRelation(): bool
    {
        return $this->relationCount() > 0;
    }

    /**
     * Get the number of relations (dot separated).
     *
     * @return int
     */
    public function relationCount(): int
    {
        return count(explode('.', $this->name)) - 1;
    }

    /**
     * Get the attribute name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->hasRelation() ? $this->extractAttribute() : $this->name;
    }

    /**
     * Set the attribute name.
     *
     * @param  string  $name
     * @return Attribute
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Isolate and extract the attribute from relation string.
     *
     * @return string
     */
    public function extractAttribute()
    {
        if (! $this->hasRelation()) {
            throw new \RuntimeException('Trying to extract attribute from non-relation string.');
        }

        $exploded = explode('.', $this->name);

        return $exploded[count($exploded) - 1];
    }
}
