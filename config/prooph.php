<?php
/*
 * This file is part of prooph/proophessor.
 * (c) 2014-2015 prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 9/6/15 - 5:07 PM
 */
return [
    'prooph' => [
        'event_store' => [
            'plugins' => [
                \Prooph\EventStoreBusBridge\EventPublisher::class,
                \Prooph\Snapshotter\SnapshotPlugin::class,
                //
            ],
        ],
        'snapshot_store' => [
            'adapter' => [
                'type' => \Prooph\EventStore\Snapshot\Adapter\Doctrine\DoctrineSnapshotAdapter::class,
                'options' => [
                    'connection_alias' => 'doctrine.connection.default',
                    'snapshot_table_map' => [
                        \Prooph\ProophessorDo\Model\Todo\Todo::class => 'snapshot',
                    ]
                ]
            ]
        ],
        'snapshotter' => [
            'version_step' => 5,
            'aggregate_repositories' => [
                \Prooph\ProophessorDo\Model\Todo\Todo::class => \Prooph\ProophessorDo\Model\Todo\TodoList::class,
            ]
        ],
        'service_bus' => [
            'command_bus' => [
                'plugins' => [
                    \Prooph\EventStoreBusBridge\TransactionManager::class,
                ],
                'router' => [
                    'routes' => [
                        \Prooph\ProophessorDo\Model\User\Command\RegisterUser::class => \Prooph\ProophessorDo\Model\User\Handler\RegisterUserHandler::class,
                        \Prooph\ProophessorDo\Model\Todo\Command\PostTodo::class     => \Prooph\ProophessorDo\Model\Todo\Handler\PostTodoHandler::class,
                        \Prooph\ProophessorDo\Model\Todo\Command\MarkTodoAsDone::class     => \Prooph\ProophessorDo\Model\Todo\Handler\MarkTodoAsDoneHandler::class,
                        \Prooph\ProophessorDo\Model\Todo\Command\AddDeadlineToTodo::class => \Prooph\ProophessorDo\Model\Todo\Handler\AddDeadlineToTodoHandler::class,
                        \Prooph\Snapshotter\TakeSnapshot::class => \Prooph\Snapshotter\Snapshotter::class,
                    ]
                ]
            ],
            'event_bus' => [
                'plugins' => [
                    \Prooph\ServiceBus\Plugin\InvokeStrategy\OnEventStrategy::class
                ],
                'router' => [
                    'routes' => [
                        \Prooph\ProophessorDo\Model\User\Event\UserWasRegistered::class => [
                            \Prooph\ProophessorDo\Projection\User\UserProjector::class,
                        ],
                        \Prooph\ProophessorDo\Model\Todo\Event\TodoWasPosted::class => [
                            \Prooph\ProophessorDo\Projection\Todo\TodoProjector::class,
                            \Prooph\ProophessorDo\Projection\User\UserProjector::class,
                        ],
                        \Prooph\ProophessorDo\Model\Todo\Event\TodoWasMarkedAsDone::class => [
                            \Prooph\ProophessorDo\Projection\Todo\TodoProjector::class,
                            \Prooph\ProophessorDo\Projection\User\UserProjector::class,
                        ],
                        \Prooph\ProophessorDo\Model\Todo\Event\DeadlineWasAddedToTodo::class => [
                            \Prooph\ProophessorDo\Projection\Todo\TodoProjector::class,
                        ],
                    ]
                ]
            ]
        ]
    ],
];
