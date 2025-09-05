<?php

use App\Enums\ArticleBoxType;
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
        Schema::create('article_boxes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('epaper_id')->constrained()->onDelete('cascade');
            $table->foreignId('epage_id')->constrained('epaper_pages')->onDelete('cascade');
            $table->foreignId('article_id')->constrained()->onDelete('cascade');
            $table->enum('type', ArticleBoxType::values())->default(ArticleBoxType::TEXT->value);
            $table->json('bounding_box'); // {x,y,width,height}
            $table->string('rasted_image')->nullable();
            $table->longText('extracted_content')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_boxs');
    }
};
