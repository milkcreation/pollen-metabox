<?php

declare(strict_types=1);

namespace Pollen\Metabox;

use Closure;
use InvalidArgumentException;
use Pollen\Http\JsonResponse;
use Pollen\Http\ResponseInterface;
use Pollen\Support\Arr;
use Pollen\Support\Concerns\BootableTrait;
use Pollen\Support\Concerns\ParamsBagDelegateTrait;
use Pollen\Support\ParamsBag;
use Pollen\Support\Proxy\MetaboxProxy;
use Pollen\View\ViewEngine;
use Pollen\View\ViewEngineInterface;

class MetaboxDriver implements MetaboxDriverInterface
{
    use BootableTrait;
    use MetaboxProxy;
    use ParamsBagDelegateTrait;

    /**
     * Instance du gestionnaire de configuration.
     * @var ParamsBag
     */
    private $configBag;

    /**
     * Instance du contexte d'affichage.
     * @var MetaboxContextInterface|null
     */
    private $context;

    /**
     * Instance de l'écran d'affichage.
     * @var MetaboxScreenInterface|null
     */
    private $screen;

    /**
     * Identifiant de qualification unique.
     * @var string;
     */
    private $uuid;

    /**
     * Alias de qualification.
     * @var string
     */
    protected $alias = '';

    /**
     * Liste des arguments dynamiques passés en paramètres.
     * @var array
     */
    protected $args = [];

    /**
     * Valeur par défaut.
     * @var mixed
     */
    protected $defaultValue;

    /**
     * Liste de fonction anonyme de traitement
     * @var Closure[]
     */
    protected $handlers = [];

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var string|null
     */
    protected $parent;

    /**
     * @var int
     */
    protected $position = 0;

    /**
     * @var Closure|array|string|null
     */
    protected $render;

    /**
     * @var string|null
     */
    protected $title;

    /**
     * Valeur courante.
     * @var mixed
     */
    protected $value;

    /**
     * Liste des attributs de configuration du pilote d'affichage.
     * @var array $viewer
     */
    protected $viewer = [];

    /**
     * Instance du moteur d'affichage des gabarits.
     * @var ViewEngineInterface
     */
    protected $viewEngine;

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
    public function boot(): MetaboxDriverInterface
    {
        if (!$this->isBooted()) {
            //events()->trigger('metabox.driver.booted', [$this->getAlias(), $this]);

            $this->parseParams();

            $this->setBooted();
            //events()->trigger('metabox.driver.booting', [$this->getAlias(), $this]);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return [
            /**
             * @var array $attrs Attributs HTML du champ.
             */
            'attrs'  => [],
            /**
             * @var string $after Contenu placé après le champ.
             */
            'after'  => '',
            /**
             * @var string $before Contenu placé avant le champ.
             */
            'before' => '',
        ];
    }

    /**
     * @inheritDoc
     */
    public function getArgs(): array
    {
        return $this->args;
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
    public function getContext(): ?MetaboxContextInterface
    {
        return $this->context;
    }

    /**
     * @inheritDoc
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @inheritDoc
     */
    public function getHandlers(): array
    {
        return $this->handlers;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function getParent(): ?string
    {
        return $this->parent;
    }

    /**
     * @inheritDoc
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @inheritDoc
     */
    public function getRender()
    {
        return $this->render;
    }

    /**
     * @inheritDoc
     */
    public function getScreen(): ?MetaboxScreenInterface
    {
        return $this->screen;
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->title ?? '';
    }

    /**
     * @inheritDoc
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @inheritDoc
     */
    public function getValue(?string $key = null, $default = null)
    {
        if (is_null($key)) {
            return $this->value;
        }

        if (is_array($this->value)) {
            return Arr::get($this->value, $key, $default);
        }

        return $default;
    }

    /**
     * @inheritDoc
     */
    public function getViewer(): array
    {
        return $this->viewer;
    }

    /**
     * @inheritDoc
     */
    public function getXhrUrl(array $params = [], ?string $controller = null): string
    {
        return $this->metabox()->getXhrRouteUrl($this->getUuid(), $controller, $params);
    }

    /**
     * @inheritDoc
     */
    public function handle(): MetaboxDriverInterface
    {
        $args = $this->getArgs();

        foreach ($this->handlers as $handler) {
            $handler($this, ...$args);
        }

        $value = $this->value;
        $this->value = ($value instanceof Closure) ? $value($this, ...$args) : $value;

        $default = $this->getDefaultValue();
        if (is_array($default)) {
            $this->value = array_merge($default, $this->value ?: []);
        } elseif (is_null($this->value)) {
            $this->value = $default;
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $render = $this->get('render');

        if ($render instanceof Closure) {
            $args = $this->getArgs();
            $render = (string)$render($this, ...$args);
        }

        return $render ?: ($this->view()->exists('index') ? $this->view('index', $this->all()) : '');
    }

    /**
     * @inheritDoc
     */
    public function setArgs($args): MetaboxDriverInterface
    {
        $this->args = $args;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setAlias(string $alias): MetaboxDriverInterface
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setConfig(array $config): MetaboxDriverInterface
    {
        if (isset($config['name'])) {
            $this->setName($config['name']);
        }

        if (isset($config['parent'])) {
            $this->setParent($config['parent']);
        }

        if (isset($config['position'])) {
            $this->setPosition($config['position']);
        }

        if (isset($config['title'])) {
            $this->setTitle($config['title']);
        }

        if (isset($config['params'])) {
            $this->setParams($config['params']);
        }

        if (isset($config['value'])) {
            $this->setValue($config['value']);
        }

        if (isset($config['viewer'])) {
            $this->setViewer($config['viewer']);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setContext(MetaboxContextInterface $context): MetaboxDriverInterface
    {
        $this->context = $context;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setDefaultValue($value = null): MetaboxDriverInterface
    {
        $this->defaultValue = $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setHandler(Closure $func): MetaboxDriverInterface
    {
        $this->handlers[] = $func;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setName(string $name): MetaboxDriverInterface
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setParent(string $parent): MetaboxDriverInterface
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setPosition(int $position): MetaboxDriverInterface
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setRender(array $render): MetaboxDriverInterface
    {
        $this->render = $render;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setScreen(MetaboxScreenInterface $screen): MetaboxDriverInterface
    {
        $this->screen = $screen;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setTitle(string $title): MetaboxDriverInterface
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setUuid(string $uuid): MetaboxDriverInterface
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setValue($value): MetaboxDriverInterface
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setViewer(array $viewer): MetaboxDriverInterface
    {
        $this->viewer = $viewer;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function view(?string $view = null, array $data = [])
    {
        if ($this->viewEngine === null) {
            $directory = null;
            $overrideDir = null;
            $default = $this->metabox()->config('default.driver.viewer', []);

            $directory = $this->get('viewer.directory');
            if ($directory && !file_exists($directory)) {
                $directory = null;
            }

            $overrideDir = $this->get('viewer.override_dir');
            if ($overrideDir && !file_exists($overrideDir)) {
                $overrideDir = null;
            }

            if ($directory === null && isset($default['directory'])) {
                $default['directory'] = rtrim($default['directory'], '/') . '/' . $this->getAlias();
                if (file_exists($default['directory'])) {
                    $directory = $default['directory'];
                }
            }

            if ($overrideDir === null && isset($default['override_dir'])) {
                $default['override_dir'] = rtrim($default['override_dir'], '/') . '/' . $this->getAlias();
                if (file_exists($default['override_dir'])) {
                    $overrideDir = $default['override_dir'];
                }
            }

            if ($directory === null) {
                $directory = $this->viewDirectory();
                if (!file_exists($directory)) {
                    throw new InvalidArgumentException(
                        sprintf('Metabox [%s] must have an accessible view directory', $this->getAlias())
                    );
                }
            }

            $this->viewEngine = new ViewEngine();
            if ($container = $this->metabox()->getContainer()) {
                $this->viewEngine->setContainer($container);
            }

            $this->viewEngine->setDirectory($directory)->setDelegate($this)->setLoader(MetaboxViewLoader::class);

            if ($overrideDir !== null) {
                $this->viewEngine->addFolder('_override_dir', $overrideDir, true);
            }

            $mixins = [
                'getName',
                'getValue'
            ];

            foreach($mixins as $mixin){
                $this->viewEngine->setDelegateMixin($mixin);
            }
        }

        if (func_num_args() === 0) {
            return $this->viewEngine;
        }

        return $this->viewEngine->render($view, $data);
    }

    /**
     * @inheritDoc
     */
    public function viewDirectory(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function xhrResponse(...$args): ResponseInterface
    {
        return new JsonResponse(
            [
                'success' => true,
            ]
        );
    }
}