<?php

namespace Build;

require_once('XMLParser.php');

class SQLBuilder extends XMLParser
{

    protected $db;


    public function __construct($db)
    {
        $this->db = $db;
    }


    /**
     * Setup the sql tables from the xml rules
     **/
    public function tablesFromXML($file)
    {
        $queries = [];

        try {
            $sql_array = get_object_vars($this->XMLtoObject($this->parseXML($file)));
        } catch (Exception $e) {
            die($e->getMessage());
        }

        foreach ($sql_array as $table) {
            $i     = 0;
            $table = get_object_vars($table);
            $key   = $table['@attributes']['name'];
            $queries[$key]['field'] = '';
            foreach ($table['field'] as $field) {
                $field = $this->setUpQuery($field);
                $foreign = $this->checkForeign($field);

                 $queries[$key]['field'] = $queries[$key]['field'].' '.$field->name.'
                '.$field->type.' '.$field->primary.' '.$field->unique.' '.$field->null.'
                '.$field->default.' '.$field->increment;

                if ($i < (sizeof($table['field']) - 1)) {
                    $queries[$key]['field'] = $queries[$key]['field'].',';
                }

                $i++;
            }

            if (isset($foreign)) {
                $queries[$key]['field'] .= ', '.$foreign;
            }
        }

        return $queries;
    }

    public function setUpQuery($field)
    {
        if ($field->primary == 'TRUE') {
                    $field->primary = 'PRIMARY KEY';
        }

        if ($field->increment == 'TRUE') {
            $field->increment = 'AUTO_INCREMENT';
        }

        if ($field->unique == 'TRUE') {
            $field->unique = 'UNIQUE';
        }

        if ($field->null == 'FALSE') {
            $field->null = 'NOT NULL';
        } else {
            $field->null = 'NULL';
        }

        if (isset($field->default)) {
            $field->default = 'DEFAULT '.$field->default;
        }

        return $field;
    }

    public function checkForeign($field)
    {
        if (isset($field->foreign)) {
            $foreign = 'FOREIGN KEY('.$field->name.') REFERENCES
            '.$field->foreign->reference->parent.'('.$field->foreign->reference->table.')';
        }
    }


    /**
     * Populate each sql table from the XML rules
     **/
    public function populateFromXML($file)
    {
        $queries = $this->tablesFromXML($file);
        foreach ($queries as $key => $query) {
            try {
                $this->db->query('CREATE TABLE '.$key.'('.$query['field'].')');
                echo 'Table '.$key." created successfully\n";
            } catch (PDOException $e) {
                echo 'Error creating table: '.$e->getMessage()."\n";
                return false;
            }

            $this->createObject($file, $query);
        }
    }


    /**
     * Create the object file for every sql table using the prototype (prototype.php)
     **/
    public function createObject($file, $query)
    {
        $className = basename(pathinfo($file, PATHINFO_FILENAME));

        $data = file_get_contents(__DIR__.'/prototype.cnf');

        $data = preg_replace('/\<the\-class\-name\>/', $className, $data);

        file_put_contents(dirname(__DIR__).'/EasySQL/Src/API/DAOs/'.$className.'.php', $data);
        chmod(dirname(__DIR__).'/Src/API/DAOs/'.$className.'.php', 0777);
    }
}
