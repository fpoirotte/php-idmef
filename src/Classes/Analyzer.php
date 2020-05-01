<?php
declare(strict_types=1);

namespace fpoirotte\IDMEF\Classes;

use \fpoirotte\IDMEF\Types\StringType;

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
