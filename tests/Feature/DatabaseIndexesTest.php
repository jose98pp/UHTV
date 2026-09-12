<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use App\Models\Noticia;

class DatabaseIndexesTest extends TestCase
{
    /** @test */
    public function noticias_table_has_expected_performance_indexes()
    {
        $indexes = collect(DB::select('SHOW INDEX FROM noticias'))
            ->pluck('Key_name')
            ->unique()
            ->toArray();

        $this->assertContains('noticias_publicada_index', $indexes);
        $this->assertContains('noticias_created_at_index', $indexes);
        $this->assertContains('noticias_category_id_publicada_index', $indexes);
        $this->assertContains('noticias_publicada_created_at_index', $indexes);
        $this->assertContains('noticias_publicada_views_index', $indexes);
    }

    /** @test */
    public function published_news_queries_work_with_indexes()
    {
        $noticias = Noticia::where('publicada', true)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $noticias);
    }
}
