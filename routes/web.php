<?php

use App\Http\Controllers\WebController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// views
Route::get('/', [WebController::class, "showHomepage"]);
Route::get('/categories', [WebController::class, "showCategories"]);

// login/register
Route::get("/register", [WebController::class, "showRegister"]);
Route::post("/verifyAccountCreation", [WebController::class, "verifyAccountCreation"]);
Route::get('/login', [WebController::class, "showLogin"]);
Route::post('/loginVerification', [WebController::class, "verifyLogin"]);
Route::get("/logout", [WebController::class, "logout"]);

// category
Route::post("/addCategory", [WebController::class, "addCategory"]);
Route::get("/category/edit/{id}", [WebController::class, "editCategory"])->name("category.edit");
Route::post("/verifyCategoryEditing", [WebController::class, "verifyCategoryEditing"]);
Route::get("/category/delete/{id}", [WebController::class, "deleteCategory"])->name("category.delete");
