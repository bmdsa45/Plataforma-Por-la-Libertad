import { Routes } from '@angular/router';
import { HomeComponent } from './pages/home/home';
import { AboutComponent } from './pages/about/about';
import { ProposalsComponent } from './pages/proposals/proposals';
import { NewsComponent } from './pages/news/news';
import { DocumentsComponent } from './pages/documents/documents';
import { ContactComponent } from './pages/contact/contact';
import { RegisterComponent } from './pages/register/register';
import { HealthMonitorComponent } from './pages/health-monitor/health-monitor';

export const routes: Routes = [
  { path: '', component: HomeComponent },
  { path: 'inicio', component: HomeComponent },
  { path: 'quienes-somos', component: AboutComponent },
  { path: 'propuestas', component: ProposalsComponent },
  { path: 'noticias', component: NewsComponent },
  { path: 'documentos', component: DocumentsComponent },
  { path: 'contacto', component: ContactComponent },
  { path: 'registro', component: RegisterComponent },
  { path: 'monitor', component: HealthMonitorComponent },
  { path: '**', redirectTo: '' }
];
