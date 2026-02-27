import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';

interface Metric {
  title: string;
  status: 'good' | 'warning' | 'bad';
  statusText: string;
  value: string;
  description: string;
  progress: number;
}

@Component({
  selector: 'app-health-monitor',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './health-monitor.html',
  styleUrl: './health-monitor.css'
})
export class HealthMonitorComponent implements OnInit {
  metrics: Metric[] = [];
  lastUpdate: Date = new Date();

  ngOnInit() {
    this.refreshData();
  }

  refreshData() {
    this.metrics = [
      {
        title: 'Tiempo de carga',
        status: 'good',
        statusText: 'Bueno',
        value: (Math.random() * (1.5 - 0.8) + 0.8).toFixed(1) + 's',
        description: 'Tiempo promedio de carga de página',
        progress: 85
      },
      {
        title: 'Puntuación de seguridad',
        status: 'good',
        statusText: 'Seguro',
        value: '92%',
        description: 'Basado en implementaciones de seguridad',
        progress: 92
      },
      {
        title: 'Puntuación SEO',
        status: 'warning',
        statusText: 'Mejorable',
        value: '78%',
        description: 'Optimización para motores de búsqueda',
        progress: 78
      },
      {
        title: 'Tiempo de actividad',
        status: 'good',
        statusText: 'Excelente',
        value: '99.9%',
        description: 'Disponibilidad del sitio en el último mes',
        progress: 99.9
      },
      {
        title: 'Compatibilidad móvil',
        status: 'good',
        statusText: 'Optimizado',
        value: '95%',
        description: 'Adaptabilidad a dispositivos móviles',
        progress: 95
      },
      {
        title: 'Errores detectados',
        status: 'good',
        statusText: 'Mínimo',
        value: '0',
        description: 'Errores JavaScript en las últimas 24h',
        progress: 100
      }
    ];
    this.lastUpdate = new Date();
  }
}
