<?php

namespace EasySQL\Query\Syntax;

use EasySQL\Query\Builder;

abstract class SqlSyntax
{
    
    private $parts = [
            "join",
            "where",
            "group",
            "having",
            "options",
            "union"
            ];

    /**
     * Setup the select query string
     *
     * @param Builder $builder
     *
     * @return string $query
     */
    public function selectSyntax(array $selectComponents)
    {
        $query = "SELECT ";
        $query .= (isset($selectComponents["select"]["aggregate"])) ?
        strtoupper($selectComponents["select"]["aggregate"]) . "({$selectComponents["select"]["column"]}) AS {$selectComponents["select"]["aggregate"]}" :
        implode(",", $selectComponents["select"]);
        $query .= " FROM {$selectComponents["table"]}";

        foreach ($this->parts as $part) {
            $query .= $this->{$part."Syntax"}($selectComponents[$part]);
        }

        return $query;
    }

    /**
     * Set up in clause of the query
     *
     * @param array $components
     *
     * @return string
     */
    public function inSyntax($components)
    {
        return (is_array($components["list"])) ?
        " " . strtoupper($components["conjunction"]) . " {$components["not"]} {$components["column"]} IN (" . implode(",", $components["list"]) . ")" :
        " " . strtoupper($components["conjunction"]) . " {$components["not"]} {$components["column"]} IN {$components["list"]}";
    }

    /**
     * Set up between clause of the query
     *
     * @param array $components
     *
     * @return string
     */
    public function betweenSyntax($components)
    {
        return " " . strtoupper($components["conjunction"]) . " {$components["column"]} {$components["not"]} BETWEEN {$components["values"][0]} AND {$components["values"][1]}";
    }

    /**
     * Set up exists clause of the query
     *
     * @param array $components
     *
     * @return string
     */
    public function existsSyntax($components)
    {
        return " " . strtoupper($components["conjunction"]) . " {$components["not"]} {$components["column"]} EXISTS {$components["value"]}";
    }

    /**
     * Set up where clause of the query
     *
     * @param array $components
     *
     * @return string
     */
    public function whereSyntax(array $components)
    {
        if (empty($components)) {
            return null;
        }

        $query = " WHERE";

        foreach ($components as $component) {
            $query .= ($component["type"] == "basic") ?
            " " . strtoupper($component["condition"]["conjunction"]) . " {$component["condition"]["not"]} {$component["condition"]["column"]} {$component["condition"]["operator"]} {$component["condition"]["value"]}" :
            $this->{$component["type"] ."Syntax"}($component["condition"]);
        }

        return $query;
    }

    /**
     * Set up group clause of the query
     *
     * @param array $components
     *
     * @return string
     */
    public function groupSyntax(array $components)
    {
        $query = (!empty($components)) ? " GROUP BY " . implode(",", $components) : "";

        return $query;
    }

    /**
     * Set up having clause of the query
     *
     * @param array $components
     *
     * @return string
     */
    public function havingSyntax(array $components)
    {
        $query = (!empty($components)) ? " HAVING {$components["column"]} {$components["operator"]} {$components["value"]}" : "";

        return $query;
    }

    /**
     * Set up union clause of the query
     *
     * @param array $components
     *
     * @return string
     */
    public function unionSyntax(array $components)
    {
        $query = null;

        if (!empty($components)) {
            foreach ($components as $component) {
                $query .= " UNION {$component}";
            }
        }

        return $query;
    }

    /**
     * Set up join clause of the query
     *
     * @param array $components
     *
     * @return string
     */
    public function joinSyntax(array $components)
    {
        $query = null;
        
        foreach ($components as $table => $component) {
            $query .= " ".strtoupper($component["type"])." {$table} ON {$component["local_column"]} {$component["operator"]} {$component["joined_column"]}";
        }

        return $query;
    }

    /**
     * Set up options clause of the query
     *
     * @param array $components
     *
     * @return string
     */
    public function optionsSyntax(array $components)
    {
        $query = null;

        $query .= (isset($components["order"])) ? " ORDER BY {$components["order"]["column"]} {$components["order"]["type"]}" : null;
        $query .= (isset($components["limit"])) ? " LIMIT {$components["limit"]}" : null;

        return $query;
    }

    /**
     * Set up insert query string
     *
     * @param Builder $builder
     *
     * @return string
     */
    public function insertSyntax(string $table, array $components)
    {
        $prepared = array_map(
            function ($parameter) {
                return "?";
            },
            $components
        );

        $columns = array_keys($components);

        return "INSERT INTO " . $table . " (" . implode(",", $columns) . ") VALUES (" . implode(",", $prepared) . ")";
    }

    /**
     * Set up update query string
     *
     * @param Builder $builder
     *
     * @return string
     */
    public function updateSyntax(array $updateComponents)
    {
        $query = "UPDATE " . $updateComponents["table"] . " SET " . $updateComponents["update"] . " = ?";

        foreach ($this->parts as $part) {
            $query .= $this->{$part."Syntax"}($updateComponents[$part]);
        }

        return $query;
    }

    /**
     * Set up delete query string
     *
     * @param Builder $builder
     *
     * @return string
     */
    public function deleteSyntax(array $deleteComponents)
    {
        $query = "DELETE FROM " . $deleteComponents["table"];

        foreach ($this->parts as $part) {
            $query .= $this->{$part."Syntax"}($deleteComponents[$part]);
        }

        return $query;
    }
}
