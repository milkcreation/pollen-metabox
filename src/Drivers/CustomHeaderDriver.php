<?php

declare(strict_types=1);

namespace Pollen\Metabox\Drivers;

use Pollen\Metabox\MetaboxDriver;

class CustomHeaderDriver extends MetaboxDriver implements CustomHeaderDriverInterface
{
    /**
     * @inheritDoc
     */
    protected $name = 'custom_header';

    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return array_merge(
            parent::defaultParams(),
            [
                'media_library_title'  => 'Personnalisation de l\'image d\'entête',
                'media_library_button' => 'Utiliser comme image d\'entête',
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->title ?? 'Image d\'entête';
    }

    /**
     * @inheritDoc
     */
    public function viewDirectory(): string
    {
        return $this->metabox()->resources('/views/drivers/custom-header');
    }
}