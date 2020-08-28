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

use LmcRbacMvc\Factory\AuthorizationServiceDelegatorFactory;
use LmcRbacMvcTest\Initializer\AuthorizationAwareFake;
use LmcRbacMvcTest\Util\ServiceManagerFactory;

/**
 * @covers  \LmcRbacMvc\Factory\AuthorizationServiceDelegatorFactory
 * @author  Jean-Marie Leroux <jmleroux.pro@gmail.com>
 * @license MIT License
 */
class AuthorizationServiceDelegatorTest extends \PHPUnit_Framework_TestCase
{
    public function testDelegatorFactory()
    {
        $authServiceClassName = 'LmcRbacMvc\Service\AuthorizationService';
        $delegator            = new AuthorizationServiceDelegatorFactory();
        $serviceLocator       = $this->getMock('Laminas\ServiceManager\ServiceLocatorInterface');
        $authorizationService = $this->getMock('LmcRbacMvc\Service\AuthorizationService', [], [], '', false);

        $callback = function () {
            return new AuthorizationAwareFake();
        };

        $serviceLocator->expects($this->once())
            ->method('get')
            ->with($authServiceClassName)
            ->will($this->returnValue($authorizationService));

        $decoratedInstance = $delegator->createDelegatorWithName($serviceLocator, 'name', 'requestedName', $callback);

        $this->assertEquals($authorizationService, $decoratedInstance->getAuthorizationService());
    }

    public function testAuthorizationServiceIsNotInjectedWithoutDelegator()
    {
        $serviceManager = ServiceManagerFactory::getServiceManager();

        $serviceManager->setAllowOverride(true);
        $authorizationService = $this->getMock('LmcRbacMvc\Service\AuthorizationService', [], [], '', false);
        $serviceManager->setService(
            'LmcRbacMvc\Service\AuthorizationService',
            $authorizationService
        );
        $serviceManager->setAllowOverride(false);

        $serviceManager->setInvokableClass(
            'LmcRbacMvcTest\AuthorizationAware',
            'LmcRbacMvcTest\Initializer\AuthorizationAwareFake'
        );
        $decoratedInstance = $serviceManager->get('LmcRbacMvcTest\AuthorizationAware');
        $this->assertNull($decoratedInstance->getAuthorizationService());
    }

    public function testAuthorizationServiceIsInjectedWithDelegator()
    {
        $serviceManager = ServiceManagerFactory::getServiceManager();

        $serviceManager->setAllowOverride(true);
        $authorizationService = $this->getMock('LmcRbacMvc\Service\AuthorizationService', [], [], '', false);
        $serviceManager->setService(
            'LmcRbacMvc\Service\AuthorizationService',
            $authorizationService
        );
        $serviceManager->setAllowOverride(false);

        $serviceManager->setInvokableClass(
            'LmcRbacMvcTest\AuthorizationAware',
            'LmcRbacMvcTest\Initializer\AuthorizationAwareFake'
        );

        $serviceManager->addDelegator(
            'LmcRbacMvcTest\AuthorizationAware',
            'LmcRbacMvc\Factory\AuthorizationServiceDelegatorFactory'
        );

        $decoratedInstance = $serviceManager->get('LmcRbacMvcTest\AuthorizationAware');
        $this->assertEquals($authorizationService, $decoratedInstance->getAuthorizationService());
    }

    public function testDelegatorThrowExceptionWhenBadInterface()
    {
        $serviceManager = ServiceManagerFactory::getServiceManager();

        $serviceManager->setAllowOverride(true);
        $authorizationService = $this->getMock('LmcRbacMvc\Service\AuthorizationService', [], [], '', false);
        $serviceManager->setService(
            'LmcRbacMvc\Service\AuthorizationService',
            $authorizationService
        );
        $serviceManager->setAllowOverride(false);

        $serviceManager->setFactory(
            'LmcRbacMvcTest\AuthorizationAware',
            function () {
                return new \StdClass();
            }
        );

        $serviceManager->addDelegator(
            'LmcRbacMvcTest\AuthorizationAware',
            'LmcRbacMvc\Factory\AuthorizationServiceDelegatorFactory'
        );

        $this->setExpectedException(
            'LmcRbacMvc\Exception\RuntimeException',
            'The service LmcRbacMvcTest\AuthorizationAware must implement AuthorizationServiceAwareInterface.'
        );
        $serviceManager->get('LmcRbacMvcTest\AuthorizationAware');
    }
}
