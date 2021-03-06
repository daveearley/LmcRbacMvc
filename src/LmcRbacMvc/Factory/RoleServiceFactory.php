<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace LmcRbacMvc\Factory;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use LmcRbacMvc\Exception\RuntimeException;
use LmcRbacMvc\Service\RoleService;

/**
 * Factory to create the role service
 *
 * @author  Michaël Gallego <mic.gallego@gmail.com>
 * @license MIT
 */
class RoleServiceFactory implements FactoryInterface
{
    /**
     * {@inheritDoc}
     * @return RoleService
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var \LmcRbacMvc\Options\ModuleOptions $moduleOptions */
        $moduleOptions = $serviceLocator->get('LmcRbacMvc\Options\ModuleOptions');

        /* @var \LmcRbacMvc\Identity\IdentityProviderInterface $identityProvider */
        $identityProvider = $serviceLocator->get($moduleOptions->getIdentityProvider());

        $roleProviderConfig = $moduleOptions->getRoleProvider();

        if (empty($roleProviderConfig)) {
            throw new RuntimeException('No role provider has been set for LmcRbacMvc');
        }

        /* @var \LmcRbacMvc\Role\RoleProviderPluginManager $pluginManager */
        $pluginManager = $serviceLocator->get('LmcRbacMvc\Role\RoleProviderPluginManager');

        /* @var \LmcRbacMvc\Role\RoleProviderInterface $roleProvider */
        $roleProvider = $pluginManager->get(key($roleProviderConfig), current($roleProviderConfig));

        /* @var \Rbac\Traversal\Strategy\TraversalStrategyInterface $traversalStrategy */
        $traversalStrategy = $serviceLocator->get('Rbac\Rbac')->getTraversalStrategy();

        $roleService = new RoleService($identityProvider, $roleProvider, $traversalStrategy);
        $roleService->setGuestRole($moduleOptions->getGuestRole());

        return $roleService;
    }
}
