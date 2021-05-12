<?php

declare(strict_types=1);

namespace Pollen\Metabox\Drivers;

use Pollen\Metabox\MetaboxDriver;
use Pollen\Support\Proxy\FieldProxy;

class SubtitleDriver extends MetaboxDriver implements SubtitleDriverInterface
{
    use FieldProxy;

    /**
     * @inheritDoc
     */
    protected $name = 'subtitle';

    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return array_merge(
            parent::defaultParams(),
            [
                'attrs' => [
                    'class'       => 'widefat',
                    'placeholder' => 'Sous-titre',
                    'style'       => 'margin-top:10px;margin-bottom:20px;background-color:#fff;font-size:1.4em;' .
                        'height:1.7em;line-height:100%;margin:10 0 15px;outline:0 none;padding:3px 8px;width:100%;',
                ],
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->title ?? 'Sous-titre';
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        return $this->field(
            'text',
            array_merge(
                $this->all(),
                [
                    'name'  => $this->getName(),
                    'value' => $this->getValue(),
                ]
            )
        )->render();
    }

    /**
     * @inheritDoc
     */
    public function viewDirectory(): string
    {
        return $this->metabox()->resources('/views/drivers/subtitle');
    }
}