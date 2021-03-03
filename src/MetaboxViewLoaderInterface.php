<?php

declare(strict_types=1);

namespace Pollen\Metabox;

use Pollen\View\FieldAwareViewLoaderInterface;
use Pollen\View\PartialAwareViewLoaderInterface;
use Pollen\View\ViewLoaderInterface;

/**
 * @method string getName()
 * @method mixed getValue(string|null $key = null, mixed $default = null)
 */
interface MetaboxViewLoaderInterface extends
    FieldAwareViewLoaderInterface,
    PartialAwareViewLoaderInterface,
    ViewLoaderInterface
{
}
