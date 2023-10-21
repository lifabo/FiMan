<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserAccount;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function testAccountPW()
    {
        dd(Hash::make(""));
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
        $dbUserData = UserAccount::where('username', $formUserName)
            ->select("id", "username", "passwd")
            ->first();

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

            // save last successful login
            date_default_timezone_set('Europe/Berlin');
            UserAccount::where('username', $formUserName)->first()->update([
                "lastLogin" => date('Y-m-d H:i:s')
            ]);

            return redirect("/");
        }
    }

    public function logout()
    {
        session()->invalidate();
        return redirect("/");
    }
}
