<?php
/**
 * Created by PhpStorm.
 * User: gatas
 * Date: 3/2/2019
 * Time: 10:19 μμ
 */

namespace EasySQL\Src\Data;

class MysqliDAO extends DAO
{
    /**
     * Executes a query
     *
     * @param string $query         The SQL query to be executed.
     * @param array  $params        The parameters array passed by the user.
     * @param int    $successString If the query does not return data return string.
     *
     * @return array|string         The array of the data resulted from the query or a string
     *                               of successfull execution of the query.
     *
     * @throws \MysqliException
     */
    public function executeQuery(string $query, array $params)
    {
        $data = [];
        // Prepare statement and execute it.
        $stmt = $this->db->prepare($query);

        if (!empty($params)) {
            $stmt->bind_param($this->getTypesString($params), ...$params);
        }

        $stmt->execute();

        if ($result = $stmt->get_result()) {
            // Fetch results as associative arrays and save them in a new array.
            while ($row = $result->fetch_assoc()) {
                array_push($data, $row);
            }
        }

        $stmt = null;

        return $data;
    }

    private function getTypesString($params)
    {
        $types = '';
        foreach ($params as $param) {
            switch (gettype($param)) {
                case 'integer':
                    $types .= 'i';
                    break;
                case 'string':
                    $types .= 's';
                    break;
                case 'double':
                    $types .= 'd';
                    break;
            }
        }

        return $types;
    }
}
