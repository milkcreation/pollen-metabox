<?php

declare(strict_types=1);

namespace Pollen\Metabox;

use Pollen\View\Engines\Plates\PlatesPartialAwareTemplateTrait;
use Pollen\View\Engines\Plates\PlatesTemplate;

class ContextTemplate extends PlatesTemplate
{
    use PlatesPartialAwareTemplateTrait;
}