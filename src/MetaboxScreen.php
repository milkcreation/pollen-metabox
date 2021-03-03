<?php

declare(strict_types=1);

namespace Pollen\Metabox;

use Pollen\Support\Concerns\BootableTrait;
use Pollen\Support\Concerns\ParamsBagAwareTrait;
use Pollen\Support\Proxy\MetaboxProxy;

class MetaboxScreen implements MetaboxScreenInterface
{
    use BootableTrait;
    use ParamsBagAwareTrait;
    use MetaboxProxy;

    /**
     * Alias de qualification.
     * @var string
     */
    protected $alias = '';

    /**
     * Indicateur d'écran d'affichage courant.
     * @var bool|null
     */
    protected $current;

    /**
     * @param MetaboxManagerInterface $metaboxManager
     */
    public function __construct(MetaboxManagerInterface $metaboxManager)
    {
        $this->setMetaboxManager($metaboxManager);
    }

    /**
     * @inheritDoc
     */
    public function boot(): MetaboxScreenInterface
    {
        if (!$this->isBooted()) {
            //events()->trigger('metabox.screen.booting', [$this->getAlias(), $this]);

            $this->parseParams();

            $this->setBooted();

            //events()->trigger('metabox.driver.booted', [$this->getAlias(), $this]);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * @inheritDoc
     */
    public function isCurrent(): bool
    {
        if (is_null($this->current)) {
            if ($this->isCurrentRoute()) {
                $this->current = true;
            } elseif ($this->isCurrentRequest()) {
                $this->current = true;
            } else {
                $this->current = false;
            }
        }
        return $this->current;
    }

    /**
     * @inheritDoc
     */
    public function isCurrentRoute(): bool
    {
        return $this->metabox()->router()->currentRouteName() === $this->alias;
    }

    /**
     * @inheritDoc
     */
    public function isCurrentRequest(): bool
    {
        return ltrim(rtrim(Request::getPathInfo(), '/'), '/') === ltrim(rtrim($this->alias, '/'), '/');
    }

    /**
     * @inheritDoc
     */
    public function setAlias(string $alias): MetaboxScreenInterface
    {
        $this->alias = $alias;

        return $this;
    }
}