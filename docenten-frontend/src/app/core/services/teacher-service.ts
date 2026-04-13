import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { firstValueFrom } from 'rxjs';

@Injectable({
  providedIn: 'root',
})
export class TeacherService {
  private http = inject(HttpClient);
  //TODO: change this to environment variable
  private apiUrl = 'http://127.0.0.1:8000/api/teachers';

  async getAllTeachers(): Promise<Teacher[]> {

    const response: any = await firstValueFrom(this.http.get<Teacher[]>(this.apiUrl));

    return response.data ? response.data : response; 
  }
  async createTeacher(teacherData: Partial<Teacher>): Promise<Teacher> {
    const response: any = await firstValueFrom(this.http.post(this.apiUrl, teacherData));
    return response.data ? response.data : response;
  }

  async updateTeacher(id: number, teacherData: Partial<Teacher>): Promise<Teacher> {
    const response: any = await firstValueFrom(this.http.put(`${this.apiUrl}/${id}`, teacherData));
    return response.data ? response.data : response;
  }

  async deleteTeacher(id: number): Promise<void> {
    await firstValueFrom(this.http.delete(`${this.apiUrl}/${id}`));
  }
}

export interface Address {
  id: number;
  street: string;
  house_number: string;
  location_data: {
    lat: number;
    lng: number;
  };
  city: City;
}
export interface City{
  id: number;
  name: string;
  postal_code: string;
}

export interface Course {
  id: number;
  name: string;
}

export interface Certificate {
  id: number;
  name: string;
}

export interface Teacher {
id: number;
  first_name: string;
  last_name: string;
  email: string;
  company_number: string;

  contact: {
    telephone: string | null;
    cellphone: string | null;
  };

  address ?: Address;
  courses ?: Course[];
  certificates ?: Certificate[];
}
