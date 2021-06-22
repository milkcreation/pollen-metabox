<?php

declare(strict_types=1);

namespace Pollen\Metabox;

use Pollen\Support\Concerns\BootableTrait;
use Pollen\Support\Concerns\ParamsBagAwareTrait;
use Pollen\Support\Proxy\MetaboxProxy;
use Pollen\View\View;
use Pollen\View\Engines\Plates\PlatesViewEngine;
use Pollen\View\ViewInterface;

class MetaboxContext implements MetaboxContextInterface
{
    use BootableTrait;
    use ParamsBagAwareTrait;
    use MetaboxProxy;

    /**
     * Alias de qualification.
     */
    protected string $alias = '';

    /**
     * Liste des pilotes déclarés.
     * @var MetaboxDriverInterface[]|array
     */
    protected array $drivers = [];

    /**
     * Instance du gestionnaire de gabarit d'affichage.
     */
    protected ?ViewInterface $view = null;

    /**
     * Instance de l'écran associé.
     */
    protected ?MetaboxScreenInterface $screen = null;

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
        if ($this->view === null) {
            $this->view = View::createFromPlates(
                function (PlatesViewEngine $platesViewEngine) {
                    $platesViewEngine
                        ->setTemplateClass(ContextTemplate::class)
                        ->setDirectory($this->metabox()->resources("/views/contexts/{$this->getAlias()}"));

                    return $platesViewEngine;
                }
            );
        }

        if (func_num_args() === 0) {
            return $this->view;
        }

        return $this->view->render($view, $data);
    }
}