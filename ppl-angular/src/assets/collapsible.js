document.addEventListener('DOMContentLoaded', function () {
  const MOBILE_MAX_WIDTH = 768;

  function setupToggle(block, label, idPrefix, isMobile) {
    if (!block) return;

    if (!isMobile) {
      block.classList.remove('collapsed');
      const prevToggle = block.previousElementSibling;
      if (prevToggle && prevToggle.classList && prevToggle.classList.contains('collapsible-toggle')) {
        prevToggle.style.display = 'none';
        prevToggle.setAttribute('aria-expanded', 'true');
      }
      return;
    }

    if (block.dataset.collapsibleInit === 'true') {
      const prevToggle = block.previousElementSibling;
      if (prevToggle && prevToggle.classList && prevToggle.classList.contains('collapsible-toggle')) {
        prevToggle.style.display = 'flex';
      }
      block.classList.add('collapsed');
      return;
    }

    // Asignar ID si no existe
    if (!block.id) {
      const rand = Math.random().toString(36).slice(2, 8);
      block.id = `${idPrefix}-${rand}`;
    }

    // Si hay heading previo, usar su texto como etiqueta
    const prevHeading = block.previousElementSibling;
    const headingText = (prevHeading && /^H[1-6]$/.test(prevHeading.tagName))
      ? prevHeading.textContent.trim()
      : label;

    // Crear botón accesible
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'collapsible-toggle';
    btn.setAttribute('aria-controls', block.id);
    btn.setAttribute('aria-expanded', 'false');
    btn.innerHTML = `<span>${headingText}</span> <i class="fas fa-chevron-down chevron" aria-hidden="true"></i>`;

    // Insertar antes del bloque
    block.parentNode.insertBefore(btn, block);

    // Colapsar inicialmente en móvil
    block.classList.add('collapsed');

    // Manejar clic
    btn.addEventListener('click', function () {
      const expanded = btn.getAttribute('aria-expanded') === 'true';
      btn.setAttribute('aria-expanded', expanded ? 'false' : 'true');
      block.classList.toggle('collapsed', expanded);
    });

    // Marcar como inicializado
    block.dataset.collapsibleInit = 'true';
  }

  function initCollapsible() {
    const isMobile = window.innerWidth <= MOBILE_MAX_WIDTH;

    // 1) Redes sociales
    document.querySelectorAll('.social-links').forEach((block) => {
      setupToggle(block, 'Síguenos en redes', 'social-links', isMobile);
    });

    // 2) Enlaces del footer (bloque único)
    document.querySelectorAll('.footer-links > ul').forEach((block) => {
      setupToggle(block, 'Enlaces', 'footer-links', isMobile);
    });

    // 3) Enlaces del footer en columnas (documentos.html)
    document.querySelectorAll('.footer-links-column > ul').forEach((block) => {
      setupToggle(block, 'Enlaces', 'footer-links-column', isMobile);
    });

    // 4) FAQs (si existiese)
    document.querySelectorAll('.faq-items').forEach((block) => {
      setupToggle(block, 'Preguntas frecuentes', 'faq', isMobile);
    });
  }

  initCollapsible();

  // Reaplicar al cambiar tamaño de ventana (debounce simple)
  let resizeTimer;
  window.addEventListener('resize', function () {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(initCollapsible, 200);
  });
});