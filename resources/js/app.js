import 'overlayscrollbars';
import '@popperjs/core';

// import 'bootstrap';
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

import 'apexcharts';
import 'admin-lte';

// Importe o módulo principal do OverlayScrollbars
import { OverlayScrollbars } from 'overlayscrollbars';

// Aguarde o DOM carregar para garantir que a sidebar já existe na tela
document.addEventListener("DOMContentLoaded", function() {
    const sidebarWrapper = document.querySelector(".sidebar-wrapper");

    // Se o wrapper existir, inicializa o plugin com as configurações padrão do AdminLTE
    if (sidebarWrapper) {
        OverlayScrollbars(sidebarWrapper, {
            scrollbars: {
                theme: "os-theme-light", // Use 'os-theme-dark' se o seu tema for escuro
                autoHide: "leave",
                clickScroll: true,
            },
        });
    }
});
