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
                'media_library_title'  => __('Personnalisation de l\'image d\'entête', 'tify'),
                'media_library_button' => __('Utiliser comme image d\'entête', 'tify'),
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->title ?? __('Image d\'entête', 'tify');
    }

    /**
     * @inheritDoc
     */
    public function viewDirectory(): string
    {
        return $this->metaboxManager()->resources('/views/drivers/custom-header');
    }
}