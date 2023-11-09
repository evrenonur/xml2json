# XML to JSON Converter
This PHP library allows you to convert XML data to JSON format using a simple and flexible approach. It provides a class, `XmlToJson`, with methods for converting XML to an associative array and then to JSON. ## Installation You can install the library via Composer. If you don't have Composer installed, you can [download it here](https://getcomposer.org/). `

     composer require evrenonur/xml2json


## Usage

Include the Composer autoloader

    require_once 'vendor/autoload.php';

## **Use the `XmlToJson` class:**
```php
    use  Onur\Xml2json\XmlToJson;
    
    // Load XML from a file  
    $xmlNode = simplexml_load_file('path/to/your/file.xml');
    
    // Create an instance of XmlToJson
    $xmlToJson = new  XmlToJson();
      
    // Convert XML to JSON and echo the result  
    echo  $xmlToJson->xmlToJson($xmlNode);
```
Replace `'path/to/your/file.xml'` with the path to your XML file.

## Options

The `xmlToArray` method of the `XmlToJson` class accepts an optional `$options` parameter to customize the conversion process. You can modify the default options by passing an associative array of options.
```php
    $options = [ 
    'namespaceRecursive' => true,
    'removeNamespace' => false,
      // ... other options
     ]; 
     
  $xmlArray = $xmlToJson->xmlToArray($xmlNode, $options);
```

For a complete list of available options, refer to the [`XmlToJson` class source code](https://github.com/evrenonur/xml2json/blob/master/src/XmlToJson.php).

##  License
This library is licensed under the MIT License

