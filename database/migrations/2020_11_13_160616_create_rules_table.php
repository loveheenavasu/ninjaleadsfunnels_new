<?php

use App\Models\Rule;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rules', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->foreignId('connection_id');
            $table->unsignedBigInteger('stage_id')->nullable();
            $table->string('name');
            $table->longText('notes')->nullable();
            $table->string('webhook_split')->nullable();
            $table->string('webhook_id_selected')->nullable();
            $table->string('webhook_send')->nullable();
            $table->string('emailtype')->nullable();
            $table->bigInteger('emails_count');
            $table->boolean('randomize_emails_order')->default(false);
            $table->string('timezone')->default('UTC');
            $table->enum('schedule', Rule::schedules());
            $table->json('schedule_days')->nullable();
            $table->integer('schedule_weekday')->nullable();
            $table->integer('schedule_monthday')->nullable();
            $table->enum('schedule_time', Rule::scheduleTimes());
            $table->integer('schedule_hour')->nullable();
            $table->integer('schedule_hour_from')->nullable();
            $table->integer('schedule_hour_to')->nullable();
            $table->enum('status', Rule::statuses())->default(Rule::STATUS_STOPPED);
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rules');
    }
}
