<?php
declare(strict_types=1);

namespace fpoirotte\IDMEF\Classes;

use \fpoirotte\IDMEF\Types\Enums\FileCategoryEnum;
use \fpoirotte\IDMEF\Types\Enums\FsTypeEnum;
use \fpoirotte\IDMEF\Types\StringType;
use \fpoirotte\IDMEF\Types\IntegerType;
use \fpoirotte\IDMEF\Types\DateTimeType;

class File extends AbstractClass
{
    protected static $_subclasses = array(
        'ident'         => StringType::class,
        'category'      => FileCategoryEnum::class,
        'fstype'        => FsTypeEnum::class,
        'file-type'     => StringType::class,
        'name'          => StringType::class,
        'path'          => StringType::class,
        'create-time'   => DateTimeType::class,
        'modify-time'   => DateTimeType::class,
        'access-time'   => DateTimeType::class,
        'data-size'     => IntegerType::class,
        'disk-size'     => IntegerType::class,
        'FileAccess'    => FileAccessList::class,
        'Linkage'       => LinkageList::class,
        'Inode'         => Inode::class,
        'Checksum'      => ChecksumList::class,
    );

    protected static $_mandatory = array(
        'name',
        'path',
        'category',
    );
}
