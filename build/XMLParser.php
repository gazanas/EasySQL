<?php

namespace Build;

/**
 * XMLParser Class
 *
 * @version 0.1.0
 **/
class XMLParser
{


    /**
     * Returns a string that contains the xml contents
     *
     * @param string $file The path where the xml file is.
     *
     * @return string $xmlContent The contents of the XML file.
     */
    public function parseXML(string $file)
    {
        try {
            $xmlContent = file_get_contents($file);
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        return $xmlContent;
    }


    /**
     * Receives an xml string and returns a SimpleXMLElement object
     *
     * @param string $xml XML String.
     *
     * @return SimpleXMLElement
     */
    public function XMLtoObject(string $xml)
    {
        return new \SimpleXMLElement($xml);
    }
}
