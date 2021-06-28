<?php

declare(strict_types=1);

namespace Pollen\Metabox;

use Pollen\ViewExtends\PlatesTemplateInterface;

/**
 * @method string getName()
 * @method mixed getValue(string|null $key = null, mixed $default = null)
 */
interface MetaboxTemplateInterface extends PlatesTemplateInterface
{
}