<?php

namespace fpoirotte\IDMEF\Classes;

use \fpoirotte\IDMEF\Types\StringType;
use \fpoirotte\IDMEF\Classes\Node;
use \fpoirotte\IDMEF\Classes\Process;

class Analyzer extends AbstractClass
{
    protected static $_subclasses = array(
        'analyzerid'    => StringType::class,
        'name'          => StringType::class,
        'manufacturer'  => StringType::class,
        'model'         => StringType::class,
        'version'       => StringType::class,
        'class'         => StringType::class,
        'ostype'        => StringType::class,
        'osversion'     => StringType::class,
        'Node'          => Node::class,
        'Process'       => Process::class,
        'Analyzer'      => __CLASS__,
    );
}
