<?php

namespace App\Services;

/**
 * Fuente única de verdad sobre las ubicaciones de banners del sitio.
 *
 * Cada entrada describe dónde se renderiza realmente el banner (archivo y línea),
 * las dimensiones que impone el contenedor en producción y la estructura de la
 * maquetación donde se inserta, para que el administrador sepa exactamente dónde
 * aparecerá su banner antes de guardarlo.
 */
class BannerLocationService
{
    /**
     * Definición de todas las ubicaciones soportadas.
     *
     * El array "mockup" describe la maquetación donde se inserta el banner.
     * Cada elemento es una fila y admite estos tipos:
     *   - bar:    franja horizontal de la página
     *   - split:  dos columnas, cada una con su propia lista de filas
     *
     * Las filas de tipo "banner" llevan la clave de ubicación, que es la que
     * el mockup resalta.
     *
     * @var array<string, array<string, mixed>>|null
     */
    protected static ?array $locations = null;

    /**
     * Todas las ubicaciones indexadas por clave.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function all(): array
    {
        return self::$locations ??= [

            'portada_top' => [
                'label' => 'Header Principal',
                'select_label' => 'Portada - Entre el Header y el Nav (100% ancho)',
                'color' => 'primary',
                'icon' => 'fa-image',
                'description' => 'Franja a todo el ancho entre el header y el menú de navegación. Si hay varias, solo se muestra la primera.',
                'dimensions' => '970 x 120 px (el contenedor recorta a 132 px de alto)',
                'rendered_in' => 'resources/views/partials/header/navbar.blade.php',
                'pages' => 'Todas las páginas del sitio',
                'notes' => 'La imagen se centra con object-fit: contain. Si la subes muy alta verás espacio vacío a los lados.',
                'mockup' => [
                    ['type' => 'bar', 'label' => 'Header UHTV (logo, fecha, clima)'],
                    ['type' => 'banner', 'key' => 'portada_top'],
                    ['type' => 'bar', 'label' => 'Menú de navegación'],
                    ['type' => 'content', 'label' => 'Contenido de la página'],
                    ['type' => 'bar', 'label' => 'Pie de página'],
                ],
            ],

            'portada_middle' => [
                'label' => 'Portada Medio',
                'select_label' => 'Portada - Entre "Explora Todas las Secciones" y "Últimas Noticias"',
                'color' => 'warning',
                'icon' => 'fa-image',
                'description' => 'Banner horizontal ancho ubicado entre la sección "Explora Todas las Secciones" y "Últimas Noticias".',
                'dimensions' => '1200 x 250 px (máximo 1024 px de ancho real)',
                'rendered_in' => 'resources/views/portada.blade.php',
                'pages' => 'Solo la portada',
                'notes' => 'Ubicado estratégicamente después de "Explora Todas las Secciones" y antes de "Últimas Noticias". Si no hay banner configurado, se muestra el banner institucional por defecto.',
                'mockup' => [
                    ['type' => 'bar', 'label' => 'Header UHTV'],
                    ['type' => 'bar', 'label' => 'Menú de navegación'],
                    ['type' => 'content', 'label' => 'Hero: carrusel + más leídas'],
                    ['type' => 'bar', 'label' => 'Cintillo de últimas noticias'],
                    ['type' => 'content', 'label' => 'Noticias por categorías'],
                    ['type' => 'content', 'label' => 'Explora todas las secciones'],
                    ['type' => 'banner', 'key' => 'portada_middle'],
                    ['type' => 'content', 'label' => 'Últimas noticias'],
                    ['type' => 'content', 'label' => 'Videos UHTV'],
                    ['type' => 'bar', 'label' => 'Pie de página'],
                ],
            ],

            'sidebar' => [
                'label' => 'Sidebar Derecho',
                'select_label' => 'Portada - Debajo de "Más Leídas" (Sidebar)',
                'color' => 'info',
                'icon' => 'fa-sidebar',
                'description' => 'Tarjeta vertical en la columna derecha de la portada, justo debajo del bloque "Más leídas".',
                'dimensions' => '300 x 250 px o 300 x 600 px (vertical)',
                'rendered_in' => 'resources/views/portada.blade.php',
                'pages' => 'Solo la portada',
                'notes' => 'Lleva su propio encabezado gris "Publicidad". En móvil la columna se apila y el banner queda debajo de todo el contenido.',
                'mockup' => [
                    ['type' => 'bar', 'label' => 'Header UHTV'],
                    ['type' => 'bar', 'label' => 'Menú de navegación'],
                    ['type' => 'split', 'main' => [
                        ['type' => 'content', 'label' => 'Carrusel de noticias'],
                        ['type' => 'content', 'label' => 'Noticias por categorías'],
                    ], 'side' => [
                        ['type' => 'content', 'label' => 'Más leídas'],
                        ['type' => 'banner', 'key' => 'sidebar'],
                    ]],
                    ['type' => 'content', 'label' => 'Últimas noticias'],
                    ['type' => 'bar', 'label' => 'Pie de página'],
                ],
            ],

            'footer' => [
                'label' => 'Footer Horizontal',
                'select_label' => 'Pie de Página (Footer)',
                'color' => 'success',
                'icon' => 'fa-rectangle-ad',
                'description' => 'Banner ancho situado encima del pie de página, en todas las páginas del sitio.',
                'dimensions' => '1200 x 250 px (máximo 1024 px de ancho real)',
                'rendered_in' => 'resources/views/partials/footer/footer.blade.php',
                'pages' => 'Todas las páginas del sitio',
                'notes' => 'La imagen se recorta con object-cover, por lo que una imagen muy apaisada puede quedar cortada arriba y abajo.',
                'mockup' => [
                    ['type' => 'bar', 'label' => 'Header UHTV'],
                    ['type' => 'bar', 'label' => 'Menú de navegación'],
                    ['type' => 'content', 'label' => 'Contenido de la página'],
                    ['type' => 'banner', 'key' => 'footer'],
                    ['type' => 'bar', 'label' => 'Pie de página (footer)'],
                ],
            ],

            'category_top' => [
                'label' => 'Categoría Arriba',
                'select_label' => 'Categoría - Arriba',
                'color' => 'danger',
                'icon' => 'fa-image',
                'description' => 'Banner justo debajo del encabezado de la categoría, antes de la lista de noticias.',
                'dimensions' => '1200 x 250 px (máximo 1024 px de ancho real)',
                'rendered_in' => 'resources/views/categoria/noticias.blade.php',
                'pages' => 'Todas las páginas de categoría',
                'notes' => 'Aparece en la categoría que estés viendo, así que el mismo banner se repetirá en cada una.',
                'mockup' => [
                    ['type' => 'bar', 'label' => 'Header UHTV'],
                    ['type' => 'bar', 'label' => 'Menú de navegación'],
                    ['type' => 'content', 'label' => 'Encabezado de la categoría'],
                    ['type' => 'banner', 'key' => 'category_top'],
                    ['type' => 'content', 'label' => 'Lista de noticias de la categoría'],
                    ['type' => 'bar', 'label' => 'Pie de página'],
                ],
            ],

            'category_bottom' => [
                'label' => 'Categoría Abajo',
                'select_label' => 'Categoría - Abajo',
                'color' => 'secondary',
                'icon' => 'fa-image',
                'description' => 'Banner al final de la categoría, después de la última noticia y antes del pie de página.',
                'dimensions' => '1200 x 250 px (máximo 1024 px de ancho real)',
                'rendered_in' => 'resources/views/categoria/noticias.blade.php',
                'pages' => 'Todas las páginas de categoría',
                'notes' => 'Queda fuera de la última tarjeta de noticias, por lo que no empuja el contenido hacia arriba.',
                'mockup' => [
                    ['type' => 'bar', 'label' => 'Header UHTV'],
                    ['type' => 'bar', 'label' => 'Menú de navegación'],
                    ['type' => 'content', 'label' => 'Encabezado de la categoría'],
                    ['type' => 'content', 'label' => 'Lista de noticias de la categoría'],
                    ['type' => 'banner', 'key' => 'category_bottom'],
                    ['type' => 'bar', 'label' => 'Pie de página'],
                ],
            ],

            'show_top' => [
                'label' => 'Noticia Arriba',
                'select_label' => 'Noticia - Arriba del artículo',
                'color' => 'dark',
                'icon' => 'fa-image',
                'description' => 'Banner entre el encabezado de la noticia y el cuerpo del artículo.',
                'dimensions' => '1200 x 250 px (máximo 1024 px de ancho real)',
                'rendered_in' => 'resources/views/show.blade.php',
                'pages' => 'Todas las páginas de noticia',
                'notes' => 'Se repite en cada noticia que se abra, así que la exposición es alta.',
                'mockup' => [
                    ['type' => 'bar', 'label' => 'Header UHTV'],
                    ['type' => 'bar', 'label' => 'Menú de navegación'],
                    ['type' => 'content', 'label' => 'Categoría, título y foto de la noticia'],
                    ['type' => 'banner', 'key' => 'show_top'],
                    ['type' => 'content', 'label' => 'Cuerpo del artículo'],
                    ['type' => 'bar', 'label' => 'Pie de página'],
                ],
            ],

            'show_bottom' => [
                'label' => 'Noticia Abajo',
                'select_label' => 'Noticia - Al final, antes del footer',
                'color' => 'dark',
                'icon' => 'fa-image',
                'description' => 'Banner al cierre del artículo, después del texto y antes del pie de página.',
                'dimensions' => '1200 x 250 px (máximo 1024 px de ancho real)',
                'rendered_in' => 'resources/views/show.blade.php',
                'pages' => 'Todas las páginas de noticia',
                'notes' => 'Aparece siempre, incluso en noticias muy cortas, porque va fuera del cuerpo del texto.',
                'mockup' => [
                    ['type' => 'bar', 'label' => 'Header UHTV'],
                    ['type' => 'bar', 'label' => 'Menú de navegación'],
                    ['type' => 'content', 'label' => 'Categoría, título y foto de la noticia'],
                    ['type' => 'content', 'label' => 'Cuerpo del artículo'],
                    ['type' => 'banner', 'key' => 'show_bottom'],
                    ['type' => 'bar', 'label' => 'Pie de página'],
                ],
            ],

            'popup' => [
                'label' => 'Popup Portada',
                'select_label' => 'Publicidad Emergente (Popup - Portada)',
                'color' => 'purple',
                'icon' => 'fa-window-restore',
                'description' => 'Ventana emergente a pantalla completa que se abre 1,5 segundos después de cargar la portada.',
                'dimensions' => '800 x 800 px o 1080 x 1080 px (vertical o cuadrado)',
                'rendered_in' => 'resources/views/partials/footer/footer.blade.php',
                'pages' => 'Todas las páginas del sitio',
                'notes' => 'Se muestra solo el primero. Tapa la página con un fondo oscuro y se cierra con la tecla Z o la X roja. La imagen se limita al 70% del alto de la pantalla.',
                'mockup' => [
                    ['type' => 'bar', 'label' => 'Header UHTV'],
                    ['type' => 'bar', 'label' => 'Menú de navegación'],
                    ['type' => 'content', 'label' => 'Portada oscurecida por el overlay'],
                    ['type' => 'overlay', 'key' => 'popup'],
                    ['type' => 'bar', 'label' => 'Pie de página'],
                ],
            ],
        ];
    }

    /**
     * Definición de una ubicación.
     *
     * @return array<string, mixed>|null
     */
    public static function get(?string $key): ?array
    {
        if ($key === null) {
            return null;
        }

        return self::all()[$key] ?? null;
    }

    /**
     * ¿Existe la ubicación indicada?
     */
    public static function exists(?string $key): bool
    {
        return $key !== null && array_key_exists($key, self::all());
    }

    /**
     * Etiqueta legible de la ubicación.
     */
    public static function label(?string $key): string
    {
        return self::get($key)['label'] ?? (string) $key;
    }

    /**
     * Proporción ancho/alto del hueco según las dimensiones recomendadas.
     *
     * Se usa en el mockup del panel para dibujar el banner con la forma que
     * tendrá realmente en el sitio.
     */
    public static function mockupRatio(?string $key): float
    {
        return match ($key) {
            'portada_top' => 8.1,   // 970 x 120
            'sidebar' => 1.2,       // 300 x 250
            'popup' => 1.0,         // 800 x 800
            default => 4.8,         // 1200 x 250
        };
    }

    /**
     * Claves de todas las ubicaciones, en orden de definición.
     *
     * @return array<int, string>
     */
    public static function keys(): array
    {
        return array_keys(self::all());
    }

    /**
     * URL que representa la página en la barra de direcciones del mockup.
     */
    public static function mockupUrl(?string $key): string
    {
        return match ($key) {
            'portada_top', 'portada_middle', 'sidebar', 'footer', 'popup' => '/',
            'category_top', 'category_bottom' => '/categoria/politica',
            'show_top', 'show_bottom' => '/noticia/ejemplo-de-noticia',
            default => '/',
        };
    }

    /**
     * URL donde se puede ver esta ubicación en el sitio real.
     *
     * Las ubicaciones de categoría y de noticia dependen de la página concreta
     * que se esté viendo, así que no tienen una URL fija.
     */
    public static function publicUrl(?string $key): ?string
    {
        $portadaKeys = ['portada_top', 'portada_middle', 'sidebar', 'footer', 'popup'];

        return in_array($key, $portadaKeys, true) ? url('/') : null;
    }

    /**
     * Validar que la ubicación recibida por el administrador es una de las conocidas.
     *
     * @return array<int, string>
     */
    public static function validationRule(): string
    {
        return 'in:' . implode(',', self::keys());
    }
}
