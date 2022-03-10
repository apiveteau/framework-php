<?php


namespace Admin\Controller;


use Core\Controller;
use Core\Logger;
use Core\Security\Session;
use Core\Security\Token;
use Front\Repository\PageRepository;
use Front\Repository\UserRepository;
use Front\Model\User;

class AdminController extends Controller
{
    public function __construct()
    {
        if (!Session::get() && !in_array($_SERVER["REQUEST_URI"], ["/admin", "/admin/verify"]))
            $this->redirect("/admin");
    }

    public function adminLogin() {
//         $user = new User();
//         $user->username = "wazhabits";
//         $user->password = password_hash("skroBy*a", PASSWORD_BCRYPT);
//         $user->email = "anatole.piveteau@gmail.com";
//         $user->fullname = "Anatole Piveteau";
//         $user->image = "";
//         $user->description = "Wazhaaa";
//         $user->level = "1000";
//         $user->save();
        if (Session::get()) {
            $this->redirect("/admin/dashboard");
        }
        $this->render("Site/Admin/Resource/html/access", []);
    }

    public function adminVerify($args) {
        $username = $args["route"]["arguments"]["post"]["username"];
        $password = $args["route"]["arguments"]["post"]["password"];
        return ((new UserRepository())->verify($username, $password));
    }

    public function dashboard() {
        $this->render("Site/Admin/Resource/html/dashboard", ["pages" => (new PageRepository())->getPages(), "title" => "Dashboard"]);
    }

    public function moduleUsers() {
        $this->render("Site/Admin/Resource/html/modules/users", ["title" => "User administration"]);
    }

    public function modulePages() {
        $this->render("Site/Admin/Resource/html/modules/pages", ["pages" => (new PageRepository())->getPages(), "title" => "Dashboard"]);
    }

    public function moduleIcons() {
        $this->render("Site/Admin/Resource/html/modules/icons", ["title" => "Icons"]);
    }

    public function logout() {
        $_SESSION["timeout"] = 0;
        return(["status" => 200]);
    }
}