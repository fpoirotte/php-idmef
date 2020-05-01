<?php

use PHPUnit\Framework\TestCase;
use \fpoirotte\IDMEF\Classes\Alert;

class ListTest extends TestCase
{
    public function setUp() : void
    {
        $this->alert = new Alert;

        // Later on, we prepend another value (ident0) to the list,
        // so the identifiers are all slightly offset.
        $this->alert->source[]->ident       = 'ident1'; // append (variation)
        $this->alert->source[1]->ident      = 'ident2'; // fixed offset
        $this->alert->source['<<']->ident   = 'ident0'; // prepend
        $this->alert->source['>>']->ident   = 'ident3'; // append
    }

    public function testListOperators()
    {
        // The alert should contain 4 sources.
        $this->assertSame(4, count($this->alert->source));

        // And their identifiers should be ident0 ... ident3.
        // First, we test them using direct indexes (0 ... 3)
        foreach (range(0, 3) as $index) {
            $this->assertSame("ident$index", (string) $this->alert->source[$index]->ident);
        }

        // Now we test them in reverse order
        // using negative indices (-1 ... -4).
        foreach (range(-1, -4) as $index) {
            $idx = 4 + $index;
            $this->assertSame("ident$idx", (string) $this->alert->source[$index]->ident);
        }
    }
}
