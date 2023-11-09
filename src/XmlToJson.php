<?php

namespace Onur\Xml2json;

class XmlToJson
{
    /**
     * Convert XML to an associative array.
     *
     * @param \SimpleXMLElement $xml      The XML element to convert.
     * @param array             $options  Options for the conversion process.
     *
     * @return array The resulting associative array.
     */
    public function xmlToArray($xml, $options = [])
    {
        // Default options for XML to array conversion
        $defaults = [
            'namespaceRecursive' => true,
            'removeNamespace' => false,
            'namespaceSeparator' => ':',
            'attributePrefix' => '@',
            'alwaysArray' => [],
            'autoArray' => true,
            'textContent' => '$',
            'autoText' => true,
            'keySearch' => false,
            'keyReplace' => false,
        ];

        $options = array_merge($defaults, $options);
        $namespaces = $xml->getDocNamespaces($options['namespaceRecursive']);
        $namespaces[''] = null; // add base (empty) namespace

        // Attributes from all namespaces
        $attributesArray = [];

        foreach ($namespaces as $prefix => $namespace) {
            if ($options['removeNamespace']) {
                $prefix = '';
            }
            foreach ($xml->attributes($namespace) as $attributeName => $attribute) {
                // Replace characters in attribute name if specified
                if ($options['keySearch']) {
                    $attributeName = str_replace($options['keySearch'], $options['keyReplace'], $attributeName);
                }
                $attributeKey = $options['attributePrefix'] . ($prefix ? $prefix . $options['namespaceSeparator'] : '') . $attributeName;
                $attributesArray[$attributeKey] = (string)$attribute;
            }
        }

        // Child nodes from all namespaces
        $tagsArray = [];

        foreach ($namespaces as $prefix => $namespace) {
            if ($options['removeNamespace']) {
                $prefix = '';
            }

            foreach ($xml->children($namespace) as $childXml) {
                // Recursively convert child nodes
                $childArray = $this->xmlToArray($childXml, $options);
                $childTagName = key($childArray);
                $childProperties = current($childArray);

                // Replace characters in tag name if specified
                if ($options['keySearch']) {
                    $childTagName = str_replace($options['keySearch'], $options['keyReplace'], $childTagName);
                }

                // Add namespace prefix if present
                if ($prefix) {
                    $childTagName = $prefix . $options['namespaceSeparator'] . $childTagName;
                }

                if (!isset($tagsArray[$childTagName])) {
                    // Only entry with this key
                    // Test if tags of this type should always be arrays, no matter the element count
                    $tagsArray[$childTagName] =
                        in_array($childTagName, $options['alwaysArray'], true) || !$options['autoArray']
                            ? array($childProperties) : $childProperties;
                } elseif (
                    is_array($tagsArray[$childTagName]) && array_keys($tagsArray[$childTagName])
                    === range(0, count($tagsArray[$childTagName]) - 1)
                ) {
                    // Key already exists and is integer indexed array
                    $tagsArray[$childTagName][] = $childProperties;
                } else {
                    // Key exists, so convert to integer indexed array with previous value in position 0
                    $tagsArray[$childTagName] = array($tagsArray[$childTagName], $childProperties);
                }
            }
        }

        // Text content of node
        $textContentArray = [];
        $plainText = trim((string)$xml);
        if ($plainText !== '') {
            $textContentArray[$options['textContent']] = $plainText;
        }

        // Combine attributes, child nodes, and text content
        $propertiesArray = !$options['autoText'] || $attributesArray || $tagsArray || ($plainText === '')
            ? array_merge($attributesArray, $tagsArray, $textContentArray) : $plainText;

        // Return node as array
        return [
            $xml->getName() => $propertiesArray,
        ];
    }

    /**
     * Convert XML to JSON.
     *
     * @param \SimpleXMLElement $xml The XML element to convert.
     *
     * @return string The resulting JSON string.
     */
    public function xmlToJson($xml)
    {
        $arrayData = $this->xmlToArray($xml);
        return json_encode($arrayData, JSON_PRETTY_PRINT);
    }
}

// End of the XmlToJson class
