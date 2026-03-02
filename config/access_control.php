<?php

return [
    'pilot_override' => [
        'enabled' => env('ACCESS_CONTROL_PILOT_OVERRIDE_ENABLED', true),
    ],
    'rollout_override' => [
        'enabled' => env('ACCESS_CONTROL_ROLLOUT_OVERRIDE_ENABLED', env('ACCESS_CONTROL_PILOT_OVERRIDE_ENABLED', true)),
        'modules' => [
            'catatan-keluarga',
            'activities',
        ],
    ],
];
