<?php

declare(strict_types=1);

namespace Pollen\Metabox;

use Pollen\Support\Concerns\BootableTraitInterface;
use Pollen\Support\Concerns\ParamsBagAwareTraitInterface;
use Pollen\Support\Proxy\MetaboxProxyInterface;
use Pollen\Support\Proxy\ViewProxyInterface;

interface MetaboxContextInterface extends
    BootableTraitInterface,
    ParamsBagAwareTraitInterface,
    MetaboxProxyInterface,
    ViewProxyInterface
{
    /**
     * Résolution de sortie de la classe sous forme de chaîne de caractères.
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Chargement.
     *
     * @return static
     */
    public function boot(): MetaboxContextInterface;

    /**
     * Récupération de l'alias de qualification.
     *
     * @return string
     */
    public function getAlias(): string;

    /**
     * Récupération de la liste des pilotes associés.
     *
     * @return MetaboxDriverInterface[]|array
     */
    public function getDrivers(): array;

    /**
     * Récupération du rendu d'affichage du contexte.
     *
     * @return string
     */
    public function render(): string;

    /**
     * Définition de l'alias de qualification.
     *
     * @param string $alias
     *
     * @return static
     */
    public function setAlias(string $alias): MetaboxContextInterface;

    /**
     * Définition d'un pilote associé.
     *
     * @param MetaboxDriverInterface $driver
     *
     * @return static
     */
    public function setDriver(MetaboxDriverInterface $driver): MetaboxContextInterface;

    /**
     * Définition de l'écran associé.
     *
     * @param MetaboxScreenInterface $screen
     *
     * @return static
     */
    public function setScreen(MetaboxScreenInterface $screen): MetaboxContextInterface;
}