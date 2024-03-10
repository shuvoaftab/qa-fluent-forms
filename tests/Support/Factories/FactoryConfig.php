<?php
namespace Tests\Support\Factories;

class FactoryConfig
{
   public static function faker(): \Faker\Generator
   {
       return \Faker\Factory::create();
   }

}
