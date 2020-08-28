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

namespace LmcRbacMvcTest\Factory;

use Laminas\ServiceManager\ServiceManager;
use LmcRbacMvc\Factory\RoleServiceFactory;
use LmcRbacMvc\Options\ModuleOptions;
use LmcRbacMvc\Role\RoleProviderPluginManager;

/**
 * @covers \LmcRbacMvc\Factory\RoleServiceFactory
 */
class RoleServiceFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testFactory()
    {
        $options = new ModuleOptions([
            'identity_provider'    => 'LmcRbacMvc\Identity\AuthenticationProvider',
            'guest_role'           => 'guest',
            'role_provider'        => [
                'LmcRbacMvc\Role\InMemoryRoleProvider' => [
                    'foo'
                ]
            ]
        ]);

        $serviceManager = new ServiceManager();
        $serviceManager->setService('LmcRbacMvc\Options\ModuleOptions', $options);
        $serviceManager->setService('LmcRbacMvc\Role\RoleProviderPluginManager', new RoleProviderPluginManager());
        $serviceManager->setService(
            'LmcRbacMvc\Identity\AuthenticationProvider',
            $this->getMock('LmcRbacMvc\Identity\IdentityProviderInterface')
        );

        $traversalStrategy = $this->getMock('Rbac\Traversal\Strategy\TraversalStrategyInterface');
        $rbac              = $this->getMock('Rbac\Rbac', [], [], '', false);

        $rbac->expects($this->once())->method('getTraversalStrategy')->will($this->returnValue($traversalStrategy));

        $serviceManager->setService('Rbac\Rbac', $rbac);

        $factory     = new RoleServiceFactory();
        $roleService = $factory->createService($serviceManager);

        $this->assertInstanceOf('LmcRbacMvc\Service\RoleService', $roleService);
        $this->assertEquals('guest', $roleService->getGuestRole());
        $this->assertAttributeSame($traversalStrategy, 'traversalStrategy', $roleService);
    }

    public function testThrowExceptionIfNoRoleProvider()
    {
        $this->setExpectedException('LmcRbacMvc\Exception\RuntimeException');

        $options = new ModuleOptions([
            'identity_provider' => 'LmcRbacMvc\Identity\AuthenticationProvider',
            'guest_role'        => 'guest',
            'role_provider'     => []
        ]);

        $serviceManager = new ServiceManager();
        $serviceManager->setService('LmcRbacMvc\Options\ModuleOptions', $options);
        $serviceManager->setService(
            'LmcRbacMvc\Identity\AuthenticationProvider',
            $this->getMock('LmcRbacMvc\Identity\IdentityProviderInterface')
        );

        $factory     = new RoleServiceFactory();
        $roleService = $factory->createService($serviceManager);
    }
}
