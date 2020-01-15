<?php
namespace EWW\Dpf\Domain\Workflow;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use Symfony\Component\Workflow\DefinitionBuilder;
use Symfony\Component\Workflow\MarkingStore\MethodMarkingStore;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\Workflow;
use Symfony\Component\Workflow\Registry;

class DocumentWorkflow
{
    public const LOCAL_STATE_NONE          = 'NONE';
    public const LOCAL_STATE_NEW           = 'NEW';
    public const LOCAL_STATE_REGISTERED    = 'REGISTERED';
    public const LOCAL_STATE_IN_PROGRESS   = 'IN_PROGRESS';
    public const LOCAL_STATE_DISCARDED     = 'DISCARDED';
    public const LOCAL_STATE_POSTPONED     = 'POSTPONED';
    public const LOCAL_STATE_DELETED       = 'DELETED';

    public const REMOTE_STATE_NONE         = 'NONE';
    public const REMOTE_STATE_ACTIVE       = "ACTIVE";
    public const REMOTE_STATE_INACTIVE     = "INACTIVE";
    public const REMOTE_STATE_DELETED      = "DELETED";

    public const STATE_NONE_NONE            = self::LOCAL_STATE_NONE.':'.self::REMOTE_STATE_NONE;

    // New
    public const STATE_NEW_NONE             = self::LOCAL_STATE_NEW.':'.self::REMOTE_STATE_NONE;

    // Registered
    public const STATE_REGISTERED_NONE      = self::LOCAL_STATE_REGISTERED.':'.self::REMOTE_STATE_NONE;

    // In progress
    public const STATE_IN_PROGRESS_NONE     = self::LOCAL_STATE_IN_PROGRESS.':'.self::REMOTE_STATE_NONE;
    public const STATE_IN_PROGRESS_ACTIVE   = self::LOCAL_STATE_IN_PROGRESS.":".self::REMOTE_STATE_ACTIVE;
    public const STATE_IN_PROGRESS_INACTIVE = self::LOCAL_STATE_IN_PROGRESS.":".self::REMOTE_STATE_INACTIVE;
    public const STATE_IN_PROGRESS_DELETED  = self::LOCAL_STATE_IN_PROGRESS.":".self::REMOTE_STATE_DELETED;

    // Active
    public const STATE_NONE_ACTIVE          = self::LOCAL_STATE_NONE.':'.self::REMOTE_STATE_ACTIVE;

    // Postponed
    public const STATE_POSTPONED_NONE       = self::LOCAL_STATE_POSTPONED.':'.self::REMOTE_STATE_NONE;
    public const STATE_NONE_INACTIVE        = self::LOCAL_STATE_NONE.':'.self::REMOTE_STATE_INACTIVE;

    // Discarded
    public const STATE_DISCARDED_NONE       = self::LOCAL_STATE_DISCARDED.':'.self::REMOTE_STATE_NONE;
    public const STATE_NONE_DELETED         = self::LOCAL_STATE_NONE.':'.self::REMOTE_STATE_DELETED;

    public const TRANSITION_CREATE              = "CREATE_TRANSITION";
    public const TRANSITION_CREATE_REGISTER     = "CREATE_REGISTER_TRANSITION";
    public const TRANSITION_REGISTER            = "REGISTER_TRANSITION";
    public const TRANSITION_DISCARD             = "DISCARD_TRANSITION";
    public const TRANSITION_POSTPONE            = "POSTPONE_TRANSITION";
    public const TRANSITION_RELEASE_PUBLISH     = "RELEASE_PUBLISH_TRANSITION";
    public const TRANSITION_RELEASE_UPDATE      = "RELEASE_UPDATE_TRANSITION";
    public const TRANSITION_RELEASE_ACTIVATE    = "RELEASE_ACTIVATE_TRANSITION";
    public const TRANSITION_DELETE_LOCALLY      = "DELETE_LOCALLY_TRANSITION";
    public const TRANSITION_DELETE_WORKING_COPY = "DELETE_WORKING_COPY_TRANSITION";
    public const TRANSITION_DELETE_DISCARDED    = "DELETE_DISCARDED_TRANSITION";

    public const PLACES = [
        self::STATE_NONE_NONE,
        self::STATE_NEW_NONE,
        self::STATE_REGISTERED_NONE,
        self::STATE_IN_PROGRESS_NONE,
        self::STATE_DISCARDED_NONE,
        self::STATE_NONE_DELETED,
        self::STATE_POSTPONED_NONE,
        self::STATE_NONE_INACTIVE,
        self::STATE_NONE_ACTIVE,
        self::STATE_IN_PROGRESS_ACTIVE,
        self::STATE_IN_PROGRESS_INACTIVE,
        self::STATE_IN_PROGRESS_DELETED
    ];

    public const TRANSITIONS = [
        self::TRANSITION_CREATE => [
            "from" => [self::STATE_NONE_NONE],
            "to" => self::STATE_NEW_NONE
        ],
        self::TRANSITION_CREATE_REGISTER => [
            "from" => [self::STATE_NONE_NONE],
            "to" => self::STATE_REGISTERED_NONE
        ],
        self::TRANSITION_REGISTER => [
            "from" => [self::STATE_NEW_NONE],
            "to" => self::STATE_REGISTERED_NONE
        ],
        self::TRANSITION_DISCARD => [
            "from" => [
                self::STATE_REGISTERED_NONE,
                self::STATE_IN_PROGRESS_NONE,
                self::STATE_POSTPONED_NONE,
                self::STATE_IN_PROGRESS_ACTIVE,
                self::STATE_IN_PROGRESS_INACTIVE,
                self::STATE_IN_PROGRESS_DELETED,
                self::STATE_NONE_ACTIVE,
                self::STATE_NONE_INACTIVE,
                self::STATE_NONE_DELETED
            ],
            "to" => [
                self::STATE_DISCARDED_NONE,
                self::STATE_DISCARDED_NONE,
                self::STATE_DISCARDED_NONE,
                self::STATE_NONE_DELETED,
                self::STATE_NONE_DELETED,
                self::STATE_NONE_DELETED,
                self::STATE_NONE_DELETED,
                self::STATE_NONE_DELETED,
                self::STATE_NONE_DELETED
            ]
        ],
        self::TRANSITION_POSTPONE => [
            "from" => [
                self::STATE_REGISTERED_NONE,
                self::STATE_IN_PROGRESS_NONE,
                self::STATE_DISCARDED_NONE,
                self::STATE_IN_PROGRESS_ACTIVE,
                self::STATE_IN_PROGRESS_DELETED,
                self::STATE_IN_PROGRESS_INACTIVE,
                self::STATE_NONE_ACTIVE,
                self::STATE_NONE_DELETED,
                self::STATE_NONE_INACTIVE
            ],
            "to" => [
                self::STATE_POSTPONED_NONE,
                self::STATE_POSTPONED_NONE,
                self::STATE_POSTPONED_NONE,
                self::STATE_NONE_INACTIVE,
                self::STATE_NONE_INACTIVE,
                self::STATE_NONE_INACTIVE,
                self::STATE_NONE_INACTIVE,
                self::STATE_NONE_INACTIVE,
                self::STATE_NONE_INACTIVE
            ]
        ],
        self::TRANSITION_RELEASE_PUBLISH => [
            "from" => [
                self::STATE_REGISTERED_NONE,
                self::STATE_IN_PROGRESS_NONE,
                self::STATE_DISCARDED_NONE,
                self::STATE_POSTPONED_NONE,
            ],
            "to" => self::STATE_NONE_ACTIVE
        ],
        self::TRANSITION_RELEASE_ACTIVATE => [
            "from" => [
                self::STATE_IN_PROGRESS_ACTIVE,
                self::STATE_IN_PROGRESS_INACTIVE,
                self::STATE_IN_PROGRESS_DELETED,
                self::STATE_NONE_ACTIVE,
                self::STATE_NONE_INACTIVE,
                self::STATE_NONE_DELETED
            ],
            "to" => self::STATE_NONE_ACTIVE
        ],
        self::TRANSITION_DELETE_LOCALLY => [
            "from" => [self::STATE_NEW_NONE],
            "to" => self::STATE_NONE_NONE
        ],
        self::TRANSITION_DELETE_DISCARDED => [
            "from" => [self::STATE_DISCARDED_NONE],
            "to" => self::STATE_NONE_NONE
        ],
        self::TRANSITION_DELETE_WORKING_COPY => [
            "from" => [self::STATE_IN_PROGRESS_ACTIVE, self::STATE_IN_PROGRESS_INACTIVE, self::STATE_IN_PROGRESS_DELETED],
            "to" => self::STATE_NONE_NONE
        ],
    ];
    
    public static function getWorkflow()
    {
        $definitionBuilder = new DefinitionBuilder();

        $definitionBuilder->addPlaces(self::PLACES);

        foreach (self::TRANSITIONS as $transitionName => $transition) {
            if (!empty($transition["from"]) && !empty($transition["to"])) {
                foreach ($transition["from"] as $key => $fromState) {
                    if (is_array($transition["to"])) {
                        $definitionBuilder->addTransition(
                            new Transition($transitionName, $fromState, $transition["to"][$key])
                        );
                    } else {
                        $definitionBuilder->addTransition(
                             new Transition($transitionName, $fromState, $transition["to"])
                        );
                    }
                }
            }
        }

        $definition = $definitionBuilder->build();

        $marking = new MethodMarkingStore(TRUE, 'state');

        return new Workflow($definition, $marking);
    }

}