<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('adresses_livraison', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laboratoire_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cabinet_id')->constrained()->cascadeOnDelete();

            // « Cabinet principal », « Antenne de Villeurbanne »…
            $table->string('libelle');

            $table->string('adresse_ligne_1');
            $table->string('adresse_ligne_2')->nullable();
            $table->string('code_postal', 10);
            $table->string('ville');
            $table->string('pays', 2)->default('FR');

            $table->string('contact')->nullable();
            $table->string('telephone', 30)->nullable();
            $table->text('instructions')->nullable();

            $table->boolean('est_principale')->default(false);

            $table->timestamp('archive_le')->nullable();
            $table->timestamps();

            $table->index(['laboratoire_id', 'cabinet_id', 'archive_le']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adresses_livraison');
    }
};
