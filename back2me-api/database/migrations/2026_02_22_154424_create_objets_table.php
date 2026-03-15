<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::create('objets', function (Blueprint $table) {
         $table->id();
        
        // Utilise exactement cette ligne, c'est la plus compatible avec MySQL 8.4
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        
        $table->foreignId('category_id')->constrained();
        $table->string('name');
        $table->text('description')->nullable();
        $table->string('location');
        $table->date('found_date');
        $table->enum('status', ['found', 'returned', 'unclaimed'])->default('found');
        $table->string('photo_url')->nullable();
        $table->timestamps();

        // On ajoute la contrainte de clé étrangère APRES avoir créé la colonne
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('objets');
    }
};
