<?php

use Illuminate\Support\Facades\Route;

// User Management Routes
// These routes will be implemented in the future

Route::middleware(['auth', 'admin'])->prefix('users')->name('users.')->group(function () {
    // Placeholder for future user management routes
});

