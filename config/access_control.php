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
            'agenda-surat',
        ],
    ],
    'sekretaris_show_pokja_menus' => env('ACCESS_CONTROL_SEKRETARIS_SHOW_POKJA', false),
];
