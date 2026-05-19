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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique(); // e.g., TIX-202602-001
            $table->string('subject');
            $table->text('description');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->string('assigned_to')->nullable(); // Bisa berupa nama atau ID teknisi
            $table->enum('status', ['open', 'in_progress', 'resolved', 'closed'])->default('open');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->timestamp('sla_due_date')->nullable();   // SLA deadline
            $table->timestamp('resolved_at')->nullable();     // Actual resolution time
            $table->timestamp('closed_at')->nullable();
            $table->text('assignment_note')->nullable(); // Catatan saat penugasan
            $table->timestamp('assigned_at')->nullable(); // Waktu penugasan
            $table->timestamps();
            $table->softDeletes();

            // Index untuk performa query
            $table->index(['status', 'priority']);
            $table->index('assigned_to');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
