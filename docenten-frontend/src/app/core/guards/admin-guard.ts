import { CanActivateFn, Router } from '@angular/router';
import { inject } from '@angular/core';
import { AuthService } from '@core/auth/auth-service';

export const adminGuard: CanActivateFn = async (route, state) => {
    const authService = inject(AuthService);
    const router = inject(Router);

    let user = authService.getCurrentUser();

    if (!user() && localStorage.getItem('access_token')) {
      await authService.fetchProfile();
    }


    if (!user()) {
      return router.parseUrl('/login');
    }

    if (user()!.role === 'Administrator') {
      return true;
    }

    return router.parseUrl('/unauthorized');

};
