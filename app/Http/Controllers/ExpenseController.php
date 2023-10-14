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
                $bankAccountID = $request->input("bankAccountID");
            }
            // first load of site, user has not manually selected and there is no session variable for bank account id yet, so use default first bank account id of the user
            else if (!$request->filled("bankAccountID") && !session()->has("currentSelectedBankAccountID")) {

                // first() can return null, so you have to catch that case
                $bankAccountID = BankAccount::where("userAccountID", session("loggedInUserID"))->first();

                $bankAccountID = $bankAccountID->id ?? null;
            }
            // expense was added, edited or deleted, there already is a session variable for bank account id, so use session variable
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

            $categories = Category::where("userAccountID", session("loggedInUserID"))
                ->orderBy("title")
                ->get();

            // retrieve bank accounts from database or set to an empty array when user has no bank accounts
            $bankAccounts = $bankAccountID != null ? BankAccount::where("userAccountID", session("loggedInUserID"))
                ->get() : array();

            // store in session variable if control elements have to be disabled
            $bankAccountID == null ? session()->flash("disableControls", true)
                : session()->flash("disableControls", false);

            if ($bankAccountID == null) {
                session()->now("status", "Du hast noch kein Konto zu dem du Ausgaben hinzufügen kannst. Wechsle zur Seite \"Konten\" in der Menüleiste, um ein Konto zu erstellen.");
                session()->now("showAlert", "true");
                session()->now("successAlert", "false");
            }

            $balance = 0;
            if ($bankAccountID != null)
                $balance = BankAccount::where("id", $bankAccountID)->first()->balance;

            return view("expenses", [
                "expenses" => $expenses,
                "categories" => $categories,
                "bankAccounts" => $bankAccounts,
                "selectedBankAccountID" => $bankAccountID,
                "balance" => $balance
            ]);
        }
    }

    public function addExpense(Request $request)
    {
        if (!session()->has('loggedInUsername'))
            return redirect("/login");
        else {

            // description length exceeds maximum
            if (mb_strlen($request->input("description")) > config("formValidation.maxInputLengthLong")) {

                session()->flash("modalStatus", "Der Name ist zu lang.");

                return redirect("/expenses")->with([
                    "shouldOpenModal" => "edit",
                    "timestamp" => $request->input("timestamp"),
                    "amount" => $request->input("amount"),
                    "description" => $request->input("description") == "" ? "" : $request->input("description"),
                    "categoryID" => $request->input("category")
                ]);
            }
            // no errors
            else {

                Expense::create([
                    "timestamp" => $request->input("timestamp"),
                    "amount" => $request->input("amount"),
                    "description" => $request->input("description") == "" ? "" : $request->input("description"),
                    "categoryID" => $request->input("category"),
                    "bankAccountID" => session()->get("currentSelectedBankAccountID")
                ]);

                $this->updateBankAccountBalance(session()->get("currentSelectedBankAccountID"));

                session()->flash("status", "Ausgabe erfolgreich erstellt.");
                session()->flash("showAlert", "true");
                session()->flash("successAlert", "true");
                return redirect("/expenses");
            }
        }
    }


    public function editExpense($id)
    {
        $dbExpenseData = Expense::where("id", $id)->first();
        $dbCategoryOfExpense = Category::where("id", $dbExpenseData->categoryID)->first();

        // expense with $id does not exist
        if ($dbExpenseData == null)
            return redirect("/expenses");
        else {
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
        // description length exceeds maximum
        if (mb_strlen($request->input("description")) > config("formValidation.maxInputLengthLong")) {

            session()->flash("modalStatus", "Der Name ist zu lang.");

            return redirect("/expenses")->with([
                "shouldOpenModal" => "edit",
                "timestamp" => $request->input("timestamp"),
                "amount" => $request->input("amount"),
                "description" => $request->input("description"),
                "categoryID" => $request->input("category")
            ]);
        }
        // no errors
        else {
            // update expense
            Expense::where("id", session("currentExpenseEditingID"))->first()->update([
                "timestamp" => $request->input("timestamp"),
                "amount" => $request->input("amount"),
                "description" => $request->input("description") == "" ? "" : $request->input("description"),
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
        $dbExpenseData = Expense::where("id", $id)->first();

        // expense with $id does not exist
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
