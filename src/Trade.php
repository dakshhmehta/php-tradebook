<?php

namespace Dakshhmehta\PhpTradebook;

class Trade
{
    private $properties = []; // Array to store dynamic properties

    public function __construct($data)
    {
        foreach ($data as $key => $val) {
            $this->properties[$key] = $val;
        }
    }

    public function __get($name)
    {
        // Check if the property exists in the array
        if (array_key_exists($name, $this->properties)) {
            return $this->properties[$name];
        }

        // Optionally, throw an exception or return null if the property doesn't exist
        throw new \Exception("Property '$name' does not exist.");
    }

    public function __set($name, $value)
    {
        // Store the property value in the array
        $this->properties[$name] = $value;
    }
}
