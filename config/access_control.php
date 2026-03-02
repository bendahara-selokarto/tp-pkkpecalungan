<?php

return [
    'pilot_override' => [
        'enabled' => env('ACCESS_CONTROL_PILOT_OVERRIDE_ENABLED', true),
        'modules' => [
            'catatan-keluarga',
            'pilot-project-keluarga-sehat',
            'pilot-project-naskah-pelaporan',
        ],
    ],
];
