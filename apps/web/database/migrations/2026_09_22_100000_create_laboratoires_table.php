<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laboratoires', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('siret', 14)->nullable();
            $table->string('ville')->nullable();

            // Préfixe des numéros de bons de travail et de factures, propre au labo
            // (la numérotation légale doit rester continue au sein d'un laboratoire).
            $table->string('prefixe_numerotation', 8)->nullable();

            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laboratoires');
    }
};
