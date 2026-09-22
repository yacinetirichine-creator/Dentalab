<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cabinets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laboratoire_id')->constrained()->cascadeOnDelete();

            $table->string('raison_sociale');
            $table->string('siret', 14)->nullable();
            $table->string('numero_tva', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('telephone', 30)->nullable();

            // Adresse de facturation. Les adresses de livraison sont à part :
            // un cabinet peut faire livrer ailleurs, et à plusieurs endroits.
            $table->string('adresse_ligne_1')->nullable();
            $table->string('adresse_ligne_2')->nullable();
            $table->string('code_postal', 10)->nullable();
            $table->string('ville')->nullable();
            $table->string('pays', 2)->default('FR');

            // Délai de règlement en jours. HYPOTHÈSE à confirmer en phase 0 :
            // voir docs/decisions/002-modele-clients.md.
            $table->unsignedSmallInteger('delai_reglement_jours')->default(30);

            $table->text('notes')->nullable();

            // Archivage plutôt que suppression : un cabinet porte un
            // historique de travaux et de factures qui doit être conservé.
            $table->timestamp('archive_le')->nullable();

            $table->timestamps();

            $table->index(['laboratoire_id', 'archive_le']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cabinets');
    }
};
