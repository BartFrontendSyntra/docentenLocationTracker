import { inject } from '@angular/core';
import { CanActivateFn, Router } from '@angular/router';
import { AuthService } from '@core/auth/auth-service';

export const viewerGuard: CanActivateFn = () => {
  const authService = inject(AuthService);
  const router = inject(Router);
  const user = authService.getCurrentUser();
  const currentUser = user();

  if (!currentUser) {
    return router.parseUrl('/login');
  }

  if (currentUser.role === 'Viewer') {
    return true;
  }

  return router.parseUrl('/unauthorized');
};
