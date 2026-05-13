<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
echo "PHP is working. Version: " . phpversion() . "<br><br>";
echo "Loaded Extensions: <pre>" . implode(", ", get_loaded_extensions()) . "</pre>";
