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

    return response.data ? response.data : response; // adjust based on wrapped data message from api or not
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
