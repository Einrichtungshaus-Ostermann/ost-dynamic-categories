<?php declare(strict_types=1);

/**
 * Einrichtungshaus Ostermann GmbH & Co. KG - Dynamic Categories
 *
 * Adds the functionality to create ProductStream like Streams as Category definition.
 *
 * 1.0.0
 * - initial release
 *
 * 1.0.1
 * - fixed multiple category assignments
 *
 * 1.0.2
 * - fixed plugin descriptions
 *
 * @package   OstDynamicCategories
 *
 * @author    Tim Windelschmidt <tim.windelschmidt@ostermann.de>
 * @copyright 2018 Einrichtungshaus Ostermann GmbH & Co. KG
 * @license   proprietary
 */

namespace OstDynamicCategories;

use Shopware\Components\Plugin;
use Shopware\Components\Plugin\Context;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class OstDynamicCategories extends Plugin
{
    /**
     * ...
     *
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        // set plugin parameters
        $container->setParameter('ost_dynamic_categories.plugin_dir', $this->getPath() . '/');
        $container->setParameter('ost_dynamic_categories.view_dir', $this->getPath() . '/Resources/views/');

        // call parent builder
        parent::build($container);
    }

    /**
     * Activate the plugin.
     *
     * @param Context\ActivateContext $context
     */
    public function activate(Context\ActivateContext $context)
    {
        // clear complete cache after we activated the plugin
        $context->scheduleClearCache($context::CACHE_LIST_ALL);
    }

    /**
     * Install the plugin.
     *
     * @param Context\InstallContext $context
     *
     * @throws \Exception
     */
    public function install(Context\InstallContext $context)
    {
        // install the plugin
        $installer = new Setup\Install(
            $this,
            $context,
            $this->container->get('models'),
            $this->container->get('shopware_attribute.crud_service')
        );
        $installer->install();

        // update it to current version
        $updater = new Setup\Update(
            $this,
            $context
        );
        $updater->install();

        // call default installer
        parent::install($context);
    }

    /**
     * Update the plugin.
     *
     * @param Context\UpdateContext $context
     */
    public function update(Context\UpdateContext $context)
    {
        // update the plugin
        $updater = new Setup\Update(
            $this,
            $context
        );
        $updater->update($context->getCurrentVersion());

        // call default updater
        parent::update($context);
    }

    /**
     * Uninstall the plugin.
     *
     * @param Context\UninstallContext $context
     *
     * @throws \Exception
     */
    public function uninstall(Context\UninstallContext $context)
    {
        // uninstall the plugin
        $uninstaller = new Setup\Uninstall(
            $this,
            $context,
            $this->container->get('models'),
            $this->container->get('shopware_attribute.crud_service')
        );
        $uninstaller->uninstall();

        // clear complete cache
        $context->scheduleClearCache($context::CACHE_LIST_ALL);

        // call default uninstaller
        parent::uninstall($context);
    }
}
