<?php

declare(strict_types=1);

namespace Pollen\Metabox\Drivers;

use Pollen\Metabox\MetaboxDriver;

class IconDriver extends MetaboxDriver implements IconDriverInterface
{
    /**
     * @inheritDoc
     */
    protected $name = 'icon';

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->title ?? 'Icône représentative';
    }

    /**
     * @inheritDoc
     */
    public function viewDirectory(): string
    {
        return $this->metabox()->resources('/views/drivers/icon');
    }
}
