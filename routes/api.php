<?php

use Illuminate\Routing\Route;

Route::apiResource('whoami', 'WhoAmIController')->only(['index']);
