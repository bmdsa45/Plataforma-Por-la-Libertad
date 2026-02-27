document.addEventListener('DOMContentLoaded', () => {
  const httpsStatusEl = document.getElementById('https-status-text');
  const httpsBar = document.getElementById('https-bar');

  const latencyLabel = document.getElementById('latency-label');
  const latencyBar = document.getElementById('latency-bar');

  const availabilityLabel = document.getElementById('availability-label');
  const availabilityBar = document.getElementById('availability-bar');

  const lastCheckEl = document.getElementById('last-check');
  const overallStatusEl = document.getElementById('overall-status');

  const securityLabel = document.getElementById('security-label');
  const securityBar = document.getElementById('security-bar');

  function setBar(barEl, percent) {
    const clamped = Math.max(0, Math.min(100, Math.round(percent)));
    // Mantener la barra (aunque no visible) para accesibilidad
    barEl.style.width = clamped + '%';
    barEl.classList.remove('bar-ok', 'bar-warn', 'bar-bad');
    let statusClass = 'bar-bad';
    if (clamped >= 70) statusClass = 'bar-ok';
    else if (clamped >= 40) statusClass = 'bar-warn';
    barEl.classList.add(statusClass);
    // Actualizar aria-valuenow en el contenedor
    const parent = barEl.parentElement;
    if (parent && parent.getAttribute('role') === 'progressbar') {
      parent.setAttribute('aria-valuenow', String(clamped));
    }
    // Reflejar estado en el contenedor del ítem (ok/warn/bad) para UI minimalista
    const item = barEl.closest('.health-item');
    if (item) {
      item.classList.remove('ok', 'warn', 'bad');
      if (statusClass === 'bar-ok') item.classList.add('ok');
      else if (statusClass === 'bar-warn') item.classList.add('warn');
      else item.classList.add('bad');
    }
  }

  async function measure() {
    // HTTPS status
    const https = window.location.protocol === 'https:';
    if (httpsStatusEl) {
      httpsStatusEl.textContent = https ? 'Activo' : 'No seguro';
    }
    if (httpsBar) setBar(httpsBar, https ? 100 : 40);

    // Availability + Latency (ping a recurso pequeño)
    let latencyMs = null;
    let available = false;
    try {
      const start = performance.now();
      const res = await fetch('ppl.svg?t=' + Date.now(), { cache: 'no-store' });
      available = !!res.ok;
      const end = performance.now();
      latencyMs = Math.round(end - start);
    } catch (_) {
      available = false;
    }

    if (latencyMs !== null) {
      if (latencyLabel) latencyLabel.textContent = `${latencyMs} ms`;
      const percent = Math.max(0, Math.min(100, 100 - (latencyMs / 10))); // 0-1000ms => 100-0%
      if (latencyBar) setBar(latencyBar, percent);
    } else {
      if (latencyLabel) latencyLabel.textContent = 'Sin datos';
      if (latencyBar) setBar(latencyBar, 0);
    }

    if (availabilityLabel) availabilityLabel.textContent = available ? 'Disponible' : 'No disponible';
    if (availabilityBar) setBar(availabilityBar, available ? 100 : 0);

    // Seguridad: escaneo de cabeceras recomendadas
    let securityPercent = null;
    try {
      const res2 = await fetch('index.html?scan=' + Date.now(), { cache: 'no-store' });
      const headers = res2.headers;
      const hasHSTS = !!headers.get('Strict-Transport-Security');
      const hasNosniff = !!headers.get('X-Content-Type-Options');
      const hasCSP = !!(headers.get('Content-Security-Policy') || document.querySelector('meta[http-equiv="Content-Security-Policy"]'));
      const hasReferrer = !!(headers.get('Referrer-Policy') || document.querySelector('meta[name="referrer"]'));
      const totalChecks = 4;
      const hits = [hasHSTS, hasNosniff, hasCSP, hasReferrer].filter(Boolean).length;
      securityPercent = Math.round((hits / totalChecks) * 100);
      if (securityLabel) securityLabel.textContent = `Cabeceras ${hits}/${totalChecks}`;
      if (securityBar) setBar(securityBar, securityPercent);
    } catch (_) {
      if (securityLabel) securityLabel.textContent = 'Sin datos';
      if (securityBar) setBar(securityBar, 0);
      securityPercent = 0;
    }

    // Last check
    if (lastCheckEl) lastCheckEl.textContent = 'Última revisión: ' + new Date().toLocaleString();

    // Overall status (incluye seguridad)
    let overall = 'Crítico';
    const sec = securityPercent ?? 0;
    if (https && available && latencyMs !== null && latencyMs <= 300 && sec >= 75) overall = 'Excelente';
    else if (available && latencyMs !== null && latencyMs <= 800 && sec >= 50) overall = 'Bueno';
    else if (available) overall = 'Atención';
    else overall = 'Crítico';
    if (overallStatusEl) overallStatusEl.textContent = 'Estado general: ' + overall;
  }

  measure();
  setInterval(measure, 30000); // Actualiza cada 30s
});