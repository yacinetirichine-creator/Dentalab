<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Nullable : l'administrateur de la plateforme (§2 du cahier des charges)
            // n'appartient à aucun laboratoire.
            $table->foreignId('laboratoire_id')
                ->nullable()
                ->after('id')
                ->constrained('laboratoires')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('laboratoire_id');
        });
    }
};
