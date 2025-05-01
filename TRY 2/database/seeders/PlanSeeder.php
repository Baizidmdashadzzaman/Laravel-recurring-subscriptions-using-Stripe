<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
    * Run the database seeds.
    *
    * @return void
    */
   public function run()
   {
       $userPlans = [
           ['uuid'=>Str::uuid()->toString(),'stripe_plan_id'=>'price_1RJpsqP2kqOTkjJTJpHKgaiM','plan_name'=>'Siler Monthly','plan_price'=>99.99,'plan_type'=>1,'status'=>1,'created_at'=>date("Y-m-d H:i:s"),'updated_at'=>date("Y-m-d H:i:s")],
           ['uuid'=>Str::uuid()->toString(),'stripe_plan_id'=>'price_1RJpw4P2kqOTkjJTGMQ5iPFZ','plan_name'=>'Gold Yearly','plan_price'=>399.99,'plan_type'=>2,'status'=>1,'created_at'=>date("Y-m-d H:i:s"),'updated_at'=>date("Y-m-d H:i:s")]
        ];

       if(plan::count() == 0){

            plan::insert($userPlans);
       }
   }


}
