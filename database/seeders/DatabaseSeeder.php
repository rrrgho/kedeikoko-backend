<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Reseller;
use App\Models\Customer;
use App\Models\BankAccount;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Administrator Dummy
        User::insert([
            'name' => 'Rian Gho',
            'email' => 'rianiregho@gmail.com',
            'password' => bcrypt('Torpedo78!!'),
        ]);

        // Bank Account
        $bank_account = ['1060014056595', '1951212631'];
        $name = ['Kedei Koko', 'Rian Iregho'];
        for($i=0; $i<count($bank_account); $i++){
            BankAccount::create([
                'account_number' => $bank_account[$i],
                'holder_name' => $name[$i],
                'bank_name' => $i == 0 ? 'Bank Mandiri' : 'Bank Central Indonesia',
            ]);
        }
    }
}
