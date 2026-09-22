<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Volontairement nullable : un compte peut exister avant que son
            // rôle soit attribué. Un rôle absent ne donne AUCUN droit — le
            // refus est la valeur par défaut, jamais l'autorisation.
            $table->string('role')->nullable()->after('laboratoire_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
