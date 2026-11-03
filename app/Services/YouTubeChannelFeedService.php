<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class YouTubeChannelFeedService
{
    private const CHANNEL_ID = 'UCx8c9O9qP3IjtnEKkEr-Bng';
    private const ATOM_NS = 'http://www.w3.org/2005/Atom';
    private const YOUTUBE_NS = 'http://www.youtube.com/xml/schemas/2015';
    private const MEDIA_NS = 'http://search.yahoo.com/mrss/';

    /**
     * Recupera los videos públicos recientes del canal mediante el feed Atom oficial.
     * No requiere API key de YouTube.
     */
    public function latestVideos(int $limit = 12)
    {
        return Cache::remember('uhtv_youtube_channel_feed_v2', 600, function () use ($limit) {
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

                $entries = $xml->children(self::ATOM_NS)->entry;

                return collect($entries ?: [])
                    ->take(max(1, min($limit, 20)))
                    ->map(function ($entry) {
                        $atom = $entry->children(self::ATOM_NS);
                        $youtube = $entry->children(self::YOUTUBE_NS);
                        $videoId = trim((string) ($youtube->videoId ?? ''));

                        if ($videoId === '') {
                            return null;
                        }

                        $mediaGroup = $entry->children(self::MEDIA_NS)->group;
                        $thumbnail = null;

                        if ($mediaGroup) {
                            $thumbnailNode = $mediaGroup->children(self::MEDIA_NS)->thumbnail;
                            if ($thumbnailNode) {
                                $thumbnail = (string) $thumbnailNode->attributes()->url;
                            }
                        }

                        $published = trim((string) ($atom->published ?? ''));
                        $date = $published !== '' ? Carbon::parse($published) : now();
                        $url = 'https://www.youtube.com/watch?v=' . $videoId;

                        return (object) [
                            'youtube_id' => $videoId,
                            'titulo' => trim((string) ($atom->title ?? 'Video de UHTV')),
                            'descripcion' => '',
                            // Mantener la misma estructura que el modelo Transmision para las vistas Blade.
                            'tipo' => 'clip',
                            'plataforma' => 'youtube',
                            'plataforma_nombre' => 'YouTube',
                            'plataforma_color' => '#ff0000',
                            'plataforma_icon' => 'fab fa-youtube',
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
