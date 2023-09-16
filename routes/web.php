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

// login/register
Route::get('/login', [WebController::class, "showLogin"]);
Route::get("/register", [WebController::class, "showRegister"]);
Route::post("/verifyAccountCreation", [WebController::class, "verifyAccountCreation"]);
Route::post('/loginVerification', [WebController::class, "verifyLogin"]);
Route::get("/logout", [WebController::class, "logout"]);

// category
Route::get('/categories', [WebController::class, "showCategories"]);
Route::post("/addCategory", [WebController::class, "addCategory"]);
Route::get("/category/edit/{id}", [WebController::class, "editCategory"])->name("category.edit");
Route::post("/verifyCategoryEditing", [WebController::class, "verifyCategoryEditing"]);
Route::delete("/category/delete/{id}", [WebController::class, "deleteCategory"])->name("category.delete");

// expense
Route::get("/expenses", [WebController::class, "showExpenses"]);
Route::post("/addExpense", [WebController::class, "addExpense"]);
Route::get("/expense/edit/{id}", [WebController::class, "editExpense"])->name("expense.edit");
Route::post("/verifyExpenseEditing", [WebController::class, "verifyExpenseEditing"]);
Route::delete("/expense/delete/{id}", [WebController::class, "deleteExpense"])->name("expense.delete");
