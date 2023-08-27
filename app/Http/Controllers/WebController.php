<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\UserAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class WebController extends Controller
{
    public function showHomepage()
    {
        if (!session()->has('loggedInUsername'))
            return redirect("/login");
        else {
            return view("homepage");
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






    #region category stuff
    public function showCategories()
    {
        if (!session()->has('loggedInUsername'))
            return redirect("/login");
        else {
            $categories = Category::all();

            return view("categories", [
                "categories" => $categories
            ]);
        }
    }

    public function checkIfCategoryExists($title)
    {
        if (Category::where("title", $title)->first() != null)
            return true;
        else
            return false;
    }

    public function addCategory(Request $request)
    {
        if (!session()->has('loggedInUsername'))
            return redirect("/login");
        else {
            // category already exists
            if ($this->checkIfCategoryExists($request->input("title"))) {
                session()->flash("modalStatus", "Eine Kategorie mit dem Namen " . $request->input('title') . " existiert bereits.");
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

                session()->flash("status", "Kategorie " . $request->input('title') . " erfolgreich erstellt.");
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
        // category already exists
        if ($this->checkIfCategoryExists($request->input("title"))) {
            session()->flash("modalStatus", "Eine Kategorie mit dem Namen " . $request->input('title') . " existiert bereits.");
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
            Category::where("id", $id)->first()->delete();

            session()->flash("status", "Kategorie erfolgreich gelöscht.");
            return redirect("/categories");
        }
    }
    #endregion
}
