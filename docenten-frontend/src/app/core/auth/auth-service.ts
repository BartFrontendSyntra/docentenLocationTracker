import { Injectable, signal, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { firstValueFrom } from 'rxjs';


@Injectable({
  providedIn: 'root',
})



export class AuthService {
  private http = inject(HttpClient);

  currentUser = signal<User | null>(null);
  private apiUrl = 'http://127.0.0.1:8000/api';

  async login(credentials: { login: string; password: string }): Promise<LoginResponse> {

    const response = await firstValueFrom(
      this.http.post<LoginResponse>(`${this.apiUrl}/login`, credentials)
    );

    localStorage.setItem('access_token', response.access_token);
    this.currentUser.set(response.user);

    return response;
  }

  logout() {
    localStorage.removeItem('access_token');
    this.currentUser.set(null);
  }
}

export interface User {
  id: number;
  username: string;
  email: string;
  role_id: number;
}

export interface LoginResponse {
  message: string;
  access_token: string;
  token_type: string;
  user: User;
}
