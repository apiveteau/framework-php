<?php


namespace Executable;


class Commander
{
    private $rootPath = "";

    public function __construct()
    {
        $this->rootPath = __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR;
    }

    public function execute($arguments) {
        $domain = $arguments[1];
        $action = $arguments[2];
        return $this->treat($domain, $action, array_values($arguments));
    }

    private function treat($domain, $action, $args) {
        if (method_exists($this, $domain . ucfirst($action)))
            return $this->{$domain . ucfirst($action)}($args);
        else
            return $domain . " domain does not exist";
    }

    private function sessionClear($args) {
        $print = "";
        $args[] = "token";
        $this->deleteRecursively($this->rootPath . "Cache", $args, $result);
        if (is_array($result)) {
            $print = count($result) . " session closed\n";
            foreach ($result as $type => $files) {
                foreach ($files as $deletion) {
                    $print .= "\t->" . $deletion["name"] . "(" . $deletion["size"] . ")\n";
                }
            }
        }
        return $print;
    }

    private function cacheClear($args) {
        $print = "";
        $args[] = "html";
        $args[] = "cache";
        $args[] = "css";
        $args[] = "js";
        $args[] = "json";
        $args[] = "sql";
        $this->deleteRecursively($this->rootPath . "Cache", $args, $result);
        $this->deleteRecursively($this->rootPath . "Public" . DIRECTORY_SEPARATOR . "temp", $args, $result);
        if (is_array($result)) {
            $print = count($result) . " files deleted\nFiles :\n";
            foreach ($result as $type => $files) {
                $print .= $type . " type files :\n";
                foreach ($files as $deletion) {
                    $print .= "\t->" . $deletion["name"] . "(" . $deletion["size"] . ")\n";
                }
            }
        }
        return $print;
    }

    private function logClear($args) {
        $args[] = "log";
        $print = "";
        $this->deleteRecursively($this->rootPath . "Log", $args,$result);
        if (is_array($result)) {
            $print = count($result) . " files deleted\nFiles :\n";
            foreach ($result as $type => $files) {
                $print .= $type . " type files :\n";
                foreach ($files as $deletion) {
                    $print .= "\t->" . $deletion["name"] . "(" . $deletion["size"] . ")\n";
                }
            }
        }
        return $print;
    }

    private function deleteRecursively($path, $constraint, &$result, $depth = 0)
    {
        $scan = glob($path . DIRECTORY_SEPARATOR . "*");
        foreach ($scan as $path) {
            if (is_dir($path)) {
                $this->deleteRecursively($path, $constraint,$result, $depth + 1);
            } else {
                unset($constraint[0], $constraint[1]);
                $extension = explode(".", $path)[count(explode(".", $path)) - 1];
                if (in_array($extension, $constraint)) {
                    $result[$extension][] = ["name" =>$path, "size" => filesize($path)];
                    unlink($path);
                }
            }
        }
    }
}
if ($argc < 3) {
    echo "#####\n### Commander\n#####\nCommand must respect this structure :\n\tphp Path\\To\\Commander.php domain:action <optional1> <optional2> ...";
    return;
}
echo (new Commander())->execute($argv);