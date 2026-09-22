<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('praticiens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laboratoire_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cabinet_id')->constrained()->cascadeOnDelete();

            $table->string('civilite', 10)->nullable();
            $table->string('nom');
            $table->string('prenom')->nullable();

            // Répertoire Partagé des Professionnels de Santé : 11 chiffres.
            $table->string('numero_rpps', 11)->nullable();

            $table->string('email')->nullable();
            $table->string('telephone', 30)->nullable();

            $table->timestamp('archive_le')->nullable();
            $table->timestamps();

            $table->index(['laboratoire_id', 'cabinet_id', 'archive_le']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('praticiens');
    }
};
