<?php

declare(strict_types=1);

namespace Pollen\Metabox;

use Closure;
use Pollen\Http\ResponseInterface;
use Pollen\Support\Concerns\BootableTraitInterface;
use Pollen\Support\Concerns\ParamsBagDelegateTraitInterface;
use Pollen\Support\Proxy\HttpRequestProxyInterface;
use Pollen\Support\Proxy\MetaboxProxyInterface;
use Pollen\View\ViewEngineInterface;

interface MetaboxDriverInterface extends
    BootableTraitInterface,
    HttpRequestProxyInterface,
    MetaboxProxyInterface,
    ParamsBagDelegateTraitInterface
{
    /**
     * Délégation d'appel des méthodes du ParamBagTrait.
     *
     * @param string $method
     * @param array $arguments
     *
     * @return mixed
     */
    public function __call(string $method, array $arguments);

    /**
     * Résolution de sortie de la classe sous forme d'une chîane de caractères.
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Chargement.
     *
     * @return static
     */
    public function boot(): MetaboxDriverInterface;

    /**
     * Récupération de la liste des arguments dynamiques passés en paramètres.
     *
     * @return array
     */
    public function getArgs(): array;

    /**
     * Récupération de l'identifiant de qualification dans le gestionnaire.
     *
     * @return string
     */
    public function getAlias(): string;

    /**
     * Récupération de l'instance du contexte d'affichage associé.
     *
     * @return MetaboxContextInterface|null
     */
    public function getContext(): ?MetaboxContextInterface;

    /**
     * Récupération de la valeur par défaut.
     *
     * @return mixed
     */
    public function getDefaultValue();

    /**
     * Récupération des fonctions de traitement
     *
     * @return Closure[]|array
     */
    public function getHandlers(): array;

    /**
     * Récupération du nom de qualification dans la requête d'enregistrement des données.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Récupération du parent.
     *
     * @return string|null
     */
    public function getParent(): ?string;

    /**
     * Récupération de la position.
     *
     * @return int
     */
    public function getPosition(): int;

    /**
     * Récupération des paramètres de configuration de rendu.
     *
     * @return string|Closure
     */
    public function getRender();

    /**
     * Récupération de l'instance de l'écran d'affichage associé.
     *
     * @return MetaboxScreenInterface|null
     */
    public function getScreen(): ?MetaboxScreenInterface;

    /**
     * Récupération de l'intitulé de qualification.
     *
     * @return string
     */
    public function getTitle(): string;

    /**
     * Récupération de l'identifiant de qualification unique.
     *
     * @return string
     */
    public function getUuid(): string;

    /**
     * Récupération de la valeur courante.
     *
     * @param string|null $key Indice de qualification de la valeur. Syntaxe à point permise.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return mixed
     */
    public function getValue(?string $key = null, $default = null);

    /**
     * Récupération de la configuration du gestionnaire de gabarits.
     *
     * @return array
     */
    public function getViewer(): array;

    /**
     * Récupération de l'url de traitement des requêtes XHR.
     *
     * @param array $params
     * @param string|null $controller
     *
     * @return string
     */
    public function getXhrUrl(array $params = [], ?string $controller = null): string;

    /**
     * Traitement.
     *
     * @return static
     */
    public function handle(): MetaboxDriverInterface;

    /**
     * Récupération de l'affichage du contenu de la boîte de saisie.
     *
     * @return string
     */
    public function render(): string;

    /**
     * Définition de la liste des arguments dynamiques passés en paramètres.
     *
     * @param array $args
     *
     * @return static
     */
    public function setArgs(array $args): MetaboxDriverInterface;

    /**
     * Définition de l'alias de qualification.
     *
     * @param string $alias
     *
     * @return static
     */
    public function setAlias(string $alias): MetaboxDriverInterface;

    /**
     * Définition de la configuration.
     *
     * @param array $config
     *
     * @return static
     */
    public function setConfig(array $config): MetaboxDriverInterface;

    /**
     * Définition de l'instance du contexte d'affichage.
     *
     * @param MetaboxContextInterface $context
     *
     * @return static
     */
    public function setContext(MetaboxContextInterface $context): MetaboxDriverInterface;

    /**
     * Définition de valeur(s) par défaut.
     *
     * @param mixed $value
     *
     * @return static
     */
    public function setDefaultValue($value = null): MetaboxDriverInterface;

    /**
     * Définition d'une fonction de traitement.
     *
     * @param Closure $func
     *
     * @return static
     */
    public function setHandler(Closure $func): MetaboxDriverInterface;

    /**
     * Définition de la clé d'indice de qualification de la soumission du formulaire d'enregistrement des données.
     *
     * @param string $name
     *
     * @return static
     */
    public function setName(string $name): MetaboxDriverInterface;

    /**
     * Définition du parent.
     *
     * @param string $parent
     *
     * @return static
     */
    public function setParent(string $parent): MetaboxDriverInterface;

    /**
     * Définition de la position.
     *
     * @param int $position
     *
     * @return static
     */
    public function setPosition(int $position): MetaboxDriverInterface;

    /**
     * Définition de la configuration du rendu.
     *
     * @param array $render
     *
     * @return static
     */
    public function setRender(array $render): MetaboxDriverInterface;

    /**
     * Définition de l'instance de l'écran d'affichage.
     *
     * @param MetaboxScreenInterface $screen
     *
     * @return static
     */
    public function setScreen(MetaboxScreenInterface $screen): MetaboxDriverInterface;

    /**
     * Définition du titre.
     *
     * @param string $title
     *
     * @return static
     */
    public function setTitle(string $title): MetaboxDriverInterface;

    /**
     * Définition de la valeur.
     *
     * @param string|int|array|Closure $value
     *
     * @return static
     */
    public function setValue($value): MetaboxDriverInterface;

    /**
     * Définition de la configuration du moteur de gabarits.
     *
     * @param array $viewer
     *
     * @return static
     */
    public function setViewer(array $viewer): MetaboxDriverInterface;

    /**
     * Définition de l'identifiant de qualification unique.
     *
     * @param string $uuid
     *
     * @return static
     */
    public function setUuid(string $uuid): MetaboxDriverInterface;

    /**
     * Récupération d'un instance du controleur de liste des gabarits d'affichage ou d'un gabarit d'affichage.
     * {@internal Si aucun argument n'est passé à la méthode, retourne l'instance du controleur de liste.}
     * {@internal Sinon récupére l'instance du gabarit d'affichage et passe les variables en argument.}
     *
     * @param string|null view Nom de qualification du gabarit.
     * @param array $data Liste des variables passées en argument.
     *
     * @return ViewEngineInterface|string
     */
    public function view(?string $view = null, array $data = []);

    /**
     * Chemin absolu du répertoire des gabarits d'affichage.
     *
     * @return string
     */
    public function viewDirectory(): string;

    /**
     * Contrôleur de traitement des requêtes XHR.
     *
     * @param array ...$args
     *
     * @return ResponseInterface
     */
    public function xhrResponse(...$args): ResponseInterface;
}