<?php

namespace TBollmeier\Argparser;


use Exception;

class ArgumentParser
{

    public function __construct(
        private array $options = []
    )
    {
    }

    public function newOption(): CmdOptionBuilder
    {
        return new CmdOptionBuilder($this);
    }

    public function addOption(CmdOption $option): void
    {
        $this->options[] = $option;
    }

    /**
     * @throws Exception
     */
    public function parse(): array
    {
        global $argv;

        list($shortOpts, $longOpts) = $this->buildOptions();
        $options = getopt($shortOpts, $longOpts);
        $args = $this->removeOptionsFromArgs($argv, $options);

        return [$args, $this->createOptionsObject($options)];
    }

    /**
     * @throws Exception
     */
    private function createOptionsObject($options): \stdClass
    {
        $ret = new \stdClass();

        foreach ($this->options as $option) {
            $shortLong = "";
            $short = $option->getShort();
            $long = $option->getLong();
            if (!empty($short)) {
                $shortLong = "-$short";
            }
            if (!empty($long)) {
                if (!empty($shortLong)) {
                    $shortLong .= ", ";
                }
                $shortLong .= "--$long";
            }
            $shortLong = "'$shortLong'";

            $component = $option->getComponentName();
            $valueMode = $option->getValueMode();

            if (array_key_exists($long, $options)) {
                $value = $options[$long];
                $ret->$component = match ($valueMode) {
                    CmdOption::VALUE_REQUIRED => $value,
                    CmdOption::VALUE_OPTIONAL => $value === false ?
                        $option->getDefaultValue() : $value,
                    default => true
                };
            } elseif (array_key_exists($short, $options)) {
                $value = $options[$short];
                $ret->$component = match ($valueMode) {
                    CmdOption::VALUE_REQUIRED => $value,
                    CmdOption::VALUE_OPTIONAL => $value === false ?
                        $option->getDefaultValue() : $value,
                    default => true
                };
            } else {
                $ret->$component = match ($option->getValueMode()) {
                    CmdOption::NO_VALUE => false,
                    CmdOption::VALUE_OPTIONAL => $option->getDefaultValue(),
                    default => throw new Exception("Required option $shortLong not given"),
                };
            }
        }

        return $ret;
    }

    private function buildOptions(): array
    {
        $shortOpts = "";
        $longOpts = [];

        foreach ($this->options as $option) {

            $valueMode = $option->getValueMode();

            $suffix = match ($valueMode) {
                CmdOption::VALUE_REQUIRED => ":",
                CmdOption::VALUE_OPTIONAL => "::",
                default => "",
            };

            $shortOpt = $option->getShort();

            if (!empty($shortOpt)) {
                $shortOpts .= $shortOpt . $suffix;
            }

            $longOpt = $option->getLong();

            if (!empty($longOpt)) {
                $longOpts[] = $longOpt . $suffix;
            }

        }

        return [$shortOpts, $longOpts];
    }

    private function removeOptionsFromArgs($args, $options): array
    {
        $ret = [];

        $idx = 1;
        $idxMax = count($args) - 1;

        while ($idx <= $idxMax) {

            $arg = $args[$idx];

            if ($arg[0] !== "-") {
                $ret[] = $arg;
            } elseif (!(strlen($arg) > 1 && $arg[1] == "-") &&
                $this->hasShortOptionPendingValue($arg, $options)
            ) {
                // Check whether option has a pending value as next item:
                // Long options will always have their value directly attached
                // Find matching short option and check whether the value is already
                // included
                $idx++; // skip next item as it represents the pending option value
            }

            $idx++;
        }

        return $ret;
    }

    private function hasShortOptionPendingValue($arg, $usedOptions): bool
    {
        foreach ($this->options as $option) {

            $short = $option->getShort();
            if (empty($short)) {
                continue; // no short option
            }

            if (!array_key_exists($short, $usedOptions)) {
                continue; // option not used in current call
            }

            $valueMode = $option->getValueMode();

            if ($valueMode == CmdOption::NO_VALUE) {
                continue; // no value
            } elseif ($valueMode == CmdOption::VALUE_OPTIONAL &&
                $usedOptions[$short] === false) {
                continue;
            }


            $matches = [];
            if (preg_match("/-" . $short . "(.*)/", $arg, $matches)) {
                if (strlen($matches[0]) == strlen($short) + 1) {
                    return true;
                }
            }

        }

        return false;
    }
}

