<?php

use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::post("login", [UserController::class, "login"]);
Route::post("register", [UserController::class, "register"]);


Route::group(["middleware" => ["auth:api"]], function () {
    Route::post("addTask", [UserController::class, "addTask"]);
    Route::get("showTasks", [UserController::class, "showTasks"]);
    Route::get("deleteTask/{id}", [UserController::class, "deleteTask"]);
    Route::get("completeTask/{id}", [UserController::class, "completeTask"]);
    Route::post("editTask", [UserController::class, "editTask"]);
    Route::post("filterTasks", [UserController::class, "filterTasks"]);
});
