<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->string('pi_intent_id')->nullable();
            $table->unsignedBigInteger('plan_id')->comment('Relation with plan table');
            $table->string('user_name',200)->nullable();
            $table->string('email',200)->nullable();
            $table->string('phone_number',200)->nullable();
            $table->string('current_status')->default(0)->comment('0 cancel,1 active');
            $table->string('subscription_id',200)->nullable();
            $table->unsignedBigInteger('stripe_user_id')->comment('Relation with plan table');
            $table->double('plan_amount',10.2)->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->text('description')->nullable();
            $table->tinyInteger('payment_type')->default(1)->comment('1 for subscription 2 for one time payment 3 for aditional payment');
            $table->timestamps();
            $table->softDeletes();

            //foreign key constraints
            $table->foreign('plan_id')->references('id')->on('plans')->onDelete('cascade');
            $table->foreign('stripe_user_id')->references('id')->on('stripe_users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_subscriptions');
    }
}
