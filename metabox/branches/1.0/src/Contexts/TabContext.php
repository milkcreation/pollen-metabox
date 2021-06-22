<?php

declare(strict_types=1);

namespace Pollen\Metabox\Contexts;

use Pollen\Metabox\MetaboxContext;
use Pollen\Metabox\MetaboxDriverInterface;

class TabContext extends MetaboxContext implements TabContextInterface
{
    /**
     * Onglet actif.
     */
    protected string $active = '';

    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return array_merge(
            parent::defaultParams(),
            [
                'rotation' => ['left', 'top', 'default', 'pills'],
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        /** @var MetaboxDriverInterface[]|array $drivers */
        if ($drivers = $this->getDrivers()) {
            $items = [];
            foreach ($drivers as $driver) {
                $items[$driver->getAlias()] = [
                    'name'     => $driver->getAlias(),
                    'title'    => $driver->getTitle(),
                    'parent'   => $driver->getParent(),
                    'content'  => "<div class=\"MetaboxTab-content\">{$driver->render()}</div>",
                    'position' => $driver->getPosition(),
                ];
            }

            $this->params(compact('items'));
        }
        return parent::render();
    }

    /**
     * @inheritDoc
     */
    public function setActive(string $tab): TabContextInterface
    {
        $this->active = $tab;

        return $this;
    }
}
