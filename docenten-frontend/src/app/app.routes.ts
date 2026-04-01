import { Routes } from '@angular/router';
import { Login } from './features/auth/login/login';
import { adminGuard } from '@core/guards/admin-guard';
import { viewerGuard } from '@core/guards/viewer-guard';
import { ViewerDashboard } from './features/viewer-dashboard/viewer-dashboard/viewer-dashboard';
import { ViewerMap } from './features/viewer-dashboard/viewer-map/viewer-map';
import { ViewerTeacherList } from './features/viewer-dashboard/viewer-teacher-list/viewer-teacher-list';
import { ViewerStatistics } from './features/viewer-dashboard/viewer-statistics/viewer-statistics';
import { AdminDashboard } from './features/admin-dashboard/admin-dashboard/admin-dashboard';
import { AdminStatistics } from './features/admin-dashboard/admin-statistics/admin-statistics';
import { AdminTeachers } from './features/admin-dashboard/admin-teachers/admin-teachers';
import { AdminUsers } from './features/admin-dashboard/admin-users/admin-users';

export const routes: Routes = [
  { path: '', redirectTo: 'login', pathMatch: 'full' },
  { path: 'login', component: Login },

   {
    path: 'admin-dashboard',
    component: AdminDashboard,
    canActivate: [adminGuard],
    children: [
      { path: '', redirectTo: 'statistics', pathMatch: 'full' },
      { path: 'statistics', component: AdminStatistics },
      { path: 'users', component: AdminUsers },
      { path: 'teachers', component: AdminTeachers }
    ]
  },
  {
    path: 'viewer-dashboard',
    component: ViewerDashboard,
    canActivate: [viewerGuard],
    children: [
      { path: '', redirectTo: 'statistics', pathMatch: 'full' },
      { path: 'statistics', component: ViewerStatistics },
      { path: 'map', component: ViewerMap },
      { path: 'teachers', component: ViewerTeacherList }
    ]

  }

];
