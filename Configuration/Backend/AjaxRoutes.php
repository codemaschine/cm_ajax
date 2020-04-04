<?php

return [
    'cm_backend_router' => [
        'path' => '/cm_ajax/dispatch',
        'target' => \TYPO3\CmAjax\Controller\BeDispatcherController::class . '::dispatch'
    ]
];