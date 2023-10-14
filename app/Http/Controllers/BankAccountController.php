<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BankAccount;
use App\Models\Expense;

class BankAccountController extends Controller
{
    public function showBankAccounts()
    {
        if (!session()->has('loggedInUsername'))
            return redirect("/login");
        else {

            $bankAccounts = BankAccount::where("userAccountID", session("loggedInUserID"))->get();

            $balanceAllAccounts = BankAccount::where("userAccountID", session("loggedInUserID"))
                ->sum("balance");

            return view("bankAccounts", [
                "bankAccounts" => $bankAccounts,
                "balanceAllAccounts" => $balanceAllAccounts
            ]);
        }
    }

    public function addBankAccount(Request $request)
    {
        if (!session()->has('loggedInUsername'))
            return redirect("/login");
        else {
            $error = false;

            // error handling
            // title unqualified (null or whitespaces)
            if ($request->input("title") == "" || trim($request->input("title") == "")) {
                $error = true;

                session()->flash("modalStatus", "Der Name darf nicht leer sein oder nur aus Leerzeichen bestehen.");
            }
            // title length exceeds maximum
            if (mb_strlen($request->input("title")) > config("formValidation.maxInputLengthShort")) {
                $error = true;

                session()->flash("modalStatus", "Der Name ist zu lang.");
            }
            // description length exceeds maximum
            else if (mb_strlen($request->input("description")) > config("formValidation.maxInputLengthLong")) {
                $error = true;

                session()->flash("modalStatus", "Die Beschreibung ist zu lang.");
            }

            //  error has occured
            if ($error) {
                return redirect("/bankAccounts")->with([
                    "shouldOpenModal" => "add",
                    "title" => $request->input("title"),
                    "description" => $request->input("description")
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

            return redirect("/bankAccounts")->with([
                "shouldOpenModal" => "edit",
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
        // title length exceeds maximum
        else if (mb_strlen($request->input("title")) > config("formValidation.maxInputLengthShort")) {
            $error = true;

            session()->flash("modalStatus", "Der Name ist zu lang.");
        }
        // description length exceeds maximum
        else if (mb_strlen($request->input("description")) > config("formValidation.maxInputLengthLong")) {
            $error = true;

            session()->flash("modalStatus", "Die Beschreibung ist zu lang.");
        }

        //  error has occured
        if ($error) {
            return redirect("/bankAccounts")->with([
                "shouldOpenModal" => "edit",
                "title" => $request->input("title"),
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

            return redirect("/bankAccounts")->with([
                "shouldOpenModal" => "confirmDelete",
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
}
