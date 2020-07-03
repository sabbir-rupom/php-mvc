<?php

namespace Core;

use \PDO;

/**
 * Base model
 */
abstract class Model
{
    private $db;
    protected $table = null;
    protected $primaryKey = null;

    public function __construct()
    {
        $this->db = getDbConnection();
    }

    public function getDB()
    {
        return $this->db;
    }

    /**
     * Retrieve records by table Primary Key/ID
     *
     * @param int|string $id Table Primary Key/ID
     * @return object|array Search result of called model class
     */
    public function find($id, string $select = '*', bool $array = false)
    {
        $sql = "SELECT {$select} FROM {$this->table} WHERE "
            . (empty($this->primaryKey) ? 'id' : $this->primaryKey) . " = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->bindParam(1, $id);
        $stmt->execute();
        if ($array) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $result = $stmt->fetch(PDO::FETCH_OBJ);
        }
        return $result;
    }

    /**
     * Retrieve first record from conditions specified
     *
     * @param array $filterParams Filter conditions
     * @param string $select Select columns
     * @param bool $array Flag for returning array result
     * @return object|array Query result
     */
    public function findBy(array $filterParams = [], string $select = '*', bool $array = false)
    {
        list($conditionSql, $values) = self::constructQuery($filterParams);
        $sql = "SELECT {$select} FROM {$this->table} {$conditionSql}";

        $stmt = $this->db->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute($values);

        if ($array) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $result = $stmt->fetch(PDO::FETCH_OBJ);
        }
        return $result;
    }

    /**
     * Retrieve all records from conditions specified
     *
     * @param array $filterParams Filter conditions
     * @param string $select Select columns
     * @param array $orderBy Result data ordering parameters
     *              Array $key = Column name e.g {id}
     *              Array $value = Order Direction e.g {ASC}
     * @param array $limitArgs Limit/Offset array parameter e.g ['limit' => 10, 'offset' => 0]
     * @param bool $array Flag for returning array result
     * @return object|array Query result
     */
    public function findAllBy(array $filterParams = [], string $select = '*', array $orderBy = [], array $limitArgs = [], bool $array = false)
    {
        list($conditionSql, $values) = self::constructQuery($filterParams, $orderBy, $limitArgs);
        $sql = "SELECT {$select} FROM {$this->table} {$conditionSql}";

        $stmt = $this->db->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute($values);
        if ($array) {
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        }
        return $result;
    }

    /**
     * Returns the number of records matching the specified condition
     *
     * @param array $filterParams Filter conditions
     * @return int Number of records from query
     */
    public function countBy(array $filterParams = array()): int
    {
        list($conditionSql, $values) = self::constructQuery($filterParams);

        $sql = "SELECT count( * ) as count FROM {$this->table} {$conditionSql}";

        $stmt = $this->db->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute($values);
        $records = $stmt->fetchAll();
        $count = 0;
        if (!empty($records[0]->count)) {
            $count = $records[0]->count;
        }
        return intval($count);
    }

    /**
     * Execute SQL query directly
     *
     * @param string $sql Prepared SQL statement
     * @param bool $array Flag for returning array result
     * @param bool $single Flag for returning single row
     * @return object|array Query result
     */
    public function query(string $sql = '', bool $array = false, bool $single = false)
    {
        if (!empty($sql)) {
            return null;
        }

        $result = $this->db->query($sql, $single ? PDO::FETCH_ORI_FIRST : PDO::FETCH_OBJ);

        return $array ? (array) $result : $result;
    }

    /**
     * To build conditional statements for requested query and prepare binding clause
     *
     * @param array $filterParams Filter conditions
     * @param array $orderBy Result data ordering parameters
     *              Array $key = Column name e.g {id}
     *              Array $value = Order Direction e.g {ASC}
     * @param array $limitArgs Limit/Offset array parameter e.g ['limit' => 10, 'offset' => 0]
     * @return type
     */
    protected static function constructQuery(array $filterParams, array $orderBy = [], array $limitArgs = [])
    {
        list($conditions, $values) = self::constructQueryCondition($filterParams);

        $sql = "";
        if (!empty($conditions)) {
            $sql .= " WHERE " . join(' AND ', $conditions);
        }
        if (is_array($orderBy) && !empty($orderBy)) {
            $sql .= " ORDER BY ";
            foreach ($orderBy as $key => $val) {
                $sql .= "{$key} {$val}";
                break;
            }
        }
        if (isset($limitArgs) && array_key_exists('limit', $limitArgs)) {
            if (array_key_exists('offset', $limitArgs)) {
                $sql .= " LIMIT " . $limitArgs['offset'] . ", " . $limitArgs['limit'];
            } else {
                $sql .= " LIMIT " . $limitArgs['limit'];
            }
        }
        return array($sql, $values);
    }

    /**
     * To construct query conditions
     *
     * @param array $filterParams Filter conditions
     * @return array Constructed Query condition
     */
    protected static function constructQueryCondition(array $params): array
    {
        $condition = $values = [];
        $operator = '=';
        if (empty($params)) {
            return [$condition, $values];
        } else {
            foreach ($params as $k => $v) {
                if (empty($v)) {
                    continue;
                } elseif (is_array($v)) {
                    $conditions[] = $k . ' IN (' . implode(',', array_fill(0, count($v), '?')) . ')';
                    $values = array_merge($values, $v);
                } else {
                    $k = explode(' ', trim($k));
                    $operator = isset($k[1]) ? $k[1] : $operator;

                    switch ($operator) {
                        case '=':
                        case '<>':
                        case '!=':
                        case '>=':
                        case '<=':
                        case '>':
                        case '<':
                            $conditions[] = $k[0] . " $operator ?";
                            $values[] = $v;
                            break;
                        case 'like':
                            $conditions[] = $k[0] . " $operator ?";
                            $values[] = ("%" . $v . "%");
                            break;
                        default:
                            $conditions[] = $k[0] . " = ?";
                            $values[] = $v;
                            break;
                    }
                }
            }
        }

        return [$conditions, $values];
    }

    /**
     * Insert new record in database
     *
     * @param array $data
     * @return void | int
     */
    public function insert(array $data)
    {
        $columns = array_keys($data);
        $values = array_values($data);

        $sql = 'INSERT INTO ' . $this->table . ' (' . join(',', $columns);
        $sql .= ') VALUES (' . str_repeat('?,', count($columns) - 1) . '?)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($values);
        return $this->db->lastInsertId();
    }

    /**
     * To update table record
     *
     * @param array $data Update data array
     * @param array $filterParams  Filter conditions
     * @return void | int Number of updated rows
     */
    public function update(array $data, array $filterParams = [])
    {
        list($conditionSql, $values) = self::constructQuery($filterParams);

        $columns = array_keys($data);
        $values = array_merge(array_values($data), $values);


        $sql = 'UPDATE ' . $this->table . ' SET ';
        $setStmts = array();
        foreach ($columns as $column) {
            $setStmts[] = $column . '=?';
        }
        $sql .= join(',', $setStmts);
        $sql .= " $conditionSql";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($values);
        /*
         * Return updated record count
         */
        return $stmt->rowCount();
    }

    /**
     * Delete table record
     *
     * @param array $filterParams  Filter conditions
     * @return boolean
     * @throws Exception
     */
    public function delete(array $filterParams = [])
    {
        if (!isset($this->{$table})) {
            throw new Exception('The ' . get_called_class() . ' is not initiated properly.');
        }

        list($conditionSql, $values) = self::constructQuery($filterParams);

        $stmt = $pdo->prepare('DELETE FROM ' . $this->table . ' ' . $conditionSql);
        $stmt->execute($values);

        return true;
    }

    /**
     * Start database query transaction
     *
     * @return bool
     */
    public function startTransaction(): bool
    {
        return $this->db->beginTransaction();
    }

    /**
     * End database query transaction
     *
     * @return bool
     */
    public function commit(): bool
    {
        return $this->db->commit();
    }

    /**
     * Rollback all query transaction
     *
     * @return bool
     */
    public function rollback(): bool
    {
        return $this->db->rollback();
    }

    /**
     * Check transaction is active / not disrupted
     *
     * @return bool
     */
    public function transactionStatus(): bool
    {
        return $this->db->inTransaction();
    }
}
