<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Category;
use App\Models\Expense;
use App\Models\UserAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class WebController extends Controller
{
    public function showStatistics()
    {
        if (!session()->has('loggedInUsername'))
            return redirect("/login");
        else {
            return view("statistics");
        }
    }

    #region login
    public function testAccountPW()
    {
        dd(Hash::make("admin"));
    }
    public function showRegister()
    {
        if (session()->has('loggedInUsername'))
            return redirect("/");
        else {
            return view("register");
        }
    }
    public function verifyAccountCreation(Request $request)
    {
        $formUserName = $request->input("username");
        $formPassword = $request->input("password");
        $formPasswordRepeat = $request->input("passwordRepeat");
        $userAlreadyExists = UserAccount::where("username", $formUserName)->first();
        $pwCheckPattern = '/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[^a-zA-Z0-9]).{5,}$/';

        // save form data for one more http request in case the verification fails
        session()->flash("username", $formUserName);
        session()->flash("password", $formPassword);
        session()->flash("passwordRepeat", $formPasswordRepeat);

        // username already taken
        if ($userAlreadyExists != null) {
            session()->flash("status", "Der Benutzername ist bereits vergeben.");
            return redirect("/register");
        }
        // passwords do not match
        else if ($formPassword != $formPasswordRepeat) {
            session()->flash("status", "Die Passwörter stimmen nicht überein.");
            return redirect("/register");
        }
        // password does not match requirements
        else if (!preg_match_all($pwCheckPattern, $formPassword)) {
            session()->flash("status", "Das Passwort erfüllt nicht alle Bedingungen.");
            return redirect("/register");
        }
        // successful, create user
        else {
            // delete form data from session, so that it is not displayed in the login form when the user is being redirected
            session()->forget("username");
            session()->forget("password");
            session()->forget("passwordRepeat");


            UserAccount::create([
                "username" => $request->input("username"),
                "passwd" => Hash::make($formPassword)
            ]);

            session()->flash("status", "Konto erfolgreich erstellt. Du kannst dich nun anmelden.");
            return redirect("/login");
        }
    }
    public function showLogin()
    {
        if (session()->has('loggedInUsername'))
            return redirect("/");
        else {
            return view("login");
        }
    }

    public function verifyLogin(Request $request)
    {
        //$this->testAccountPW("admin");
        $formUserName = $request->input("username");
        $formPassword = $request->input("password");
        $dbUserData = UserAccount::where('username', $formUserName)->first();

        // save form data for one more http request in case the verification fails
        session()->flash("username", $formUserName);
        session()->flash("password", $formPassword);

        // user does not exist or password is wrong
        if ($dbUserData == null || !Hash::check($formPassword, $dbUserData->passwd)) {
            session()->flash("status", "Benutzername oder Passwort falsch.");
            return redirect("/login");
        }
        // login successful
        else {
            session()->put("loggedInUsername", $dbUserData->username);
            session()->put("loggedInUserID", $dbUserData->id);

            return redirect("/");
        }
    }

    public function logout()
    {
        session()->invalidate();
        return redirect("/");
    }
    #endregion






    #region category

    public function test($id)
    {
        Category::where("id", $id)->first()->delete();
        return redirect("/categories");
    }

    public function showCategories()
    {
        if (!session()->has('loggedInUsername'))
            return redirect("/login");
        else {
            $categories = Category::where("userAccountID", session("loggedInUserID"))->get();

            return view("categories", [
                "categories" => $categories
            ]);
        }
    }

    public function checkIfCategoryExists($title)
    {
        if (Category::where("title", $title)->where("userAccountID", session("loggedInUserID"))->where("id", "!=", session("currentCategoryEditingID"))->first() != null)
            return true;
        else
            return false;
    }

    public function addCategory(Request $request)
    {
        if (!session()->has('loggedInUsername'))
            return redirect("/login");
        else {
            // title unqualified (null or whitespaces)
            if ($request->input("title") == "" || trim($request->input("title") == "")) {
                return redirect("/categories")->with([
                    "shouldOpenModal" => "add",
                    "modalStatus" => "Der Name darf nicht leer sein oder nur aus Leerzeichen bestehen."
                ]);
            }
            // category already exists
            else if ($this->checkIfCategoryExists($request->input("title"))) {
                session()->flash("modalStatus", "Eine Kategorie mit dem Namen '" . $request->input('title') . "' existiert bereits.");
                $shouldOpenModal = "add";
                return redirect("/categories")->with([
                    "shouldOpenModal" => $shouldOpenModal,
                    "title" => $request->input("title")
                ]);
            } else {
                Category::create([
                    "title" => $request->input("title"),
                    "userAccountID" => session("loggedInUserID")
                ]);

                session()->flash("status", "Kategorie '" . $request->input('title') . "' erfolgreich erstellt.");
                session()->flash("showAlert", "true");
                session()->flash("successAlert", "true");
                return redirect("/categories");
            }
        }
    }

    public function editCategory($id)
    {
        $dbCategoryData = Category::where("id", $id)->first();

        // category with $id does not exist
        if ($dbCategoryData == null)
            return redirect("/categories");
        else {
            session()->put("currentCategoryEditingID", $id);

            $shouldOpenModal = "edit";
            return redirect("/categories")->with([
                "shouldOpenModal" => $shouldOpenModal,
                "title" => $dbCategoryData->title
            ]);
        }
    }

    public function verifyCategoryEditing(Request $request)
    {
        $error = false;

        // error handling
        // category already exists
        if ($this->checkIfCategoryExists($request->input("title"))) {
            $error = true;

            session()->flash("modalStatus", "Eine Kategorie mit dem Namen '" . $request->input('title') . "' existiert bereits.");
        }
        //  null or only whitespaces
        else if ($request->input("title") == null || trim($request->input("title")) == "") {
            $error = true;

            session()->flash("modalStatus", "Der Name darf nicht leer sein oder nur aus Leerzeichen bestehen.");
        }


        //  error has occured
        if ($error) {
            $shouldOpenModal = "edit";
            return redirect("/categories")->with([
                "shouldOpenModal" => $shouldOpenModal,
                "title" => $request->input("title")
            ]);
        }
        // successful, update category
        else {
            Category::where("id", session("currentCategoryEditingID"))->first()->update([
                "title" => $request->input("title")
            ]);

            session()->forget("currentCategoryEditingID");

            session()->flash("status", "Kategorie erfolgreich umbenannt.");
            session()->flash("showAlert", "true");
            session()->flash("successAlert", "true");
            return redirect("/categories");
        }
    }

    public function deleteCategory($id)
    {
        $dbCategoryData = Category::where("id", $id)->first();

        // category with $id does not exist
        if ($dbCategoryData == null)
            return redirect("/categories");
        else {
            $dbCategoryUsageCount = Expense::where("categoryID", $id)->count();
            session()->put("currentCategoryDeletingID", $id);
            $shouldOpenModal = "confirmDelete";

            return redirect("/categories")->with([
                "shouldOpenModal" => $shouldOpenModal,
                "confirmDeleteTitle" => $dbCategoryData->title,
                "usageCount" => $dbCategoryUsageCount
            ]);
        }
    }

    public function confirmCategoryDeletion()
    {
        Category::where("id", session("currentCategoryDeletingID"))->first()->delete();

        session()->forget("currentCategoryDeletingID");
        session()->flash("status", "Kategorie erfolgreich gelöscht.");
        session()->flash("showAlert", "true");
        session()->flash("successAlert", "false");

        return redirect("/categories");
    }
    #endregion





    #region expense
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
                ->orderBy("timestamp")
                ->get() : array();

            $categories = Category::where("userAccountID", session("loggedInUserID"))->get();

            // retrieve bank accounts from database or set to an empty array when user has no bank accounts
            $bankAccounts = $bankAccountID != null ? BankAccount::where("userAccountID", session("loggedInUserID"))
                ->get() : array();

            // store in session variable if control elements have to be disabled
            $bankAccountID == null ? session()->flash("disableControls", true)
                : session()->flash("disableControls", false);

            if ($bankAccountID == null) {
                session()->now("status", "Du hast noch kein Konto, erstelle erst eins um Ausgaben hinzufügen zu können.");
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


    public function editExpense($id)
    {
        $dbExpenseData = Expense::where("id", $id)->first();
        $dbCategoryOfExpense = Category::where("id", $dbExpenseData->categoryID)->first();

        // expense with $id does not exist
        if ($dbExpenseData == null)
            return redirect("/expenses");
        else {
            session()->put("currentExpenseEditingID", $id);

            $shouldOpenModal = "edit";
            return redirect("/expenses")->with([
                "shouldOpenModal" => $shouldOpenModal,
                "timestamp" => $dbExpenseData->timestamp,
                "amount" => $dbExpenseData->amount,
                "description" => $dbExpenseData->description,
                "categoryID" => $dbCategoryOfExpense != null ? $dbCategoryOfExpense->id : null
            ]);
        }
    }

    public function verifyExpenseEditing(Request $request)
    {
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

    public function deleteExpense($id)
    {
        $dbExpenseData = Expense::where("id", $id)->first();

        // expense with $id does not exist
        if ($dbExpenseData == null)
            return redirect("/expenses");
        else {
            session()->put("currentExpenseDeletingID", $id);
            $shouldOpenModal = "confirmDelete";

            return redirect("/expenses")->with([
                "shouldOpenModal" => $shouldOpenModal,
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
    #endregion


    #region bank account
    public function showBankAccounts()
    {
        if (!session()->has('loggedInUsername'))
            return redirect("/login");
        else {

            $bankAccounts = BankAccount::where("userAccountID", session("loggedInUserID"))->get();

            return view("bankAccounts", [
                "bankAccounts" => $bankAccounts
            ]);
        }
    }

    public function addBankAccount(Request $request)
    {
        if (!session()->has('loggedInUsername'))
            return redirect("/login");
        else {
            // title unqualified (null or whitespaces)
            if ($request->input("title") == "" || trim($request->input("title") == "")) {
                return redirect("/bankAccounts")->with([
                    "shouldOpenModal" => "add",
                    "description" => $request->input("description"),
                    "modalStatus" => "Der Name darf nicht leer sein oder nur aus Leerzeichen bestehen."
                ]);
            } else {

                BankAccount::create([
                    "title" => $request->input("title"),
                    "description" => $request->input("description") == "" ? "" : $request->input("description"),
                    "balance" => "0",
                    "userAccountID" => session("loggedInUserID")
                ]);

                session()->flash("status", "Konto erfolgreich erstellt.");
                session()->flash("showAlert", "true");
                session()->flash("successAlert", "true");
                return redirect("/bankAccounts");
            }
        }
    }


    public function editBankAccount($id)
    {
        $dbBankAccountData = BankAccount::where("id", $id)->first();

        // bank account with $id does not exist
        if ($dbBankAccountData == null)
            return redirect("/bankAccounts");
        else {
            session()->put("currentBankAccountEditingID", $id);
            session()->put("balance", $dbBankAccountData->balance);

            $shouldOpenModal = "edit";
            return redirect("/bankAccounts")->with([
                "shouldOpenModal" => $shouldOpenModal,
                "title" => $dbBankAccountData->title,
                "description" => $dbBankAccountData->description,
            ]);
        }
    }

    public function verifyBankAccountEditing(Request $request)
    {
        $error = false;

        // error handling
        //  null or only whitespaces
        if ($request->input("title") == null || trim($request->input("title")) == "") {
            $error = true;

            session()->flash("modalStatus", "Der Name darf nicht leer sein oder nur aus Leerzeichen bestehen.");
        }

        //  error has occured
        if ($error) {
            $shouldOpenModal = "edit";
            return redirect("/bankAccounts")->with([
                "shouldOpenModal" => $shouldOpenModal,
                "description" => $request->input("description")
            ]);
        } else {

            // update expense
            BankAccount::where("id", session("currentBankAccountEditingID"))->first()->update([
                "title" => $request->input("title"),
                "description" => $request->input("description") == "" ? "" : $request->input("description")
            ]);

            session()->forget("currentBankAccountEditingID");

            session()->flash("status", "Konto erfolgreich bearbeitet.");
            session()->flash("showAlert", "true");
            session()->flash("successAlert", "true");
            return redirect("/bankAccounts");
        }
    }

    public function deleteBankAccount($id)
    {
        $dbBankAccountData = BankAccount::where("id", $id)->first();

        // bank account with $id does not exist
        if ($dbBankAccountData == null)
            return redirect("/bankAccounts");
        else {
            $dbBankAccountUsageCount = Expense::where("bankAccountID", $id)->count();
            session()->put("currentBankAccountDeletingID", $id);
            $shouldOpenModal = "confirmDelete";

            return redirect("/bankAccounts")->with([
                "shouldOpenModal" => $shouldOpenModal,
                "usageCount" => $dbBankAccountUsageCount
            ]);
        }
    }

    public function confirmBankAccountDeletion()
    {
        BankAccount::where("id", session("currentBankAccountDeletingID"))->first()->delete();

        if (session("currentSelectedBankAccountID") == session("currentBankAccountDeletingID"))
            session()->forget("currentSelectedBankAccountID");

        session()->forget("currentBankAccountDeletingID");
        session()->flash("status", "Konto erfolgreich gelöscht.");
        session()->flash("showAlert", "true");
        session()->flash("successAlert", "false");
        return redirect("/bankAccounts");
    }
    #endregion
}
