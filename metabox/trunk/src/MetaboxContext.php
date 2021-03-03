<?php

declare(strict_types=1);

namespace Pollen\Metabox;

use Pollen\Support\Concerns\BootableTrait;
use Pollen\Support\Concerns\ParamsBagAwareTrait;
use Pollen\Support\Proxy\MetaboxProxy;
use Pollen\View\ViewEngine;
use Pollen\View\ViewEngineInterface;

class MetaboxContext implements MetaboxContextInterface
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
     * Liste des pilotes déclarés.
     * @var MetaboxDriverInterface[]|array
     */
    protected $drivers = [];

    /**
     * Instance du gestionnaire de gabarit d'affichage.
     * @var ViewEngineInterface|null
     */
    protected $viewEngine;

    /**
     * Instance de l'écran associé.
     * @var MetaboxScreenInterface|null
     */
    protected $screen;

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
    public function __toString(): string
    {
        return $this->render();
    }

    /**
     * @inheritDoc
     */
    public function boot(): MetaboxContextInterface
    {
        if (!$this->isBooted()) {
            //events()->trigger('metabox.context.booting', [$this->getAlias(), $this]);

            $this->parseParams();

            $this->setBooted();

            //events()->trigger('metabox.context.booted', [$this->getAlias(), $this]);
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
    public function getDrivers(): array
    {
        return $this->drivers;
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        return $this->view('index', $this->params()->all());
    }

    /**
     * @inheritDoc
     */
    public function setAlias(string $alias): MetaboxContextInterface
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setDriver(MetaboxDriverInterface $driver): MetaboxContextInterface
    {
        $this->drivers[$driver->getUuid()] = $driver;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setScreen(MetaboxScreenInterface $screen): MetaboxContextInterface
    {
        $this->screen = $screen;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function view(?string $view = null, array $data = [])
    {
        if ($this->viewEngine === null) {
            $this->viewEngine = new ViewEngine();

            $this->viewEngine
                ->setDirectory($this->metabox()->resources("/views/contexts/{$this->getAlias()}"))
                ->setLoader(ContextViewLoader::class);
        }

        if (func_num_args() === 0) {
            return $this->viewEngine;
        }

        return $this->viewEngine->render($view, $data);
    }
}