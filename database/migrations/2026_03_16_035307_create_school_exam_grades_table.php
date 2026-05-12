<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_exam_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->string('class_name');
            $table->string('group_name')->nullable();
            $table->string('section_name')->nullable();
            $table->string('subject_name');
            $table->decimal('min_mark', 5, 2);
            $table->decimal('max_mark', 5, 2);
            $table->string('letter_name'); // A+, A, etc.
            $table->decimal('number_point', 4, 2); // 5.00, 4.00, etc.
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_exam_grades');
    }
};