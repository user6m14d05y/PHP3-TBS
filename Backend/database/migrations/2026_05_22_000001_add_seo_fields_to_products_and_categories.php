<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('seo_title', 70)->nullable()->after('description');
            $table->string('meta_description', 170)->nullable()->after('seo_title');
            $table->string('focus_keyword', 120)->nullable()->after('meta_description');
            $table->string('image_alt', 160)->nullable()->after('thumbnail');
        });

        Schema::table('category', function (Blueprint $table) {
            $table->string('slug', 90)->nullable()->after('name');
            $table->string('seo_title', 70)->nullable()->after('img');
            $table->string('meta_description', 170)->nullable()->after('seo_title');
            $table->text('seo_content')->nullable()->after('meta_description');
        });

        Schema::table('category_item', function (Blueprint $table) {
            $table->string('slug', 100)->nullable()->after('name');
            $table->string('seo_title', 70)->nullable()->after('slug');
            $table->string('meta_description', 170)->nullable()->after('seo_title');
            $table->text('seo_content')->nullable()->after('meta_description');
        });

        $this->backfillSlugs('category', 80);
        $this->backfillSlugs('category_item', 90);

        Schema::table('category', function (Blueprint $table) {
            $table->unique('slug', 'idx_category_slug_unique');
        });

        Schema::table('category_item', function (Blueprint $table) {
            $table->unique('slug', 'idx_category_item_slug_unique');
        });
    }

    public function down(): void
    {
        Schema::table('category_item', function (Blueprint $table) {
            $table->dropUnique('idx_category_item_slug_unique');
            $table->dropColumn(['slug', 'seo_title', 'meta_description', 'seo_content']);
        });

        Schema::table('category', function (Blueprint $table) {
            $table->dropUnique('idx_category_slug_unique');
            $table->dropColumn(['slug', 'seo_title', 'meta_description', 'seo_content']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['seo_title', 'meta_description', 'focus_keyword', 'image_alt']);
        });
    }

    private function backfillSlugs(string $table, int $maxLength): void
    {
        $usedSlugs = [];

        DB::table($table)->select('id', 'name')->orderBy('id')->chunkById(100, function ($rows) use ($table, $maxLength, &$usedSlugs) {
            foreach ($rows as $row) {
                $base = Str::slug((string) $row->name);
                $fallbackPrefix = $table === 'category_item' ? 'category-item' : $table;
                $base = $base !== '' ? $base : $fallbackPrefix . '-' . $row->id;
                $base = substr($base, 0, $maxLength);

                $slug = $base;
                $counter = 2;

                while (isset($usedSlugs[$slug])) {
                    $suffix = '-' . $counter++;
                    $slug = substr($base, 0, max(1, $maxLength - strlen($suffix))) . $suffix;
                }

                $usedSlugs[$slug] = true;
                DB::table($table)->where('id', $row->id)->update(['slug' => $slug]);
            }
        });
    }
};
