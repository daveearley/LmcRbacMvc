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

return [
    'service_manager' => [
        'invokables' => [
            'LmcRbacMvc\Collector\RbacCollector' => 'LmcRbacMvc\Collector\RbacCollector',
        ],

        'factories' => [
            /* Factories that do not map to a class */
            'LmcRbacMvc\Guards' => 'LmcRbacMvc\Factory\GuardsFactory',

            /* Factories that map to a class */
            'Rbac\Rbac'                                       => 'LmcRbacMvc\Factory\RbacFactory',
            'LmcRbacMvc\Assertion\AssertionPluginManager'        => 'LmcRbacMvc\Factory\AssertionPluginManagerFactory',
            'LmcRbacMvc\Guard\GuardPluginManager'                => 'LmcRbacMvc\Factory\GuardPluginManagerFactory',
            'LmcRbacMvc\Identity\AuthenticationIdentityProvider' => 'LmcRbacMvc\Factory\AuthenticationIdentityProviderFactory',
            'LmcRbacMvc\Options\ModuleOptions'                   => 'LmcRbacMvc\Factory\ModuleOptionsFactory',
            'LmcRbacMvc\Role\RoleProviderPluginManager'          => 'LmcRbacMvc\Factory\RoleProviderPluginManagerFactory',
            'LmcRbacMvc\Service\AuthorizationService'            => 'LmcRbacMvc\Factory\AuthorizationServiceFactory',
            'LmcRbacMvc\Service\RoleService'                     => 'LmcRbacMvc\Factory\RoleServiceFactory',
            'LmcRbacMvc\View\Strategy\RedirectStrategy'          => 'LmcRbacMvc\Factory\RedirectStrategyFactory',
            'LmcRbacMvc\View\Strategy\UnauthorizedStrategy'      => 'LmcRbacMvc\Factory\UnauthorizedStrategyFactory',
        ],
    ],

    'view_helpers' => [
        'factories' => [
            'LmcRbacMvc\View\Helper\IsGranted' => 'LmcRbacMvc\Factory\IsGrantedViewHelperFactory',
            'LmcRbacMvc\View\Helper\HasRole'   => 'LmcRbacMvc\Factory\HasRoleViewHelperFactory'
        ],
        'aliases' => [
            'isGranted' => 'LmcRbacMvc\View\Helper\IsGranted',
            'hasRole'   => 'LmcRbacMvc\View\Helper\HasRole'
        ]
    ],

    'controller_plugins' => [
        'factories' => [
            'LmcRbacMvc\Mvc\Controller\Plugin\IsGranted' => 'LmcRbacMvc\Factory\IsGrantedPluginFactory'
        ],
        'aliases' => [
            'isGranted' => 'LmcRbacMvc\Mvc\Controller\Plugin\IsGranted'
        ]
    ],

    'view_manager' => [
        'template_map' => [
            'error/403'                             => __DIR__ . '/../view/error/403.phtml',
            'laminas-developer-tools/toolbar/zfc-rbac' => __DIR__ . '/../view/laminas-developer-tools/toolbar/zfc-rbac.phtml'
        ]
    ],

    'laminas-developer-tools' => [
        'profiler' => [
            'collectors' => [
                'zfc_rbac' => 'LmcRbacMvc\Collector\RbacCollector',
            ],
        ],
        'toolbar' => [
            'entries' => [
                'zfc_rbac' => 'laminas-developer-tools/toolbar/zfc-rbac',
            ],
        ],
    ],

    'zfc_rbac' => [
        // Guard plugin manager
        'guard_manager' => [],

        // Role provider plugin manager
        'role_provider_manager' => [],

        // Assertion plugin manager
        'assertion_manager' => []
    ]
];
