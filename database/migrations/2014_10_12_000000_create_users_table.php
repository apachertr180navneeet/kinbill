<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('full_name');
            $table->string('slug');
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->string('password');
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->enum('role',['admin','user','vendor','customer'])->default('user');
            $table->string('address')->default(Null);
            $table->string('area')->default(Null);
            $table->string('city')->default(Null);
            $table->string('state')->default(Null);
            $table->string('country');
            $table->integer('country_code')->default(61);
            $table->string('zipcode')->default(Null);
            $table->string('latitude')->default(Null);
            $table->string('longitude')->default(Null);
            $table->string('timezone')->default('Australia/Sydney');
            // $table->text('avatar')->default(Null);
            // $table->text('bio')->default(Null);
            $table->string('device_token')->default(Null);
            $table->enum('device_type',['android','ios'])->default('ios');
            $table->enum('status',['active','inactive'])->default('active');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
