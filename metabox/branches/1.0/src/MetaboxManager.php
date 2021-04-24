<?php

declare(strict_types=1);

namespace Pollen\Metabox;

use Exception;
use Illuminate\Support\Collection;
use Pollen\Http\ResponseInterface;
//use Pollen\Metabox\Contexts\TabContext;
//use Pollen\Metabox\Drivers\ColorDriver;
//use Pollen\Metabox\Drivers\CustomHeaderDriver;
//use Pollen\Metabox\Drivers\ExcerptDriver;
//use Pollen\Metabox\Drivers\FilefeedDriver;
//use Pollen\Metabox\Drivers\IconDriver;
//use Pollen\Metabox\Drivers\ImagefeedDriver;
//use Pollen\Metabox\Drivers\OrderDriver;
//use Pollen\Metabox\Drivers\PostfeedDriver;
//use Pollen\Metabox\Drivers\RelatedTermDriver;
//use Pollen\Metabox\Drivers\SlidefeedDriver;
//use Pollen\Metabox\Drivers\SubtitleDriver;
//use Pollen\Metabox\Drivers\VideofeedDriver;
use Pollen\Routing\RouteInterface;
use Pollen\Support\Concerns\BootableTrait;
use Pollen\Support\Concerns\ConfigBagAwareTrait;
use Pollen\Support\Concerns\ResourcesAwareTrait;
use Pollen\Support\Exception\ManagerRuntimeException;
use Pollen\Support\Proxy\ContainerProxy;
use Pollen\Support\Proxy\RouterProxy;
use Pollen\Routing\Exception\NotFoundException;
use Psr\Container\ContainerInterface as Container;
use Ramsey\Uuid\Uuid;
use RuntimeException;

class MetaboxManager implements MetaboxManagerInterface
{
    use BootableTrait;
    use ConfigBagAwareTrait;
    use ResourcesAwareTrait;
    use ContainerProxy;
    use RouterProxy;

    /**
     * Instance principale.
     * @var static|null
     */
    private static $instance;

    /**
     * Définition des pilotes par défaut.
     * @var array
     */
    private $defaultDrivers = [
        //'color'         => ColorDriver::class,
        //'custom-header' => CustomHeaderDriver::class,
        //'excerpt'       => ExcerptDriver::class,
        //'filefeed'      => FilefeedDriver::class,
        //'icon'          => IconDriver::class,
        //'imagefeed'     => ImagefeedDriver::class,
        //'order'         => OrderDriver::class,
        //'postfeed'      => PostfeedDriver::class,
        //'related-term'  => RelatedTermDriver::class,
        //'slidefeed'     => SlidefeedDriver::class,
        //'subtitle'      => SubtitleDriver::class,
        //'videofeed'     => VideofeedDriver::class,
    ];

    /**
     * Définition des contextes par défaut.
     * @var array
     */
    private $defaultContexts = [
        //'tab' => TabContext::class,
    ];

    /**
     * Définition du contexte de base.
     * @var MetaboxContextInterface
     */
    protected $baseContext = MetaboxContext::class;

    /**
     * Définition du pilote de base.
     * @var MetaboxDriverInterface
     */
    protected $baseDriver = MetaboxDriver::class;

    /**
     * Définition de l'écran de base.
     * @var MetaboxScreenInterface
     */
    protected $baseScreen = MetaboxScreen::class;

    /**
     * Liste des contextes assignés.
     * @var MetaboxContext[]
     */
    protected $contexts = [];

    /**
     * Liste des contextes déclarés.
     * @var MetaboxContextInterface[]|string[]|array
     */
    protected $contextDefinitions = [];

    /**
     * Instance de l'écran courant.
     * @var string|null
     */
    protected $currentScreen;

    /**
     * Liste des pilotes assignés.
     * @var MetaboxDriver[]
     */
    protected $drivers = [];

    /**
     * Liste des pilotes déclarées.
     * @var MetaboxDriverInterface[]|string[]|array
     */
    protected $driverDefinitions = [];

    /**
     * Cartographie des pilotes déclarés.
     * @var MetaboxDriverInterface[]
     */
    protected $driversMap = [];

    /**
     * Liste des écrans assignés.
     * @var MetaboxScreen[]
     */
    protected $screens = [];

    /**
     * Liste des écrans déclarées.
     * @var MetaboxScreenInterface[]|string[]|array
     */
    protected $screenDefinitions = [];

    /**
     * Route de traitement des requêtes XHR.
     * @var RouteInterface|null
     */
    protected $xhrRoute;

    /**
     * @param array $config
     * @param Container|null $container
     */
    public function __construct(array $config = [], ?Container $container = null)
    {
        $this->setConfig($config);

        if ($container !== null) {
            $this->setContainer($container);
        }

        $this->setResourcesBaseDir(dirname(__DIR__) . '/resources');

        if ($this->config('boot_enabled', true)) {
            $this->boot();
        }

        if (!self::$instance instanceof static) {
            self::$instance = $this;
        }
    }

    /**
     * Récupération de l'instance principale.
     *
     * @return static
     */
    public static function getInstance(): MetaboxManagerInterface
    {
        if (self::$instance instanceof self) {
            return self::$instance;
        }
        throw new ManagerRuntimeException(sprintf('Unavailable [%s] instance', __CLASS__));
    }

    /**
     * @inheritDoc
     */
    public function add(string $alias, $driverDefinition, string $screen, string $context): MetaboxManagerInterface
    {
        $driverMapKey = $this->getDriverMapKey($alias, $context, $screen);
        if (isset($this->driversMap[$driverMapKey])) {
            throw new RuntimeException(
                sprintf(
                    'Another MetaboxDriver [%s] already exists for screen [%s] and context [%s]',
                    $alias,
                    $screen,
                    $context
                )
            );
        }

        $this->driversMap[$driverMapKey] = [
            'alias'   => $alias,
            'context' => $context,
            'driver'  => $driverDefinition,
            'screen'  => $screen,
        ];

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function all(): array
    {
        return $this->drivers;
    }

    /**
     * @inheritDoc
     */
    public function boot(): MetaboxManagerInterface
    {
        if (!$this->isBooted()) {
            //events()->trigger('metabox.booting', [$this]);

            if ($router = $this->router()) {
                $this->xhrRoute = $router->xhr(
                    '/api/' . md5('metabox') . '/{metabox}/{controller}',
                    [$this, 'xhrResponseDispatcher']
                );
            }

            try {
                foreach ($this->defaultDrivers as $alias => $abstract) {
                    if ($this->containerHas($abstract)) {
                        $this->registerDriver($alias, $abstract);
                    } elseif (class_exists($abstract)) {
                        $this->registerDriver($alias, new $abstract($this));
                    }
                }
                foreach ($this->defaultContexts as $alias => $abstract) {
                    if ($this->containerHas($abstract)) {
                        $this->registerContext($alias, $abstract);
                    } elseif (class_exists($abstract)) {
                        $this->registerContext($alias, new $abstract($this));
                    }
                }
            } catch (Exception $e) {
                throw new RuntimeException($e->getMessage());
            }

            $this->setBooted();

            //events()->trigger('metabox.booted', [$this]);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function dispatch(?string $screenAlias = null): MetaboxManagerInterface
    {
        if ($screenAlias !== null) {
            $screen = $this->getScreen($screenAlias);
        } elseif ($this->currentScreen !== null) {
            $screen = $this->getScreen($this->currentScreen);
        } else {
            $screen = null;
        }
        if (!$screen instanceof MetaboxScreenInterface) {
            throw new RuntimeException('MetaboxScreen not found');
        }

        $driversCollect = (new Collection($this->driversMap))->filter(
            function (array $map) use ($screen) {
                return $map['screen'] === $screen->getAlias();
            }
        );

        $contexts = [];
        $contextAliases = $driversCollect->unique('context')->pluck('context');
        foreach ($contextAliases as $contextAlias) {
            $contexts[$contextAlias] = $this->getContext($contextAlias);
        }

        foreach ($driversCollect as $uuid => $map) {
            $driver = $this->_fetchDriver($uuid);
            $context = $contexts[$map['context']];
            $this->drivers[$uuid] = $driver->setScreen($screen)->setContext($context);
            $context->setDriver($driver);
        }

        $driversOrderCollect = new Collection($this->drivers);
        $max = $driversOrderCollect->max(
            function (MetaboxDriverInterface $driver) {
                return $driver->getPosition();
            }
        );
        $pad = 0;
        $driversOrderCollect->each(
            function (MetaboxDriver $driver) use (&$pad, $max) {
                return $driver->setPosition($driver->getPosition() ?: ++$pad + $max);
            }
        );

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getContext(string $alias): ?MetaboxContextInterface
    {
        if ($exists = $this->contexts[$alias] ?? null) {
            return $exists;
        }
        $def = $this->contextDefinitions[$alias] ?? null;

        $params = [];
        if (is_array($def)) {
            $params = $def;
            $def = null;
        }
        try {
            if ($def === null) {
                $abstract = $this->baseContext;
                $context = $this->containerHas($abstract) ? $this->containerGet($abstract) : new $abstract($this);
            } elseif ($def instanceof MetaboxContextInterface) {
                $context = $def;
            } elseif (is_string($def)) {
                if ($this->containerHas($def)) {
                    $context = $this->containerGet($def);
                } elseif (class_exists($def)) {
                    $context = new $def($this);
                }
            }
        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage());
        }

        if (!isset($context) || !is_object($context) || !($context instanceof MetaboxContextInterface)) {
            throw new RuntimeException(sprintf('Unable to define MetaboxContext with alias [%s]', $alias));
        }

        $this->contexts[$alias] = $context->setAlias($alias);
        $this->contexts[$alias]->setParams(
            array_merge($context->defaultParams(), $this->config("context.$alias", []), $params)
        );

        return $this->contexts[$alias]->boot();
    }

    /**
     * @param string $driver
     * @param string $screen
     * @param string $context
     *
     * @return string
     */
    public function getDriverMapKey(string $driver, string $screen, string $context): string
    {
        return Uuid::uuid5(Uuid::NAMESPACE_URL, $driver . $screen . $context)->__toString();
    }

    /**
     * @inheritDoc
     */
    public function getScreen(string $alias): ?MetaboxScreenInterface
    {
        if ($exists = $this->screens[$alias] ?? null) {
            return $exists;
        }

        if (!$this->hasScreen($alias)) {
            return null;
        }
        $def = $this->screenDefinitions[$alias] ?? null;

        $params = [];
        if (is_array($def)) {
            $params = $def;
            $def = null;
        }
        try {
            if ($def === null) {
                $abstract = $this->baseScreen;
                $screen = $this->containerHas($abstract) ? $this->containerGet($abstract) : new $abstract($this);
            } elseif ($def instanceof MetaboxScreenInterface) {
                $screen = $def;
            } elseif (is_string($def)) {
                if ($this->containerHas($def)) {
                    $screen = $this->containerGet($def);
                } elseif (class_exists($def)) {
                    $screen = new $def($this);
                }
            }
        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage());
        }

        if (!isset($screen) || !is_object($screen) || !($screen instanceof MetaboxScreenInterface)) {
            throw new RuntimeException(sprintf('Unable to define MetaboxScreen with alias [%s]', $alias));
        }

        $this->screens[$alias] = $screen->setAlias($alias);
        $this->screens[$alias]->setParams(
            array_merge($screen->defaultParams(), $this->config("screen.$alias", []), $params)
        );

        return $this->screens[$alias]->boot();
    }

    /**
     * @inheritDoc
     */
    public function getXhrRouteUrl(string $metabox, ?string $controller = null, array $params = []): ?string
    {
        if ($this->xhrRoute instanceof RouteInterface && ($router = $this->router())) {
            $controller = $controller ?? 'xhrResponse';

            return $router->getRouteUrl($this->xhrRoute, array_merge($params, compact('metabox', 'controller')));
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    public function hasScreen(string $alias): bool
    {
        return (bool)(new Collection($this->driversMap))->first(function($map) use ($alias) {
            return $alias === $map['screen'];
        });
    }

    /**
     * @inheritDoc
     */
    public function registerContext(string $alias, $contextDefinition = null): MetaboxManagerInterface
    {
        if (array_key_exists($alias, $this->contextDefinitions)) {
            throw new RuntimeException(sprintf('MetaboxContext with alias [%s] already registered', $alias));
        }
        $this->contextDefinitions[$alias] = $contextDefinition;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function registerDriver(string $alias, $driverDefinition = null): MetaboxManagerInterface
    {
        if (array_key_exists($alias, $this->driverDefinitions)) {
            throw new RuntimeException(sprintf('MetaboxDriver with alias [%s] already registered', $alias));
        }
        $this->driverDefinitions[$alias] = $driverDefinition;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function registerScreen(string $alias, $screenDefinition = null): MetaboxManagerInterface
    {
        if (array_key_exists($alias, $this->screenDefinitions)) {
            throw new RuntimeException(sprintf('MetaboxScreen with alias [%s] already registered', $alias));
        }
        $this->screenDefinitions[$alias] = $screenDefinition;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function render(string $contextAlias, ...$args): string
    {
        if ($context = $this->getContext($contextAlias)) {
            foreach ($context->getDrivers() as $driver) {
                $driver->setArgs($args)->handle();
            }
            return $context->render();
        }

        return '';
    }

    /**
     * @inheritDoc
     */
    public function setBaseContext(string $baseContext): MetaboxManagerInterface
    {
        $this->baseContext = $baseContext;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setBaseDriver(string $baseDriver): MetaboxManagerInterface
    {
        $this->baseDriver = $baseDriver;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setBaseScreen(string $baseScreen): MetaboxManagerInterface
    {
        $this->baseScreen = $baseScreen;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setCurrentScreen(string $screen): MetaboxManagerInterface
    {
        $this->currentScreen = $screen;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function stack(string $screen, string $context, array $driversDefinitions): MetaboxManagerInterface
    {
        foreach ($driversDefinitions as $alias => $driversDefinition) {
            try {
                $this->add($alias, $driversDefinition, $screen, $context);
            } catch (Exception $e) {
                throw new RuntimeException($e->getMessage());
            }
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function xhrResponseDispatcher(string $metabox, string $controller, ...$args): ResponseInterface
    {
        try {
            $driver = $this->_fetchDriver($metabox);
        } catch (Exception $e) {
            throw new NotFoundException(
                sprintf('MetaboxDriver [%s] return exception : %s', $metabox, $e->getMessage()),
                'MetaboxDriver Error',
                $e
            );
        }

        if ($driver !== null) {
            try {
                return $driver->{$controller}(...$args);
            } catch (Exception $e) {
                throw new NotFoundException(
                    sprintf('MetaboxDriver [%s] Controller [%s] call return exception', $controller, $metabox),
                    'MetaboxDriver Error',
                    $e
                );
            }
        }

        throw new NotFoundException(
            sprintf('MetaboxDriver [%s] unreachable', $metabox),
            'MetaboxDriver Error'
        );
    }

    /**
     * Retrouve l'instance d'un pilote.
     *
     * @param string $uuid
     *
     * @return MetaboxDriverInterface
     */
    private function _fetchDriver(string $uuid): MetaboxDriverInterface
    {
        if ($exists = $this->drivers[$uuid] ?? null) {
            return $exists;
        }

        if (!$map = $this->driversMap[$uuid] ?? null) {
            throw new RuntimeException('Any MetaboxDriver [%s] mapped');
        }

        $alias = $map['alias'];
        $driverDef = $map['driver'];

        $config = [];
        if (is_array($driverDef)) {
            $config = $driverDef;
            $driverDef = $config['driver'] ?? null;
            unset($config['driver']);
        }
        if (is_string($driverDef) && isset($this->driverDefinitions[$driverDef])) {
            $driverDef = $this->driverDefinitions[$driverDef];
        }
        try {
            if ($driverDef === null) {
                $abstract = $this->baseDriver;
                $driver = $this->containerHas($abstract) ? $this->containerGet($abstract) : new $abstract($this);
            } elseif ($driverDef instanceof MetaboxDriverInterface) {
                $driver = clone $driverDef;
            } elseif (is_string($driverDef)) {
                if ($this->containerHas($driverDef)) {
                    $driver = $this->containerGet($driverDef);
                } elseif (class_exists($driverDef)) {
                    $driver = new $driverDef($this);
                }
            }
        } catch (Exception $e) {
            throw new RuntimeException($e->getMessage());
        }
        if (!isset($driver) || !is_object($driver) || !($driver instanceof MetaboxDriverInterface)) {
            throw new RuntimeException(sprintf('Unable to define MetaboxDriver with alias [%s]', $alias));
        }

        $driver
            ->setAlias($alias)
            ->setUuid($uuid)
            ->setConfig(array_merge($this->config("driver.$alias", []), $config));

        return $driver->boot();
    }
}