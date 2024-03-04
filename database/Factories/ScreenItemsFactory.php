<?php

namespace Factories\ScreenItemsFactory;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScreenItemsFactory extends Factory
{

    public static function setUp()
    {

    }

    public function definition(): array
    {
        return [
            // Define the structure of your JSON data here
            'json_data' => $this->generateNestedJson(),
        ];
    }

    private function generateNestedJson()
    {
        $label = $this->faker->word;
        $config = $this->generateConfigJson();
        $items = $this->generateItemsJson();

        return [
            'label' => $label,
            'config' => $config,
            'items' => $items,
        ];
    }
    private function generateConfigJson()
    {
        // Generate config JSON here
        return [
            'label' => $this->faker->word,
            'icon' => $this->faker->randomNumber(),
            // Add more config keys as needed
        ];
    }
    private function generateItemsJson($depth = 3)
    {
        if ($depth <= 0) {
            return [];
        }

        $items = [];

        $numItems = $this->faker->numberBetween(1, 5);
        for ($i = 0; $i < $numItems; $i++) {
            $label = $this->faker->word;
            $config = $this->generateConfigJson();
            $subItems = $this->generateItemsJson($depth - 1);

            $items[] = [
                'label' => $label,
                'config' => $config,
                'items' => $subItems,
            ];
        }
        dd($items);
        return $items;
    }

}
