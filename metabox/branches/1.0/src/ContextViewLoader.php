<?php

declare(strict_types=1);

namespace Pollen\Metabox;

use Pollen\View\PartialAwareViewLoader;
use Pollen\View\ViewLoader;

class ContextViewLoader extends ViewLoader implements ContextViewLoaderInterface
{
    use PartialAwareViewLoader;
}