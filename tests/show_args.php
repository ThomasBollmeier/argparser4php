<?php

require_once '../vendor/autoload.php';


$parser = (new TBollmeier\Argparser\ArgumentParser())
    ->newOption()
        ->short("f")
        ->long("flag")
        ->add()
    ->newOption()
        ->long("required")
        ->valueRequired()
        ->add()
    ->newOption()
        ->short("o")
        ->long("optional")
        ->valueOptional()
        ->defaultValue(42)
        ->add();

list($args, $options) = $parser->parse();

var_dump($args);
var_dump($options);
