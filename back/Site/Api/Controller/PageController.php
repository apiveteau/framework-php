<?php


namespace Api\Controller;

use Core\Controller;
use Core\Database\Manager;
use Core\Security\Session;
use Front\Model\Page;
use Front\Repository\PageRepository;

class PageController extends Controller
{
    public function __construct()
    {
        if (!Session::get())
            $this->redirect($_SERVER["HTTP_HOST"] . "/404" . $_SERVER["REQUEST_URI"]);
    }

    public function create($arguments) {
        $pageData = $arguments["route"]["arguments"]["post"];
        $page = (new Page($pageData))
			->save();
        return ["status" => 200, "result" => $page];
    }

    public function get($arguments) {
        if (isset($arguments["route"]["arguments"]["id"])) {
            return (new PageRepository())
                ->findBy("id", $arguments["route"]["arguments"]["id"], "=");
        } else {
            return (new PageRepository())
                ->getPages();
        }
    }

    public function update($arguments) {
        return [
            "result" => (new Page($arguments["route"]["arguments"]["post"]))->save(),
            "status" => 200
        ];
    }
    
    public function delete($arguments) {
        return [
                "result" => Manager::getConnection("mysql")
                    ->getQueryBuilder(Page::class)
                    ->delete(Page::class)
                    ->where([["id", "=", $arguments["route"]["arguments"]["id"]]])
                    ->execute(),
                "status" => 200
            ];
    }
}