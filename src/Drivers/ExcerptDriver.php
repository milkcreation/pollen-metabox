<?php

declare(strict_types=1);

namespace Pollen\Metabox\Drivers;

use Pollen\Metabox\MetaboxDriver;

class ExcerptDriver extends MetaboxDriver implements ExcerptDriverInterface
{
    /**
     * @inheritDoc
     */
    protected $name = 'excerpt';

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->title ?? 'Extrait';
    }

    /**
     * @inheritDoc
     */
    public function viewDirectory(): string
    {
        return $this->metabox()->resources('/views/drivers/excerpt');
    }
}