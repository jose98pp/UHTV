<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class YouTubeChannelFeedService
{
    private const CHANNEL_ID = 'UCx8c9O9qP3IjtnEKkEr-Bng';

    /**
     * Recupera los videos públicos recientes del canal mediante el feed Atom oficial.
     * No requiere API key de YouTube.
     */
    public function latestVideos(int $limit = 12)
    {
        return Cache::remember('uhtv_youtube_channel_feed', 600, function () use ($limit) {
            try {
                $response = Http::accept('application/atom+xml')
                    ->withHeaders(['User-Agent' => 'UHTVNewsPortal/1.0'])
                    ->timeout(8)
                    ->get('https://www.youtube.com/feeds/videos.xml', [
                        'channel_id' => self::CHANNEL_ID,
                    ]);

                if (!$response->successful()) {
                    Log::warning('No se pudo consultar el feed de YouTube UHTV', [
                        'status' => $response->status(),
                    ]);
                    return collect();
                }

                $previous = libxml_use_internal_errors(true);
                $xml = simplexml_load_string(
                    $response->body(),
                    \SimpleXMLElement::class,
                    LIBXML_NONET | LIBXML_NOCDATA
                );
                libxml_clear_errors();
                libxml_use_internal_errors($previous);

                if (!$xml) {
                    Log::warning('El feed Atom de YouTube UHTV devolvió XML inválido.');
                    return collect();
                }

                return collect($xml->entry ?? [])
                    ->take(max(1, min($limit, 20)))
                    ->map(function ($entry) {
                        $videoIdNodes = $entry->children('http://www.youtube.com/xml/schemas/2015');
                        $videoId = trim((string) ($videoIdNodes->videoId ?? ''));

                        if ($videoId === '') {
                            return null;
                        }

                        $mediaGroup = $entry->children('http://search.yahoo.com/mrss/')->group;
                        $thumbnail = null;
                        if ($mediaGroup && isset($mediaGroup->thumbnail)) {
                            $thumbnailNode = $mediaGroup->children('http://search.yahoo.com/mrss/')->thumbnail;
                            $thumbnail = (string) $thumbnailNode->attributes()->url;
                        }

                        $published = trim((string) ($entry->published ?? ''));
                        $date = $published !== '' ? Carbon::parse($published) : now();
                        $url = 'https://www.youtube.com/watch?v=' . $videoId;

                        return (object) [
                            'youtube_id' => $videoId,
                            'titulo' => trim((string) ($entry->title ?? 'Video de UHTV')),
                            'descripcion' => '',
                            'url' => $url,
                            'embed_url' => 'https://www.youtube-nocookie.com/embed/' . $videoId . '?rel=0&playsinline=1',
                            'effective_thumbnail' => $thumbnail ?: 'https://img.youtube.com/vi/' . $videoId . '/hqdefault.jpg',
                            'tipo_nombre' => 'Video YouTube',
                            'duracion' => null,
                            'fecha_transmision' => $date,
                            'created_at' => $date,
                            'en_vivo' => false,
                        ];
                    })
                    ->filter()
                    ->values();
            } catch (\Throwable $e) {
                Log::warning('Error al cargar videos recientes del canal UHTV', [
                    'error' => $e->getMessage(),
                ]);

                return collect();
            }
        });
    }

    public function extractVideoId(?string $url): ?string
    {
        if (!$url) {
            return null;
        }

        if (preg_match('~(?:youtu\.be/|youtube(?:-nocookie)?\.com/(?:watch\?v=|embed/|live/|shorts/))([A-Za-z0-9_-]{11})~', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }
}
