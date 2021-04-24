<?php

declare(strict_types=1);

namespace Pollen\Metabox;

use Closure;
use Pollen\Http\ResponseInterface;
use Pollen\Support\Concerns\BootableTraitInterface;
use Pollen\Support\Concerns\ConfigBagAwareTraitInterface;
use Pollen\Support\Concerns\ResourcesAwareTraitInterface;
use Pollen\Support\Proxy\ContainerProxyInterface;
use Pollen\Support\Proxy\RouterProxyInterface;
use Pollen\Routing\Exception\NotFoundException;

interface MetaboxManagerInterface extends
    BootableTraitInterface,
    ConfigBagAwareTraitInterface,
    ResourcesAwareTraitInterface,
    ContainerProxyInterface,
    RouterProxyInterface
{
    /**
     * @param string $alias
     * @param string|array|MetaboxDriverInterface|Closure $driverDefinition Alias de qualification|Attributs de
     *     configuration|Instance
     * @param string|null $screen
     * @param string|null $context
     *
     * @return static
     */
    public function add(
        string $alias,
        $driverDefinition,
        string $screen,
        string $context
    ): MetaboxManagerInterface;

    /**
     * Récupération de la liste des pilotes assignés.
     *
     * @return MetaboxDriverInterface[]|array
     */
    public function all(): array;

    /**
     * Chargement.
     *
     * @return static
     */
    public function boot(): MetaboxManagerInterface;

    /**
     * Repartition des éléments d'un écran. Ecran courant par défaut.
     *
     * @param string|null $screenAlias
     *
     * @return static
     */
    public function dispatch(?string $screenAlias = null): MetaboxManagerInterface;

    /**
     * Récupération d'un contexte assigné.
     *
     * @param string $alias
     *
     * @return MetaboxContextInterface|null
     */
    public function getContext(string $alias): ?MetaboxContextInterface;

    /**
     * Récupération d'un écran assigné.
     *
     * @param string $alias
     *
     * @return MetaboxScreenInterface|null
     */
    public function getScreen(string $alias): ?MetaboxScreenInterface;

    /**
     * Récupération de l'url de traitement des requêtes XHR.
     *
     * @param string $metabox Alias de qualification du pilote associé.
     * @param string|null $controller Nom de qualification du controleur de traitement de la requête XHR.
     * @param array $params Liste de paramètres complémentaire transmis dans l'url
     *
     * @return string|null
     */
    public function getXhrRouteUrl(string $metabox, ?string $controller = null, array $params = []): ?string;

    /**
     * Vérification d'existance d'un écran d'affichage.
     *
     * @param string $alias
     *
     * @return bool
     */
    public function hasScreen(string $alias): bool;

    /**
     * Déclaration d'un contexte d'affichage.
     *
     * @param string $alias
     * @param string|array|MetaboxContextInterface|null $contextDefinition
     *
     * @return static
     */
    public function registerContext(string $alias, $contextDefinition = null): MetaboxManagerInterface;

    /**
     * Déclaration d'un pilote de boîte de saisie.
     *
     * @param string $alias
     * @param string|array|MetaboxDriverInterface|null $driverDefinition
     *
     * @return static
     */
    public function registerDriver(string $alias, $driverDefinition = null): MetaboxManagerInterface;

    /**
     * Déclaration d'un pilote de boîte de saisie.
     *
     * @param string $alias
     * @param string|array|MetaboxScreenInterface|null $screenDefinition
     *
     * @return static
     */
    public function registerScreen(string $alias, $screenDefinition = null): MetaboxManagerInterface;

    /**
     * Récupération du rendu l'affichage des boîtes de saisies associées à un contexte d'un écran d'affichage.
     *
     * @param string $contextAlias
     * @param mixed ...$args
     *
     * @return string
     */
    public function render(string $contextAlias, ...$args): string;

    /**
     * Définition du contexte de base.
     *
     * @param string $baseContext
     *
     * @return static
     */
    public function setBaseContext(string $baseContext):  MetaboxManagerInterface;

    /**
     * Définition du pilote de base.
     *
     * @param string $baseDriver
     *
     * @return static
     */
    public function setBaseDriver(string $baseDriver): MetaboxManagerInterface;

    /**
     * Définition de l'écran de base.
     *
     * @param string $baseScreen
     *
     * @return static
     */
    public function setBaseScreen(string $baseScreen): MetaboxManagerInterface;

    /**
     * Définition de l'écran d'affichage courant.
     *
     * @param string $screen
     *
     * @return static
     */
    public function setCurrentScreen(string $screen): MetaboxManagerInterface;

    /**
     * Déclaration d'un jeu de boîte de saisie boîte de saisie.
     *
     * @param string $screen Nom de qualification de l'écran d'affichage.
     * @param string $context Nom de qualification du contexte de l'écran d'affichage.
     * @param string[][]|array[][]|MetaboxDriverInterface[][] $driversDefinitions Liste des boîtes de saisie.
     *
     * @return static
     */
    public function stack(string $screen, string $context, array $driversDefinitions): MetaboxManagerInterface;

    /**
     * Répartiteur de traitement d'une requête XHR.
     *
     * @param string $metabox Alias de qualification du pilote associé.
     * @param string $controller Nom de qualification du controleur de traitement de la requête.
     * @param mixed ...$args Liste des arguments passés au controleur
     *
     * @return ResponseInterface
     *
     * @throws NotFoundException
     */
    public function xhrResponseDispatcher(string $metabox, string $controller, ...$args): ResponseInterface;
}
