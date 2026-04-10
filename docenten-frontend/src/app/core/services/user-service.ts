import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { firstValueFrom } from 'rxjs';

export interface User {
  id: number;
  name: string;
  email: string;
  role: string;
  created_at?: string;
}

@Injectable({
  providedIn: 'root'
})
export class UserService {
  private http = inject(HttpClient);
  private apiUrl = 'http://127.0.0.1:8000/api/users';

  async getAllUsers(): Promise<User[]> {
    const response: any = await firstValueFrom(this.http.get(this.apiUrl));
    return response.data ? response.data : response; 
  }

  async createUser(userData: Partial<User>): Promise<User> {
    const response: any = await firstValueFrom(this.http.post(this.apiUrl, userData));
    return response.data ? response.data : response;
  }

  async updateUser(id: number, userData: Partial<User>): Promise<User> {
    const response: any = await firstValueFrom(this.http.put(`${this.apiUrl}/${id}`, userData));
    return response.data ? response.data : response;
  }

  async deleteUser(id: number): Promise<void> {
    await firstValueFrom(this.http.delete(`${this.apiUrl}/${id}`));
  }
}
