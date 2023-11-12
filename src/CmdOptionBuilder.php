<?php

namespace TBollmeier\Argparser;

class CmdOptionBuilder
{
    
    public function __construct(
        private ArgumentParser $argParser,
        private string $short = "",
        private string $long = "",
        private int $valueMode = CmdOption::NO_VALUE,
        private string $defaultValue = "",
        private string $componentName = ""
    )
    {
    }
    
    public function short(string $short): CmdOptionBuilder
    {
        $this->short = $short;
        return $this;
    }
    
    public function long(string $long): CmdOptionBuilder
    {
        $this->long = $long;
        return $this;
    }
    
    public function valueRequired(): CmdOptionBuilder
    {
        $this->valueMode = CmdOption::VALUE_REQUIRED;
        return $this;
    }
    
    public function valueOptional(): CmdOptionBuilder
    {
        $this->valueMode = CmdOption::VALUE_OPTIONAL;
        return $this;
    }

    public function defaultValue($value): CmdOptionBuilder
    {
        $this->defaultValue = $value;
        return $this;
    }

    public function componentName(string $name): CmdOptionBuilder
    {
        $this->componentName = $name;
        return $this;
    }
    
    public function add(): ArgumentParser
    {
        $this->argParser->addOption(
            new CmdOption(
                $this->short,
                $this->long,
                $this->valueMode,
                $this->defaultValue,
                $this->componentName
            ));
        
        return $this->argParser;
    }
    
}

