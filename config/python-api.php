<?php

return [
    'url' => env('PYTHON_API_URL', 'http://localhost:8001'),

    'train_endpoint' => '/train',
    'predict_endpoint' => '/predict',

    'timeout' => env('PYTHON_API_TIMEOUT', 300),
];