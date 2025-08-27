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
        Schema::create('toilettes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('localisation_id')->constrained()->onDelete('cascade');
            $table->string('image')->nullable();
            $table->string('nom');
            $table->string('horaires');
            $table->enum('etat', ['ouvert','fermé'])->default('ouvert');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('toilettes');
    }
};
