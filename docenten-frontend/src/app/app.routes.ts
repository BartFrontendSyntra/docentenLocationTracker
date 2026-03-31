import { Routes } from '@angular/router';
import { Login } from './features/auth/login/login';
import { adminGuard } from '@core/guards/admin-guard';
import { viewerGuard } from '@core/guards/viewer-guard';
import { ViewerDashboard } from './features/viewer-dashboard/viewer-dashboard/viewer-dashboard';

export const routes: Routes = [
  { path: '', redirectTo: 'login', pathMatch: 'full' },
  { path: 'login', component: Login },

  {
    path: 'admin-dashboard',
    // load the admin component
    canActivate: [adminGuard],
  },
  {
    path: 'viewer-dashboard',
    component: ViewerDashboard,
    canActivate: [viewerGuard],

  }

];
