<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Task\ManageController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::resource("/login", LoginController::class);
    Route::post("/login/process", [LoginController::class, "login"])->name("login.process");
});

Route::middleware(["isoLogin"])->group(function () {
    Route::resource("/", ManageController::class);
    Route::post("/generate/task", [ManageController::class, "loadTask"])->name("load.task");
    Route::post("/logout", [LoginController::class, "logout"])->name("logout.account");
    Route::post("/upload/file", [ManageController::class, "uploadFile"])->name("upload.file");
});