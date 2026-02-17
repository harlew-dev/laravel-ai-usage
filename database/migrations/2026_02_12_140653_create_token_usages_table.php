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
        Schema::create('token_usages', function (Blueprint $table) {
            $table->id();
            
            $table->string('invocation_id')->index();

            // $table->nullableMorphs('usageable');

            $table->string('type')
                ->index();
            $table->string('agent')
                ->index()
                ->nullable();
            $table->string('provider')
                ->index();
            $table->string('model')
                ->index();

            $table->integer('input_tokens')->nullable();
            $table->integer('output_tokens')->nullable();
            $table->integer('cache_write_tokens')->nullable();
            $table->integer('cache_read_tokens')->nullable();
            $table->integer('reasoning_tokens')->nullable();
            $table->integer('total_tokens')->nullable();

            $table->timestamps();

            $table->index(['provider', 'model']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('token_usages');
    }
};
