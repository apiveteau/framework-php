<?php

namespace Core\Template\Parts;

use Core\Environment;
use Core\Files;
use Core\Kernel;
use Core\Logger;

class Assets
{
    /**
     * Vars constructor.
     * @param $buffer
     * @param $args
     * @param string $quote
     */
    public function __construct(&$buffer, &$args)
    {
        $this->doAsset($buffer);
    }

    /**
     * This function build an asset call in template
     * @param $buffer
     */
    private function doAsset(&$buffer) {
        $matches = [];
        preg_match_all("/{assets:(([a-zA-Z0-9\/]*)\.([\w|\w.\w+]*))}/", $buffer, $matches);
        $i = 0;
        while ($i < count($matches[1])) {
            $this->makePath($matches[2][$i], $matches[3][$i], $filePath, $htmlFilePath);
            $buffer = str_replace("{assets:" . $matches[2][$i] . "." . $matches[3][$i] . "}", $this->buildHTML($htmlFilePath), $buffer);
            $i++;
        }
    }

    /**
     * @param $match
     * @param $extension
     * @param $readablePath
     * @param $htmlPath
     */
    private function makePath($match, $extension, &$readablePath, &$htmlPath) {
        $readablePath = PATH_ROOT .  $match . "." . $extension;
        $htmlPath = "Public/temp/assets/" . md5(date(Environment::getConfiguration("DATE_FORMAT")) . $readablePath) . "." . $extension;
        if (!file_exists(PATH_ROOT . $htmlPath) || (Kernel::$context === "Develop")) {
            Logger::log("template", "Save '" . $htmlPath . "' cache file from " . $readablePath, Logger::$DEBUG_LEVEL);
            if (!file_exists($readablePath))
                Logger::log("template", "The assets " . $readablePath . " not exist", Logger::$WARNING_LEVEL);
            Files::put(PATH_ROOT . $htmlPath, Files::read($readablePath), true);
        }
        $htmlPath = "/" . $htmlPath;
    }

    /**
     * @param $path
     * @return string
     */
    private function buildHTML($path) {
        $extension = explode(".", $path)[1];
        if ($extension === "css" || $extension === ".min.css")
            $html = "<link rel='stylesheet' href='" . $path . "'>";
        elseif ($extension === "js")
            $html = "<script src='" . $path . "'></script>";
        return $html;
    }
}