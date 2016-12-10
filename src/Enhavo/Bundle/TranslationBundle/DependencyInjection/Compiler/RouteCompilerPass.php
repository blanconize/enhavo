<?php
/**
 * RouteCompilerPass.php
 *
 * @since 27/11/16
 * @author gseidel
 */

namespace Enhavo\Bundle\TranslationBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RouteCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $this->overwriteRouteType($container);
        $this->overwriteSlugType($container);
        $this->overwriteRouteValidator($container);
    }

    protected function overwriteRouteType(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('enhavo_app.form.route');
        $definition->setClass('Enhavo\Bundle\TranslationBundle\Form\Type\RouteType');
        $definition->addArgument($container->getParameter('enhavo_translation.translate'));
    }

    protected function overwriteSlugType(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('enhavo_slug');
        $definition->setClass('Enhavo\Bundle\TranslationBundle\Form\Type\SlugType');
        $definition->addArgument($container->getParameter('enhavo_translation.translate'));
    }

    protected function overwriteRouteValidator(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('enhavo_app.validator.unique_url');
        $definition->setClass('Enhavo\Bundle\TranslationBundle\Validator\Constraints\RouteValidator');
    }
}