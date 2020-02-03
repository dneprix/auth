<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

class Users extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(User::COLLECTION, function ($collection) {
            // Fields
            $collection->string('email');
            $collection->string('password_hash');
            $collection->string('email_hash');
            $collection->dateTime('created_at');
            $collection->dateTime('updated_at');
            $collection->dateTime('activated_at');

            // Indexes
            $collection->unique('email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists( User::COLLECTION);
    }
}
