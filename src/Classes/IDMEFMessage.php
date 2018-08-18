<?php

namespace fpoirotte\IDMEF\Classes;

/**
 * A class aggregating IDMEF messages
 * as a collection of Alert/Heartbeat objects.
 */
class IDMEFMessage extends AbstractList
{
    protected $_type = AbstractIDMEFMessage::class;

    protected function changeChildParent($child)
    {
        // Do not change the node's parent.
        //
        // This node should be invisible to its children,
        // while still allowing browsing to them.
        //
        // This also avoids a costly cloning operation
        // when unserializing an XML IDMEF message.
    }
}
