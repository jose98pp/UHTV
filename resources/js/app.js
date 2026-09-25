/**
 * UHTV — JavaScript Entry Point (Vite)
 *
 * Configuración global de Axios para peticiones AJAX con CSRF de Laravel.
 * Bootstrap se carga desde Vite y se expone globalmente para los scripts
 * heredados que todavía hacen referencia a window.bootstrap.
 */
import * as bootstrap from 'bootstrap';
import './bootstrap.js';

window.bootstrap = bootstrap;
