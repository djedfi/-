<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use \App\Models\State;

class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'state_id'      =>  rand(1,50),
            'customer_id'   =>  $this->generateRandomString(9),
            'first_name'    =>  $this->faker->name(),
            'last_name'     =>  $this->faker->lastName(),
            'address_p'     =>  $this->faker->address(),
            'city'          =>  $this->faker->city(),
            'zip'           =>  $this->faker->postcode(),
            'cellphone'     =>  str_replace(array('(',')',' ','-','.'),'',$this->faker->tollFreePhoneNumber()),
            'gender'        =>  rand(1,3)
        ];
    }

    private function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
