<?php

declare(strict_types=1);

namespace Pollen\Metabox;

use Pollen\Container\BootableServiceProvider;
use Pollen\Metabox\Contexts\TabContext;
use Pollen\Metabox\Drivers\ColorDriver;
use Pollen\Metabox\Drivers\CustomHeaderDriver;
use Pollen\Metabox\Drivers\ExcerptDriver;
use Pollen\Metabox\Drivers\FilefeedDriver;
use Pollen\Metabox\Drivers\IconDriver;
use Pollen\Metabox\Drivers\ImagefeedDriver;
use Pollen\Metabox\Drivers\OrderDriver;
use Pollen\Metabox\Drivers\PostfeedDriver;
use Pollen\Metabox\Drivers\RelatedTermDriver;
use Pollen\Metabox\Drivers\SlidefeedDriver;
use Pollen\Metabox\Drivers\SubtitleDriver;
use Pollen\Metabox\Drivers\VideofeedDriver;

class MetaboxServiceProvider extends BootableServiceProvider
{
    /**
     * @inheritDoc
     */
    protected $provides = [
        MetaboxManagerInterface::class,
        MetaboxContext::class,
        MetaboxDriver::class,
        MetaboxScreen::class,
        ColorDriver::class,
        CustomHeaderDriver::class,
        ExcerptDriver::class,
        FilefeedDriver::class,
        IconDriver::class,
        ImagefeedDriver::class,
        OrderDriver::class,
        PostfeedDriver::class,
        RelatedTermDriver::class,
        SlidefeedDriver::class,
        SubtitleDriver::class,
        TabContext::class,
        VideofeedDriver::class,
        'metabox.view-engine.context',
        'metabox.view-engine.driver',
    ];

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share(
            MetaboxManagerInterface::class,
            function () {
                return new MetaboxManager([], $this->getContainer());
            }
        );

        $this->getContainer()->add(
            MetaboxContext::class,
            function () {
                return new MetaboxContext($this->getContainer()->get(MetaboxManagerInterface::class));
            }
        );

        $this->getContainer()->add(
            MetaboxDriver::class,
            function () {
                return new MetaboxDriver($this->getContainer()->get(MetaboxManagerInterface::class));
            }
        );

        $this->getContainer()->add(
            MetaboxScreen::class,
            function () {
                return new MetaboxScreen($this->getContainer()->get(MetaboxManagerInterface::class));
            }
        );

        $this->registerContexts();
        $this->registerDrivers();
    }

    /**
     * Déclaration des contextes.
     *
     * @return void
     */
    public function registerContexts(): void
    {
        $this->getContainer()->add(
            TabContext::class,
            function () {
                return new TabContext($this->getContainer()->get(MetaboxManagerInterface::class));
            }
        );
    }

    /**
     * Déclaration des pilotes.
     *
     * @return void
     */
    public function registerDrivers(): void
    {
        $this->getContainer()->add(
            ColorDriver::class,
            function () {
                return new ColorDriver($this->getContainer()->get(MetaboxManagerInterface::class));
            }
        );
        $this->getContainer()->add(
            CustomHeaderDriver::class,
            function () {
                return new CustomHeaderDriver($this->getContainer()->get(MetaboxManagerInterface::class));
            }
        );
        $this->getContainer()->add(
            ExcerptDriver::class,
            function () {
                return new ExcerptDriver($this->getContainer()->get(MetaboxManagerInterface::class));
            }
        );
        $this->getContainer()->add(
            FilefeedDriver::class,
            function () {
                return new FilefeedDriver($this->getContainer()->get(MetaboxManagerInterface::class));
            }
        );
        $this->getContainer()->add(
            IconDriver::class,
            function () {
                return new IconDriver($this->getContainer()->get(MetaboxManagerInterface::class));
            }
        );
        $this->getContainer()->add(
            OrderDriver::class,
            function () {
                return new OrderDriver($this->getContainer()->get(MetaboxManagerInterface::class));
            }
        );
        $this->getContainer()->add(
            ImagefeedDriver::class,
            function () {
                return new ImagefeedDriver($this->getContainer()->get(MetaboxManagerInterface::class));
            }
        );
        $this->getContainer()->add(
            PostfeedDriver::class,
            function () {
                return new PostfeedDriver($this->getContainer()->get(MetaboxManagerInterface::class));
            }
        );
        $this->getContainer()->add(
            RelatedTermDriver::class,
            function () {
                return new RelatedTermDriver($this->getContainer()->get(MetaboxManagerInterface::class));
            }
        );
        $this->getContainer()->add(
            SlidefeedDriver::class,
            function () {
                return new SlidefeedDriver($this->getContainer()->get(MetaboxManagerInterface::class));
            }
        );
        $this->getContainer()->add(
            SubtitleDriver::class,
            function () {
                return new SubtitleDriver($this->getContainer()->get(MetaboxManagerInterface::class));
            }
        );
        $this->getContainer()->add(
            VideofeedDriver::class,
            function () {
                return new VideofeedDriver($this->getContainer()->get(MetaboxManagerInterface::class));
            }
        );
    }
}