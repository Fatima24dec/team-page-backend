<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeamMembersController;

Route::get('/teams', [TeamMembersController::class, 'teams']);