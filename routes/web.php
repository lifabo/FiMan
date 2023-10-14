<?php

use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\StatisticController;
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

// homepage
Route::get('/', StatisticController::class);

// login
Route::get('/login', [LoginController::class, "showLogin"]);
Route::post('/loginVerification', [LoginController::class, "verifyLogin"]);
Route::get("/logout", [LoginController::class, "logout"]);

// category
Route::get('/categories', [CategoryController::class, "showCategories"]);
Route::post("/addCategory", [CategoryController::class, "addCategory"]);
Route::get("/category/edit/{id}", [CategoryController::class, "editCategory"])->name("category.edit");
Route::post("/verifyCategoryEditing", [CategoryController::class, "verifyCategoryEditing"]);
Route::delete("/category/delete/{id}", [CategoryController::class, "deleteCategory"])->name("category.delete");
Route::delete("/confirmCategoryDeletion", [CategoryController::class, "confirmCategoryDeletion"]);

// expense
Route::match(['get', 'post'], '/expenses', [ExpenseController::class, 'showExpenses'])->name('expense.show');
Route::post("/addExpense", [ExpenseController::class, "addExpense"]);
Route::get("/expense/edit/{id}", [ExpenseController::class, "editExpense"])->name("expense.edit");
Route::post("/verifyExpenseEditing", [ExpenseController::class, "verifyExpenseEditing"]);
Route::delete("/expense/delete/{id}", [ExpenseController::class, "deleteExpense"])->name("expense.delete");
Route::delete("/confirmExpenseDeletion", [ExpenseController::class, "confirmExpenseDeletion"]);

// bank account
Route::get("/bankAccounts", [BankAccountController::class, "showBankAccounts"]);
Route::post("/addBankAccount", [BankAccountController::class, "addBankAccount"]);
Route::get("/bankAccount/edit/{id}", [BankAccountController::class, "editBankAccount"])->name("bankAccount.edit");
Route::post("/verifyBankAccountEditing", [BankAccountController::class, "verifyBankAccountEditing"]);
Route::delete("/bankAccount/delete/{id}", [BankAccountController::class, "deleteBankAccount"])->name("bankAccount.delete");
Route::delete("/confirmBankAccountDeletion", [BankAccountController::class, "confirmBankAccountDeletion"]);
