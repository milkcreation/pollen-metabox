<?php

declare(strict_types=1);

namespace Pollen\Metabox;

use Pollen\View\Engines\Plates\PlatesFieldAwareTemplateTrait;
use Pollen\View\Engines\Plates\PlatesPartialAwareTemplateTrait;
use Pollen\View\Engines\Plates\PlatesTemplate;

/**
 * @method string getName()
 * @method mixed getValue(string|null $key = null, mixed $default = null)
 */
class MetaboxTemplate extends PlatesTemplate
{
    use PlatesFieldAwareTemplateTrait;
    use PlatesPartialAwareTemplateTrait;
}