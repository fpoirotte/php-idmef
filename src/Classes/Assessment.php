<?php

namespace fpoirotte\IDMEF\Classes;

class Assessment extends AbstractClass
{
    protected static $_subclasses = array(
        'Impact'        => Impact::class,
        'Action'        => ActionList::class,
        'Confidence'    => Confidence::class,
    );
}
