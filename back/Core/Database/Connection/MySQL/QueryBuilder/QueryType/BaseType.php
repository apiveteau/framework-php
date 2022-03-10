<?php


namespace Core\Database\Connection\Mysql\Type;

use Core\Connection\Mysql;
use Core\Database\Manager;

/**
 * Class BaseType
 * @package Core\Database\Connection\Mysql\Type
 */
abstract class BaseType implements Type
{
    /**
     * @var array $configuration
     */
    protected static $configuration;

    public function __construct($tablename = "")
    {
        self::$configuration["table"]["name"] = Manager::getConnection("mysql")->getTableName($tablename);
    }

    /**
     * @param integer $nbr
     * @return $this
     */
    public function limit($nbr) {
        self::$configuration["limit"]["number"] = $nbr;
        self::$configuration["limit"]["sql"] = " LIMIT " . $nbr;
        return $this;
    }

    /**
     * @param integer $nbr
     * @return $this
     */
    public function offset($nbr) {
        self::$configuration["offset"]["number"] = $nbr;
        self::$configuration["offset"]["sql"] = " OFFSET " . $nbr;
        return $this;
    }

    /**
     * @param array $innerJoinConfiguration
     * @return $this;
     * join => [
     *      inner => [
     *          [
     *              [table1 => field],
     *              [table2 => field],
     *               operator => =
     *          ],
     *       ],
     * ]
     */
    public function innerJoin(array $innerJoinConfiguration) {
        self::$configuration["join"]["inner"] = $innerJoinConfiguration;
        self::$configuration["join"]["inner"]["sql"] = "";
        $i = 0;
        foreach ($innerJoinConfiguration as $join) {
            $table1 = array_keys($join[0])[0];
            $table2 = array_keys($join[1])[0];
            self::$configuration["join"]["inner"]["sql"] .= " INNER JOIN `" . strtolower($table2)
                . "` ON `" . Manager::getConnection("mysql")->getTableName($table1) . "`.`" . strtolower($join[0][$table1]) . "` "
                . $join["operator"]
                . " `" . Manager::getConnection("mysql")->getTableName($table2) . "`.`" . strtolower($join[1][$table2]) . "`";
            $i++;
        }
        return $this;
    }

    /**
     *      where: [
     *          [fieldname, operator, value],
     *          [fieldname, operator, value, concatenator: AND],
     *          [fieldname, operator, value, concatenator: OR]
     *      ]
     * @param array $whereConfiguration
     * @return $this
     */
    public function where(array $whereConfiguration) {
        $i = 0;
        self::$configuration["where"] = $whereConfiguration;
        $sql = " WHERE ";
        foreach ($whereConfiguration as $condition) {
            if ($i !== 0) {
                if (isset($condition["concatenator"]))
                    $sql .= " " . $condition["concatenator"] . " `" . $condition[0] . "` " . $condition[1] . " \"" . $this->quote($condition[2]) . "\"";
                else
                    $sql .= " AND `" . $condition[0] . "` " . $condition[1] . " " . $this->quote($condition[2]);
            }
            else
                $sql .= "`" . $condition[0] . "` " . $condition[1] . " " . $this->quote($condition[2]);
            $i++;
        }
        self::$configuration["where"]["sql"] = $sql;
        return $this;
    }

    /**
     * @return array
     */
    public function execute() {
        $tablename = Manager::getConnection("mysql")->getTableName(self::$configuration["table"]["name"]);
        $result[$tablename] = Manager::getConnection("mysql")->exec($this->getQuery());
        return  $result;
    }

    /**
     * @return array
     */
    public function getConfiguration() {
        return self::$configuration;
    }

    /**
     * @param $string
     * @return mixed
     */
    protected function quote($string) {
        return '"' . str_replace('"', '\"', str_replace('\\', '/', str_replace("--", " - - ", $string))) . '"';
    }
}