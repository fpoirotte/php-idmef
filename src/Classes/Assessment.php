<?php

namespace fpoirotte\IDMEF\Classes;

use \fpoirotte\IDMEF\Classes\Impact;
use \fpoirotte\IDMEF\Classes\ActionList;
use \fpoirotte\IDMEF\Classes\Confidence;

class Assessment extends AbstractClass
{
    protected static $_subclasses = array(
        'Impact'        => Impact::class,
        'Action'        => ActionList::class,
        'Confidence'    => Confidence::class,
    );
}
