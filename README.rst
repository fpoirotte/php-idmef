PHP-IDMEF
#########

Introduction
============

This repository contains a PHP library to deal with messages using the
Intrusion Detection Message Exchange Format (IDMEF) defined in
`RFC 4765 <https://tools.ietf.org/html/rfc4765>`_.

It supports all the classes and attributes of the RFC, provides support
for XML serialization/unserialization and also includes code to send alerts
generated using IDMEF to `Prelude SIEM <https://www.prelude-siem.org/>`_.


Prerequisites
=============

For basic usage, you only need the following dependencies:

- The `Composer <https://getcomposer.org/>`_ dependency manager
- PHP >= 5.6
- the PHP ``DOM`` extension
- the PHP ``Filter`` extension

Additional features require additional dependencies.
To use XML serialization/unserialization, you will also need:

- the PHP ``XMLReader`` extension
- the PHP ``XMLWriter`` extension

To send alerts to Prelude SIEM, you will also need:

- PHP >= 7.3
- Dmitry Stogov's `FFI extension <https://github.com/dstogov/php-ffi/>`_ for PHP
- a working installation of `Prelude SIEM <https://www.prelude-siem.org/>`_,
  especially, a running instance of the ``prelude-manager`` service


Installation
============

Use Composer to add the library to your project's requirements:

..  sourcecode:: bash

    $ php /path/to/composer.phar require fpoirotte/idmef


Usage
=====

IDMEF messages
--------------

A few words about IDMEF paths
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

To make working with IDMEF messages easier, this library implements
the same concept of "IDMEF paths" as Prelude SIEM through a ``getIterator()``
method. (more on that later)

The library also supports direct access to attributes using getters and setters.
(read on for more information)

When returning an attribute's path through the ``getName()`` method,
the library always uses the official class/attribute names defined
in the IDMEF RFC, such as ``Alert.CorrelationAlert.name``.

However, when setting an IDMEF message's attributes, you may also use
Prelude SIEM's IDMEF paths, such as ``alert.correlation_alert.name``.
A list of valid paths recognized by both Prelude SIEM and this library
can be found on the `SECEF website
<https://redmine.secef.net/projects/secef/wiki/LibPrelude_IDMEF_path>`_.

When using getters/setters, a similar approach is taken, but with a few
caveats:

-   PHP's object operator (``->``) is used instead of a dot (``.``)
    to separate the various parts of the IDMEF path.

    Hence, to retrieve the value for the ``Alert.CorrelationAlert.name`` path
    inside an IDMEF object, use ``$name = $alert->CorrelationAlert->name``,
    Prelude's path names (``$alert->correlation_alert->name``) are also
    supported.

-   PHP's array operator (``[]``) is used to access entries inside a list,
    instead of Prelude's list access operator (``()``).

    Like Prelude SIEM, this library also supports negative list indices. 
    Therefore, to retrieve the name of the last source node using the getters,
    the following call may be used: ``$name = $alert->source[-1]->node->name;``.
    Compare this to Prelude's paths: ``alert.source(-1).node.name``.

-   Prelude SIEM's prepend (``<<``) and append (``>>``) operators can be used.
    As a result, the following call appends a new source node to the alert
    and gives it a name: ``$alert->source['>>']->node->name = "foo";``.

-   PHP's array operator may also be used to append a new entry to a list.
    Therefore, calling ``$alert->source[]->node->name = "foo";`` is functionally
    identical to calling ``$alert->source['>>']->node->name = "foo";``.

As is the case with Prelude SIEM, this library indexes IDMEF lists
starting from 0. So, ``$alert->source[0]`` refers to the first source
in the alert.

Last but not least, there is one noticeable difference between this library's
paths implementation and Prelude SIEM's paths, involving the ``Analyzer``
class. The RFC states that analyzers can be chained using a recursive
definition (``Alert.Analyzer``, ``Alert.Analyzer.Analyzer`` ...).
To make working with chained analyzers easier, Prelude SIEM represents
them as a list (``alert.analyzer(0)``, ``alert.analyzer(1)``, ...).
To be as close to the IDMEF RFC as possible, this library uses the recursive
approach to represent chained analyzers. However, some of the API may also
implement Prelude SIEM's notation for them, so you mileage may vary.


Data types
~~~~~~~~~~

The library automatically converts values to their expected type whenever
it is possible. It will also convert PHP types to their IDMEF counterparts
automatically.

Therefore, it is possible to pass a string value representing an integer
to an attribute that expects an IDMEF integer:

..  sourcecode:: php

    // Import a few symbols
    use \fpoirotte\IDMEF\Types\IntegerType;
    use \fpoirotte\IDMEF\Types\StringType;

    // The following statements are okay:
    $alert->OverflowAlert->size = new IntegerType(42);  // IDMEF integer object
    $alert->OverflowAlert->size = 42;                   // PHP integer
    $alert->OverflowAlert->size = '42';                 // IDMEF integer value
    $alert->OverflowAlert->size = '0x2A';               // IDMEF (hexadecimal) integer value

    // The following statements will throw an exception:
    $alert->OverflowAlert->size = new StringType('42'); // The "size" attribute is an integer, not a string
    $alert->OverflowAlert->size = 42.0;                 // A floating-point value is not an integer either
    $alert->OverflowAlert->size = '';                   // Invalid integer (value is missing)
    $alert->OverflowAlert->size = '0x';                 // Invalid integer (hexadecimal number missing a value)
    $alert->OverflowAlert->size = '2A';                 // Invalid integer (possibly an hexadecimal number missing the prefix,
                                                        // or trailing data after the intended number)

However, this is only true when the expected type is known in advance.
For those situations where this may not be the case (eg. additional data),
the library will also attempt to convert the type automatically, but you
may have to set the type explicitly.

The following table shows how native PHP types after converted into their
IDMEF counterparts.

..  list-table:: PHP-type to IDMEF-type conversion table
    :header-rows: 1

    * - PHP type
      - IDMEF type
    * - ``boolean``
      - boolean (``\fpoirotte\IDMEF\Types\BooleanType``)
    * - ``integer``
      - integer (``\fpoirotte\IDMEF\Types\IntegerType``)
    * - ``string``
      - string (``\fpoirotte\IDMEF\Types\StringType``)
    * - ``float``
      - real number (``\fpoirotte\IDMEF\Types\RealType``)
    * - ``\DateTimeInterface`` and its derivatives
      - date-type (``\fpoirotte\IDMEF\Types\DateTimeType``)
    * - ``\DOMNode``
      - xmltext (``\fpoirotte\IDMEF\Types\XmltextType``)
    * - ``\SimpleXMLElement``
      - xmltext (``\fpoirotte\IDMEF\Types\XmltextType``)
    * - ``\XMLWriter``
      - xmltext (``\fpoirotte\IDMEF\Types\XmltextType``)
    * - ``\fpoirotte\IDMEF\Types\AbstractType`` and its derivatives
      - *unchanged*
    * - *any other value*
      - *throws an exception*

The following types must be managed manually when used in additional data:

-   ``\fpoirotte\IDMEF\Types\ByteType``
-   ``\fpoirotte\IDMEF\Types\ByteStringType``
-   ``\fpoirotte\IDMEF\Types\CharacterType``
-   ``\fpoirotte\IDMEF\Types\NtpstampType``
-   ``\fpoirotte\IDMEF\Types\PortlistType``


IDMEF message manipulation
~~~~~~~~~~~~~~~~~~~~~~~~~~

The following example shows how to create an alert, set some of its attributes,
then do some stuff with it.

..  sourcecode:: php

    <?php

    // Include Composer's autoloader
    require '.' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

    // Import a few symbols from the library
    use \fpoirotte\IDMEF\Classes\Alert;
    use \fpoirotte\IDMEF\Types\AbstractType;

    // Create the alert
    $alert = new Alert;

    // Set mandatory attributes
    $alert->analyzer->analyzerid = 'hq-dmz-analyzer01';
    $alert->analyzer->node->category = 'dns';
    $alert->analyzer->node->location = 'Headquarters DMZ Network';
    $alert->analyzer->node->name = 'analyzer01.example.com';
    $alert->create_time->ntpstamp = '0xbc722ebe.0x00000000';

    // Set some optional attributes and provide additional data
    $alert->classification->text = "Houston, we've had a problem here";
    $alert->additional_data[  ]->type = 'string';
    $alert->additional_data[-1]->meaning = 'mission';
    $alert->additional_data[-1]->data = 'Apollo 13';
    $alert->additional_data[  ]->type = 'string';
    $alert->additional_data[-1]->meaning = 'speaker';
    $alert->additional_data[-1]->data = 'Jack Swigert';

    // Display the alert's classification:
    echo $alert->classification->text . PHP_EOL;

    // Iterate over additional data and display each entry's meaning and data:
    foreach ($alert->additional_data as $ad) {
        echo $ad->meaning . ': ' . $ad->data . PHP_EOL;
    }

    // Same thing, but this time we use an explicit iterator and IDMEF paths:
    foreach ($alert->getIterator('alert.additional_data') as $ad) {
        echo $ad->meaning . ': ' . $ad->data . PHP_EOL;
    }

    // Dump the alert's contents, by iterating over instances
    // of the AbstractType class (the base class for all leaf nodes)
    foreach ($alert->getIterator('{' . AbstractType::class . '}', null, 0, -1) as $path => $node) {
        echo $path . ' => ' . $node . PHP_EOL;
    }

    // Look for nodes with a specific value:
    foreach ($alert->getIterator(null, 'Apollo 13', 0, -1) as $path => $node) {
        echo $path . PHP_EOL;   // displays "Alert.AdditionalData(0).data"
    }

    // The 3rd ($minDepth) and 4th ($maxDepth) parameter to getIterator()
    // can be used to restrict iteration to nodes at a certain depth,
    // starting at 0 for the root object.
    // The following example will only dump the analyzer node's attribute
    // due to the restrictions.
    // Eg.  path:   Alert.Analyzer.Node.Name
    //      depth:  (0)   (1)      (2)  (3)
    foreach ($alert->getIterator(null, null, 3, -1) as $path => $node) {
        echo $path . PHP_EOL;   // displays "Alert.Analyzer.Node.category",
                                //          "Alert.Analyzer.Node.location"
                                //      and "Alert.Analyzer.Node.name"
    }


Heatbeat messages and more specialized alert messages (CorrelationAlert,
ToolAlert and OverflowAlert) follow the same pattern.


XML (un)serialization
---------------------

When serializing an IDMEF message to XML, a special container must be created.

Assuming an alert and a heartbeat have been created and stored respectively
in the ``$alert`` and ``$heartbeat`` variables, the following example
can be used to serialize them into an XML IDMEF message:

..  sourcecode:: php

    <?php

    // Import the container and the serializer
    use \fpoirotte\IDMEF\Classes\IDMEFMessage;
    use \fpoirotte\IDMEF\Serializers\Xml;

    // Create an instance of the container and add the messages to it
    $idmef = new IDMEFMessage;
    $idmef[] = $alert;
    $idmef[] = $heartbeat;

    // Create an instance of the serialization class and produce the output
    $serializer = new Xml;
    echo $serializer->serialize($idmef) . PHP_EOL;

Likewise, unserialization returns an ``IDMEFMessage`` container.
Assuming that ``$xml`` refers to a valid XML IDMEF message containing both
an alert and a heartbeat (in that order), the following code could be used
to unserialize them:

..  sourcecode:: php

    <?php

    // Import the (un)serializer
    use \fpoirotte\IDMEF\Serializers\Xml;

    // Create an instance of the serialization class
    // and unserialize the message
    $serializer = new Xml;
    $idmef      = $serializer->unserialize($xml);
    // The unserialization process maintains the objects' order
    $alert      = $idmef[0];
    $heartbeat  = $idmef[1];


Prelude SIEM
------------

To send IDMEF messages to Prelude SIEM, you must first register a profile
with the ``idmef:w`` permission for the library.

On the machine where ``prelude-manager`` resides, run this:

..  sourcecode:: bash

    sudo prelude-admin registration-server prelude-manager


In parallel, on the machine where the library will be running, run this:

..  sourcecode:: bash

    # Replace "php" with a custom name for the newly-created profile.
    #
    # Replace "localhost" with the hostname where prelude-manager is installed.
    #
    # Replace "clicky" & "users" respectively with the names of the user and group
    # that will execute the PHP script.
    #
    sudo prelude-admin register php idmef:w localhost --uid clicky --gid users

Then, follow the instructions printed by both commands.

Once the profile is successfully registered, you can send IDMEF messages
to Prelude SIEM using the following code:

..  sourcecode:: php

    <?php

    // Replace this value with your registered profile's name
    $profile = 'php';

    // Create a new Prelude agent using that profile
    $agent = \fpoirotte\IDMEF\PreludeAgent::create($profile);

    // Send various alerts/heartbeats
    $agent->send($alert);
    $agent->send($correlation_alert);
    $agent->send($heartbeat);
    // and so on

..  note::

    The agent will automatically send heartbeat messages to ``prelude-manager``
    at a regular interval (defined in the agent's profile).
    It is therefore not necessary to send them manually.

    Likewise, the agent will automatically be (properly) shut down when it
    becomes unused, as part of PHP's garbage collection process.
    You may also force a shutdown manually by using the following code snippet:

    ..  sourcecode:: php

        <?php

        unset($agent);
        gc_collect_cycles();


License
=======

This library is licensed under the GNU Public License version 2.
See the ``COPYING`` file inside the repository for more information.
