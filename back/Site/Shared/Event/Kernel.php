<?php


namespace Front\Event;

use Core\Kernel as CoreKernel;
use Core\Logger;
use Core\Response;
use Front\Controller\DefaultController;
use Front\Repository\PageRepository;

class Kernel
{
    /**
     * @param $current
     * @event core/kernel.boot
     */
    public static function KernelBoot(&$current) {
        $page = (new PageRepository())->getCurrentPage();
        if (CoreKernel::$routing->status === 404) {
            if (is_object($page)) {
                Response::setStatus(200);
                CoreKernel::$routing->define($_SERVER["REQUEST_URI"], DefaultController::class . "->renderPage", $_SERVER["HTTP_HOST"]);
            } else {
                Response::setStatus(200);
                CoreKernel::$routing->define($_SERVER["REQUEST_URI"], DefaultController::class . "->notFound", $_SERVER["HTTP_HOST"], ["title" => "Page Not Found"]);
            }
        }
    }
}