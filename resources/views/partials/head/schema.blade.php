    <!-- Datos Estructurados Schema.org (Google NewsMediaOrganization & WebSite) -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@graph": [
        {
          "@type": "NewsMediaOrganization",
          "@id": "{{ url('/') }}/#organization",
          "name": "Última Hora TV",
          "url": "{{ url('/') }}",
          "logo": {
            "@type": "ImageObject",
            "url": "{{ asset('images/Logo.jpg') }}"
          },
          "sameAs": [
            "https://www.facebook.com/ultimahoratvbolivia",
            "https://www.youtube.com/@UHTVBolivia",
            "https://tiktok.com/@uhtvbolivia"
          ]
        },
        {
          "@type": "WebSite",
          "@id": "{{ url('/') }}/#website",
          "url": "{{ url('/') }}",
          "name": "Última Hora TV",
          "publisher": { "@id": "{{ url('/') }}/#organization" },
          "potentialAction": {
            "@type": "SearchAction",
            "target": "{{ url('/buscar') }}?q={search_term_string}",
            "query-input": "required name=search_term_string"
          }
        }
      ]
    }
    </script>
