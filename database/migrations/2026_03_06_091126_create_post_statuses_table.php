<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('post_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // 'published', 'draft', etc.
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Update posts table to reference status
        Schema::table('posts', function (Blueprint $table) {
            $table->foreignId('post_status_id')
                  ->default(2) // assuming 'draft' will have id = 2 after seeding
                  ->constrained('post_statuses')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete(); // cannot delete status if posts exist
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['post_status_id']);
            $table->dropColumn('post_status_id');
        });

        Schema::dropIfExists('post_statuses');
    }
};
