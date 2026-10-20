@once
<style>
    /* ============================================================
       BANNERES PUBLICITARIOS - Tamaño unificado en móvil
       ============================================================ */

    /* Contenedor del banner: altura fija/máxima consistente en móvil */
    .uhtv-banner-slot {
        --uhtv-banner-max-height: 420px;
        width: 100%;
        max-height: min(80vh, var(--uhtv-banner-max-height));
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8fafc;
        background: var(--uhtv-banner-bg, #f8fafc);
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
        overflow: hidden;
    }

    .dark .uhtv-banner-slot {
        background: #0f172a;
        border-color: #334155;
    }

    @media (min-width: 768px) {
        .uhtv-banner-slot {
            --uhtv-banner-max-height: 600px;
        }
    }

    /* Imagen del banner: nunca se corta, nunca se distorsiona, nunca pixelada */
    .uhtv-banner-img {
        width: auto;
        height: auto;
        max-width: 100%;
        max-height: min(80vh, var(--uhtv-banner-max-height, 420px));
        object-fit: contain;
        display: block;
        border-radius: 0.5rem;
    }

    /* Lugar reservado cuando no hay imagen cargada aún */
    .uhtv-banner-slot::before {
        content: "";
        display: block;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%);
        background: var(--uhtv-banner-placeholder-bg, linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%));
        mask-image: radial-gradient(circle at center, black 60%, transparent 70%);
        -webkit-mask-image: radial-gradient(circle at center, black 60%, transparent 70%);
        opacity: 0.4;
    }

    /* Cuando la imagen ya cargó, ocultar el placeholder */
    .uhtv-banner-slot img.uhtv-banner-img ~ .uhtv-banner-slot::before { display: none; }
    .uhtv-banner-slot.loaded::before { display: none; }

    /* Footer banner: mismo contenedor unificado */
    .publicidad-footer .uhtv-banner-slot {
        max-width: 100%;
    }

    /* Popup: mantener proporción sin cortar, limitado al 70vh */
    .uhtv-banner-popup-img {
        width: 100%;
        max-height: 70vh;
        object-fit: contain;
        display: block;
    }
</style>
@endonce

<script>
    // Cuando una imagen de banner termina de cargar, ocultamos el placeholder.
    document.querySelectorAll('img.uhtv-banner-img').forEach(function(img){
        if (img.complete) {
            img.closest('.uhtv-banner-slot')?.classList.add('loaded');
        } else {
            img.addEventListener('load', function(){
                img.closest('.uhtv-banner-slot')?.classList.add('loaded');
            });
        }
    });
</script>
