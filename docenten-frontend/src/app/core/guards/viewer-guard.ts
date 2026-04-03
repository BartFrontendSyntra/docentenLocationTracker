import { inject } from '@angular/core';
import { CanActivateFn, Router } from '@angular/router';
import { AuthService } from '@core/auth/auth-service';

export const viewerGuard: CanActivateFn = async(route,state) => {
  const authService = inject(AuthService);
  const router = inject(Router);
  let currentUser = authService.currentUser();

  if (!currentUser && localStorage.getItem('access_token')) {
    currentUser = await authService.fetchProfile();
  }
  
  if(!currentUser) {
    return router.parseUrl('/login');
  }

  if (currentUser.role === 'Viewer') {
    return true;
  }

  return router.parseUrl('/unauthorized');
};
