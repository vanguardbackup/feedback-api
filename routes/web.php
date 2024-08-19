<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'message' => "You've reached the API for Vanguard's Experiments feedback.",
        'links' => [
            'vanguard_repository' => 'https://github.com/vanguardbackup/vanguard',
            'vanguard_website' => 'https://vanguardbackup.com',
            'api_documentation' => 'https://docs.vanguardbackup.com/api/introduction', // Replace with actual docs URL
            'experiments_info' => 'https://docs.vanguardbackup.com/experiments', // Replace with actual URL
        ],
        'need_help' => 'If you need assistance, please contact support@vanguardbackup.com',
    ], 200, [], JSON_PRETTY_PRINT);
});
