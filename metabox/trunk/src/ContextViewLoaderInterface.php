<?php

declare(strict_types=1);

namespace Pollen\Metabox;

use Pollen\View\PartialAwareViewLoaderInterface;
use Pollen\View\ViewLoaderInterface;

interface ContextViewLoaderInterface extends PartialAwareViewLoaderInterface, ViewLoaderInterface
{
}
