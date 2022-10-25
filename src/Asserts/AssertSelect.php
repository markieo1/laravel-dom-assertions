<?php

declare(strict_types=1);

namespace Sinnbeck\DomAssertions\Asserts;

use PHPUnit\Framework\Assert;

/**
 * @internal
 */
class AssertSelect extends BaseAssert
{
    public function containsOption(mixed $attributes): self
    {
        $this->contains('option', $attributes);

        return $this;
    }

    public function containsOptions(...$attributes): self
    {
        foreach ($attributes as $attribute) {
            $this->containsOption($attribute);
        }

        return $this;
    }

    public function hasValue($value): self
    {
        Assert::assertNotNull(
            $option = $this->getParser()->query('option[selected="selected"]'),
            'No options are selected!'
        );

        Assert::assertEquals(
            $value,
            $this->getAttributeFor($option, 'value')
        );

        return $this;
    }

    public function hasValues(array $values): self
    {
        Assert::assertNotNull(
            $this->getParser()->query('option[selected="selected"]'),
            'No options are selected!'
        );

        $selected = [];
        foreach ($this->getParser()->queryAll('option[selected="selected"]') as $option) {
            $selected[] = $this->getAttributeFor($option, 'value');
        }

        Assert::assertEqualsCanonicalizing(
            $values,
            $selected,
            sprintf('Selected values does not match')
        );

        return $this;
    }
}