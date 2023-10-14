<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Expense;

class CategoryController extends Controller
{
    public function checkIfCategoryExists($title)
    {
        if (Category::where("title", $title)->where("userAccountID", session("loggedInUserID"))->where("id", "!=", session("currentCategoryEditingID"))->first() != null)
            return true;
        else
            return false;
    }

    public function showCategories()
    {
        if (!session()->has('loggedInUsername'))
            return redirect("/login");
        else {
            $categories = Category::where("userAccountID", session("loggedInUserID"))
                ->select("id", "title")
                ->orderBy("title")
                ->get();

            return view("categories", [
                "categories" => $categories
            ]);
        }
    }

    public function addCategory(Request $request)
    {
        session()->put("shouldOpenModal", "add");

        if (!session()->has('loggedInUsername'))
            return redirect("/login");
        else {
            // store title in session
            session()->flash("title", $request->input("title"));

            // title unqualified (null or whitespaces)
            if ($request->input("title") == "" || trim($request->input("title") == "")) {
                return redirect("/categories")->with([
                    "shouldOpenModal" => "add",
                    "modalStatus" => "Der Name darf nicht leer sein oder nur aus Leerzeichen bestehen."
                ]);
            }
            // title length exceeds maximum
            else if (mb_strlen($request->input("title")) > config("formValidation.maxInputLengthShort")) {
                return redirect("/categories")->with([
                    "shouldOpenModal" => "add",
                    "modalStatus" => "Der Name ist zu lang."
                ]);
            }
            // category already exists
            else if ($this->checkIfCategoryExists($request->input("title"))) {
                session()->flash("modalStatus", "Eine Kategorie mit dem Namen '" . $request->input('title') . "' existiert bereits.");
                return redirect("/categories")->with([
                    "shouldOpenModal" => "add",
                    "title" => $request->input("title")
                ]);
            }
            // no errors, create category
            else {
                Category::create([
                    "title" => $request->input("title"),
                    "userAccountID" => session("loggedInUserID")
                ]);

                session()->flash("status", "Kategorie erfolgreich erstellt.");
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

            return redirect("/categories")->with([
                "shouldOpenModal" => "edit",
                "title" => $dbCategoryData->title
            ]);
        }
    }

    public function verifyCategoryEditing(Request $request)
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
        // category already exists
        if ($this->checkIfCategoryExists($request->input("title"))) {
            $error = true;

            session()->flash("modalStatus", "Eine Kategorie mit dem Namen '" . $request->input('title') . "' existiert bereits.");
        }


        //  error has occured
        if ($error) {
            //session()->flash("title", $request->input("title"));

            return redirect("/categories")->with([
                "shouldOpenModal" => "edit",
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

            return redirect("/categories")->with([
                "shouldOpenModal" => "confirmDelete",
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
}
