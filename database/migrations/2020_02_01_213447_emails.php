<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use App\Models\Email;

class Emails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(Email::COLLECTION, function ($collection) {
            // Fields
            $collection->string('type');
            $collection->string('user_id');
            $collection->string('status');
            $collection->dateTime('created_at');
            $collection->dateTime('updated_at');
            // Indexes
            $collection->index(['status', 'updated_at' => 1]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(Email::COLLECTION);
    }
}
