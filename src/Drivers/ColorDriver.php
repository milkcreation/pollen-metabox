<?php

declare(strict_types=1);

namespace Pollen\Metabox\Drivers;

use Pollen\Metabox\MetaboxDriver;

class ColorDriver extends MetaboxDriver implements ColorDriverInterface
{
    /**
     * @inheritDoc
     */
    protected $name = 'color';

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->title ?? 'Couleur';
    }

    /**
     * @inheritDoc
     */
    public function viewDirectory(): string
    {
        return $this->metabox()->resources('/views/drivers/color');
    }
}
