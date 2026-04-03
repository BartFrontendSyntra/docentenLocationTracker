import { HttpInterceptorFn } from '@angular/common/http';

// TODO: look at possibility of this being optional if we want a different strategy

export const authInterceptor: HttpInterceptorFn = (req, next) => {

  const token = localStorage.getItem('access_token');

  if (token) {
    const authReq = req.clone({
      setHeaders: {
        Authorization: `Bearer ${token}`
      }
    });

    return next(authReq);
  }

  return next(req);
};
