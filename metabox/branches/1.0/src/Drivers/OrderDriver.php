<?php

declare(strict_types=1);

namespace Pollen\Metabox\Drivers;

use Pollen\Metabox\MetaboxDriver;

class OrderDriver extends MetaboxDriver implements OrderDriverInterface
{
    /**
     * @inheritDoc
     */
    protected $name = 'order';

    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return array_merge(
            parent::defaultParams(),
            [
                'attrs' => [
                    'min' => -1,
                ],
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->title ?? 'Ordre d\'affichage';
    }

    /**
     * @inheritDoc
     */
    public function viewDirectory(): string
    {
        return $this->metabox()->resources('/views/drivers/order');
    }
}
