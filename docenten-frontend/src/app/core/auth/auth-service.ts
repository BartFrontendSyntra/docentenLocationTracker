import { Injectable, signal, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { firstValueFrom } from 'rxjs';


@Injectable({
  providedIn: 'root',
})



export class AuthService {
  private http = inject(HttpClient);
  private userSignal = signal<User | null>(null);

  currentUser = this.userSignal.asReadonly();

  private apiUrl = 'http://127.0.0.1:8000/api';


  async login(credentials: { login: string; password: string }): Promise<LoginResponse> {

    const response = await firstValueFrom(
      this.http.post<LoginResponse>(`${this.apiUrl}/login`, credentials)
    );

    localStorage.setItem('access_token', response.access_token);
    this.userSignal.set(response.user);
    return response;
  }

  logout() {
    localStorage.removeItem('access_token');
    this.userSignal.set(null);
  }

  async fetchProfile(): Promise<User | null> {
    const token = localStorage.getItem('access_token');

    if (!token) {
      return null;
    }
    try {
      const user = await firstValueFrom(this .http.get<User>(`${this.apiUrl}/user`));
      this.userSignal.set(user);
      return user;

    }
    catch (err) {
      console.error('Error fetching profile:', err);
      this.logout();
      return null;
    }
  }
}

export type RoleName = 'Administrator' | 'Viewer';

export interface User {
  id: number;
  name: string;
  email: string;
  role: RoleName;
}

export interface LoginResponse {
  message: string;
  access_token: string;
  token_type: string;
  user: User;
}
