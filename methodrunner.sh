#!/bin/bash

read -rp "Enter class use statement(Ex, use Tests\FormData;): " namespace
read -rp "Enter the method name(Ex, CountryName): " method_name

# Extract the class name from the namespace
class_name=$(echo "$namespace" | grep -oP '(?<=\\)([^\\;]+)(?=;|$)')

# Prepare PHP code
php_code="require 'vendor/autoload.php'; $namespace echo $class_name::$method_name();"

echo -e $'\n'
echo -e "\e[1;31m Here is the output: \e[0m"

# Execute the PHP code
eval "php -r \"$php_code\""

# Display full command
echo -e $'\n'
echo "In case you don't want to input again, run below command"
echo "php -r 'require \"vendor/autoload.php\"; $namespace echo $class_name::$method_name();'"
echo -e $'\n'



