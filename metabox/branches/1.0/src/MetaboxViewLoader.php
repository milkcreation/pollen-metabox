<?php

declare(strict_types=1);

namespace Pollen\Metabox;

use Pollen\View\FieldAwareViewLoader;
use Pollen\View\PartialAwareViewLoader;
use Pollen\View\ViewLoader;

/**
 * @method string getName()
 * @method mixed getValue(string|null $key = null, mixed $default = null)
 */
class MetaboxViewLoader extends ViewLoader implements MetaboxViewLoaderInterface
{
    use FieldAwareViewLoader;
    use PartialAwareViewLoader;
}