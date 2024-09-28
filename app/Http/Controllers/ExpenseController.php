<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BankAccount;
use App\Models\Expense;
use App\Models\Category;

class ExpenseController extends Controller
{
    public function showExpenses(Request $request)
    {
        if (!session()->has('loggedInUsername'))
            return redirect("/login");
        else {
            $bankAccountID = null;

            // user has manually selected a bank account, so the selected one should be loaded
            if ($request->filled("bankAccountID")) {
                // check if bankAccountID belongs to currently logged in user
                // BankAccount::where("id", $request->input("bankAccountID"))
                // ->where("userAccountID", )
                $bankAccountID = $request->input("bankAccountID");
            }
            // first load of site, user has not manually selected and there is no session variable for bank account id yet,
            // so use first bank account id of the user as default
            else if (!$request->filled("bankAccountID") && !session()->has("currentSelectedBankAccountID")) {

                // first() can return null, so you have to catch that case
                $bankAccountID = BankAccount::where("userAccountID", session("loggedInUserID"))->first();

                $bankAccountID = $bankAccountID->id ?? null;
            }
            // expense was added, edited or deleted, so there already is a session variable for bank account id, so use session variable
            else {
                $bankAccountID = session()->get("currentSelectedBankAccountID");
            }

            session()->put("currentSelectedBankAccountID", $bankAccountID);

            // retrieve expenses from database or set to an empty array when user has no bank accounts
            $expenses = $bankAccountID != null ? Expense::leftJoin("category", "expense.categoryID", "category.ID")
                ->select("expense.*", "category.title as categoryTitle")
                ->where("expense.bankAccountID", $bankAccountID)
                ->orderByDesc("timestamp")
                ->orderByDesc('ID')
                ->get() : array();

            // retrieve categories of user
            $categories = Category::where("userAccountID", session("loggedInUserID"))
                ->select("id", "title")
                ->orderBy("title")
                ->get();

            // retrieve bank accounts from database or set to an empty array when user has no bank accounts
            $bankAccounts = $bankAccountID != null ? BankAccount::where("userAccountID", session("loggedInUserID"))
                ->get() : array();

            // store in session variable if control elements have to be disabled
            $bankAccountID == null ? session()->flash("disableControls", true)
                : session()->flash("disableControls", false);

            // bank account id is null, so the user has no bank accounts
            if ($bankAccountID == null) {
                session()->now("status", "Du hast noch kein Konto zu dem du Ausgaben hinzufügen kannst. Wechsle zur Seite \"Konten\" in der Menüleiste, um ein Konto zu erstellen.");
                session()->now("showAlert", "true");
                session()->now("successAlert", "false");
            }

            // set balance to a default of 0, if the user has a bank account then load the balance of the currently selected bank account
            $balance = 0;
            if ($bankAccountID != null)
                $balance = BankAccount::where("id", $bankAccountID)->first()->balance;


            // only calculate sum if user has at least one bank account
            $positiveAmountCurrentMonth = 0;
            $negativeAmountCurrentMonth = 0;
            if ($bankAccountID != null) {
                // retrieve sum of positive and negative transactions in this month from database
                $positiveAmountCurrentMonth = Expense::join("bank_account", "expense.bankAccountID", "bank_account.id")
                    ->where("bank_account.userAccountID", session("loggedInUserID"))
                    ->where("bank_account.id", $bankAccountID)
                    ->whereRaw('MONTH(expense.timestamp) = MONTH(NOW())')
                    ->where('expense.amount', '>', 0)
                    ->sum("amount");

                // multiplying with -1 in order to get rid of the minus symbol, there is already a minus icon in the expense view
                $negativeAmountCurrentMonth = Expense::join("bank_account", "expense.bankAccountID", "bank_account.id")
                    ->where("bank_account.userAccountID", session("loggedInUserID"))
                    ->where("bank_account.id", $bankAccountID)
                    ->whereRaw('MONTH(expense.timestamp) = MONTH(NOW())')
                    ->where('expense.amount', '<=', 0)
                    ->sum("amount") * -1;
            }


            return view("expenses", [
                "expenses" => $expenses,
                "categories" => $categories,
                "bankAccounts" => $bankAccounts,
                "selectedBankAccountID" => $bankAccountID,
                "balance" => $balance,
                "positiveAmountCurrentMonth" => $positiveAmountCurrentMonth,
                "negativeAmountCurrentMonth" => $negativeAmountCurrentMonth
            ]);
        }
    }

    public function addExpense(Request $request)
    {
        if (!session()->has('loggedInUsername'))
            return redirect("/login");
        else {
            $error = false;
            $modalStatus = "";

            // todo: check if request->amount is really a number
            // todo: check if request->timestamp is really a date

            // description length exceeds maximum
            if (mb_strlen($request->input("description")) > config("formValidation.maxInputLengthLong")) {
                $error = true;

                $modalStatus = "Die Beschreibung ist zu lang.";
            }

            // error has occured
            if ($error) {
                return redirect("/expenses")->with([
                    "shouldOpenModal" => "edit",
                    "timestamp" => $request->input("timestamp"),
                    "amount" => $request->input("amount"),
                    "description" => $request->input("description") ?? "",
                    "categoryID" => $request->input("category"),
                    "modalStatus" => $modalStatus
                ]);
            }
            // no errors, create expense
            else {
                Expense::create([
                    "timestamp" => $request->input("timestamp"),
                    "amount" => $request->input("amount"),
                    "description" => $request->input("description") == "" ? "" : $request->input("description"),
                    "categoryID" => $request->input("category"),
                    "bankAccountID" => session("currentSelectedBankAccountID")
                ]);

                $this->updateBankAccountBalance(session("currentSelectedBankAccountID"));

                session()->flash("status", "Ausgabe erfolgreich erstellt.");
                session()->flash("showAlert", "true");
                session()->flash("successAlert", "true");
                return redirect("/expenses");
            }
        }
    }


    public function editExpense($id)
    {
        $dbExpenseData = Expense::join("bank_account", "expense.bankAccountID", "bank_account.id")
            ->join("user_account", "user_account.id", "bank_account.userAccountID")
            ->where("expense.id", $id)
            ->where("user_account.id", session("loggedInUserID"))
            ->select(
                "expense.id",
                "expense.timestamp",
                "expense.amount",
                "expense.description",
                "expense.categoryID",
                "expense.bankAccountID",
                "bank_account.id",
                "bank_account.userAccountID",
                "user_account.id"
            )
            ->first();

        // expense with $id does not exist or $id does not belong to current user
        if ($dbExpenseData == null)
            return redirect("/expenses");
        else {
            $dbCategoryOfExpense = Category::where("id", $dbExpenseData->categoryID)->first();

            session()->put("currentExpenseEditingID", $id);

            return redirect("/expenses")->with([
                "shouldOpenModal" => "edit",
                "timestamp" => $dbExpenseData->timestamp,
                "amount" => $dbExpenseData->amount,
                "description" => $dbExpenseData->description,
                "categoryID" => $dbCategoryOfExpense != null ? $dbCategoryOfExpense->id : null
            ]);
        }
    }

    public function verifyExpenseEditing(Request $request)
    {
        $error = false;
        $modalStatus = "";

        // todo: check if request->amount is really a number
        // todo: check if request->timestamp is really a date

        // description length exceeds maximum
        if (mb_strlen($request->input("description")) > config("formValidation.maxInputLengthLong")) {
            $error = false;

            $modalStatus = "Der Name ist zu lang.";
        }

        if ($error) {
            return redirect("/expenses")->with([
                "shouldOpenModal" => "edit",
                "timestamp" => $request->input("timestamp"),
                "amount" => $request->input("amount"),
                "description" => $request->input("description"),
                "categoryID" => $request->input("category"),
                "modalStatus" => $modalStatus
            ]);
        }
        // no errors, update expense
        else {
            Expense::where("id", session("currentExpenseEditingID"))->first()->update([
                "timestamp" => $request->input("timestamp"),
                "amount" => $request->input("amount"),
                "description" => $request->input("description") ?? "",
                "categoryID" => $request->input("category")
            ]);

            $this->updateBankAccountBalance(session()->get("currentSelectedBankAccountID"));

            session()->forget("currentExpenseEditingID");

            session()->flash("status", "Ausgabe erfolgreich bearbeitet.");
            session()->flash("showAlert", "true");
            session()->flash("successAlert", "true");
            return redirect("/expenses");
        }
    }

    public function deleteExpense($id)
    {
        $dbExpenseData = Expense::join("bank_account", "expense.bankAccountID", "bank_account.id")
            ->join("user_account", "user_account.id", "bank_account.userAccountID")
            ->where("expense.id", $id)
            ->where("user_account.id", session("loggedInUserID"))
            ->first();

        // expense with $id does not exist or $id does not belong to current user
        if ($dbExpenseData == null)
            return redirect("/expenses");
        else {
            session()->put("currentExpenseDeletingID", $id);

            return redirect("/expenses")->with([
                "shouldOpenModal" => "confirmDelete",
            ]);
        }
    }

    public function confirmExpenseDeletion()
    {
        Expense::where("id", session("currentExpenseDeletingID"))->first()->delete();

        $this->updateBankAccountBalance(session()->get("currentSelectedBankAccountID"));

        session()->forget("currentExpenseDeletingID");

        session()->flash("status", "Ausgabe erfolgreich gelöscht.");
        session()->flash("showAlert", "true");
        session()->flash("successAlert", "false");

        return redirect("/expenses");
    }

    public function updateBankAccountBalance($id)
    {
        $balance = Expense::where("bankAccountID", $id)->sum("amount");

        BankAccount::where("id", $id)->first()->update([
            "balance" => $balance
        ]);
    }
}
