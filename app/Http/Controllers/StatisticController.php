<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Category;
use App\Models\Expense;
use Illuminate\Support\Facades\DB;

class StatisticController extends Controller
{
    public function __invoke()
    {
        if (!session()->has('loggedInUsername'))
            return redirect("/login");
        else {
            $allCategories = Category::where("userAccountID", session("loggedInUserID"))
                ->orderBy("title")
                ->select("title")
                ->get();

            $expensesAmountPerCategoryCurrentMonth = Expense::join("bank_account", "expense.bankAccountID", "bank_account.id")
                ->leftJoin("category", "expense.categoryID", "category.id")
                ->where("bank_account.userAccountID", session("loggedInUserID"))
                ->whereMonth("timestamp", now()->month)
                ->whereYear("timestamp", now()->year)
                ->groupBy("category.title")
                ->selectRaw("category.title as categoryTitle, SUM(amount) as totalAmount")
                ->get();


            $expensesAmountPerCategoryPerMonthLast12Months = Expense::join("bank_account", "expense.bankAccountID", "bank_account.id")
                ->leftJoin("category", "expense.categoryID", "category.id")
                ->where("bank_account.userAccountID", session("loggedInUserID"))
                ->whereRaw("expense.timestamp >= DATE_SUB(NOW(), INTERVAL 11 MONTH)")
                ->selectRaw("category.title as categoryTitle, SUM(amount) as totalAmount, MONTHNAME(expense.timestamp) as month")
                ->groupBy("category.title")
                ->groupBy(DB::raw('MONTHNAME(expense.timestamp)'))
                ->orderBy("expense.timestamp")
                ->get();

            $expensesMonthlyBalanceLast12Months = Expense::join("bank_account", "expense.bankAccountID", "bank_account.id")
                ->where("bank_account.userAccountID", session("loggedInUserID"))
                ->whereRaw("expense.timestamp >= DATE_SUB(NOW(), INTERVAL 11 MONTH)")
                ->selectRaw("MONTHNAME(expense.timestamp) as month, SUM(CASE WHEN expense.amount >= 0 THEN expense.amount ELSE 0 END) AS allPositiveAmounts,
                    SUM(CASE WHEN expense.amount < 0 THEN expense.amount ELSE 0 END) AS allNegativeAmounts,
                    SUM(expense.amount) AS balance")
                ->groupBy(DB::raw('MONTHNAME(expense.timestamp)'))
                ->orderBy("expense.timestamp")
                ->get();

            //  dd($expensesMonthlyBalanceLast12Months);

            /*$bankAccountsBalancePerMonthLast12Months = Expense::join("bank_account", "expense.bankAccountID", "bank_account.id")
                ->where("bank_account.userAccountID", session("loggedInUserID"))
                ->groupBy("bankAccountID")
                ->groupBy(DB::raw('MONTHNAME(expense.timestamp)'))
                ->selectRaw("SUM(amount), MONTHNAME(expense.timestamp) as month")
                ->orderBy("expense.timestamp")
                ->get();

            dd($bankAccountsBalancePerMonthLast12Months);
            */

            session()->put("allCategories", $allCategories);
            session()->put("expensesAmountPerCategoryCurrentMonth", $expensesAmountPerCategoryCurrentMonth);
            session()->put("expensesAmountPerCategoryPerMonthLast12Months", $expensesAmountPerCategoryPerMonthLast12Months);
            session()->put("expensesMonthlyBalanceLast12Months", $expensesMonthlyBalanceLast12Months);

            return view("statistics");
        }
    }
}
