<?php

namespace TBollmeier\Argparser;

class CmdOption
{
    const NO_VALUE = 1;
    const VALUE_REQUIRED = 2;
    const VALUE_OPTIONAL = 3;

    public function __construct(
        private string $short,
        private string $long,
        private int $valueMode,
        private string $defaultValue="",
        private string $componentName=""
    )
    {
    }

    public function getShort(): string
    {
        return $this->short;
    }

    public function getLong(): string
    {
        return $this->long;
    }

    public function getValueMode(): int
    {
        return $this->valueMode;
    }

    public function getDefaultValue(): string
    {
        return $this->defaultValue;
    }

    public function getComponentName(): string
    {
        if (!empty($this->componentName)) {
            return $this->componentName;
        } elseif (!empty($this->long)) {
            return $this->long;
        } else {
            return $this->short;
        }
    }
}

