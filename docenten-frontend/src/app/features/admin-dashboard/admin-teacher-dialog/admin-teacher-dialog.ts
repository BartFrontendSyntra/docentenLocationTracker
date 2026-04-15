import { AfterViewInit, Component, inject, OnInit, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { HttpClient } from '@angular/common/http';
import { firstValueFrom } from 'rxjs';
import { Teacher } from '@core/services/teacher-service';
import * as L from 'leaflet';

// Material Imports
import { MatDialogRef, MAT_DIALOG_DATA, MatDialogModule } from '@angular/material/dialog';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatSelectModule } from '@angular/material/select';
import { MatButtonModule } from '@angular/material/button';
import { MatTabsModule } from '@angular/material/tabs';
import { MatIconModule } from '@angular/material/icon';

@Component({
  selector: 'app-admin-teacher-dialog',
  standalone: true,
  imports: [
    CommonModule,
    ReactiveFormsModule,
    MatDialogModule,
    MatFormFieldModule,
    MatInputModule,
    MatSelectModule,
    MatButtonModule,
    MatTabsModule,
    MatIconModule
  ],
  templateUrl: './admin-teacher-dialog.html',
  styleUrls: ['./admin-teacher-dialog.css']
})
export class AdminTeacherDialog implements OnInit, AfterViewInit {
  private fb = inject(FormBuilder);
  private http = inject(HttpClient);
  private dialogRef = inject(MatDialogRef<AdminTeacherDialog>);
  public incomingTeacher: Teacher | undefined = inject(MAT_DIALOG_DATA);

  private map!: L.Map;
  private marker!: L.Marker;
  teacherForm!: FormGroup;
  isEditMode = false;

  availableCourses = signal<any[]>([]);
  availableCertificates = signal<any[]>([]);

  async ngOnInit() {
    this.isEditMode = !!this.incomingTeacher;


    const existingCourseIds = this.incomingTeacher?.courses?.map(c => c.id) || [];
    const existingCertIds = this.incomingTeacher?.certificates?.map(c => c.id) || [];


    this.teacherForm = this.fb.group({
      first_name: [this.incomingTeacher?.first_name || '', Validators.required],
      last_name: [this.incomingTeacher?.last_name || '', Validators.required],
      email: [this.incomingTeacher?.email || '', [Validators.required, Validators.email]],
      company_number: [this.incomingTeacher?.company_number || ''],
      telephone: [this.incomingTeacher?.contact?.telephone || ''],
      cellphone: [this.incomingTeacher?.contact?.cellphone || ''],
      street: [this.incomingTeacher?.address?.street || ''],
      house_number: [this.incomingTeacher?.address?.house_number || ''],
      city: [this.incomingTeacher?.address?.city?.name || ''],
      postal_code: [this.incomingTeacher?.address?.city?.postal_code || ''],
      course_ids: [existingCourseIds],
      certificate_ids: [existingCertIds],
      lat: [this.incomingTeacher?.address?.location_data?.lat || null],
      lng: [this.incomingTeacher?.address?.location_data?.lng || null],
    });

    await this.loadDropdownData();
  }

  ngAfterViewInit() {
    setTimeout(() => {
      this.initMap();
    }, 200);
  }

  private initMap() {
    const startLat = this.teacherForm.value.lat || 51.06; //if no lat/lng yet, default to the Oudsberg, the highest sand dune in Flanders!
    const startLng = this.teacherForm.value.lng || 5.61;

    this.map = L.map('admin-location-map').setView([startLat, startLng], 12);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap'
    }).addTo(this.map);

    // Create a draggable marker
    this.marker = L.marker([startLat, startLng], { draggable: true }).addTo(this.map);

    // If the admin drags the pin, update the form's hidden lat/lng fields!
    this.marker.on('dragend', () => {
      const position = this.marker.getLatLng();
      this.teacherForm.patchValue({
        lat: position.lat,
        lng: position.lng
      });
    });
  }


  async geocodeAddress() {
    const vals = this.teacherForm.value;
    const query = `${vals.house_number} ${vals.street}, ${vals.postal_code} ${vals.city}`;

    if (!vals.street || !vals.city) {
      alert('Gelieve eerst een straat en stad in te vullen.');
      return;
    }

    try {

      const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`);
      const data = await response.json();

      if (data && data.length > 0) {

        const lat = parseFloat(data[0].lat);
        const lng = parseFloat(data[0].lon); // Nominatim uses 'lon', not 'lng'


        this.teacherForm.patchValue({ lat, lng });

        this.map.flyTo([lat, lng], 16);
        this.marker.setLatLng([lat, lng]);
      } else {
        alert('Kan adres niet vinden, gelieve het manueel te pinnen op de map');
      }
    } catch (error) {
      console.error('Geocoding failed', error);
    }
  }

  onTabChange() {
    setTimeout(() => {
      if (this.map) {
        this.map.invalidateSize();
      }
    }, 100);
  }

  async loadDropdownData() {
    try {

      const coursesRes: any = await firstValueFrom(this.http.get('http://127.0.0.1:8000/api/courses'));
      const certsRes: any = await firstValueFrom(this.http.get('http://127.0.0.1:8000/api/certificates'));

      this.availableCourses.set(coursesRes.data || coursesRes);
      this.availableCertificates.set(certsRes.data || certsRes);
    } catch (error) {
      console.error('Could not load dropdown data', error);
    }
  }

  onSubmit() {
    if (this.teacherForm.valid) {

      const rawData = this.teacherForm.value;

      const formattedData = {
        first_name: rawData.first_name,
        last_name: rawData.last_name,
        email: rawData.email,
        company_number: rawData.company_number,
        contact: {
          telephone: rawData.telephone,
          cellphone: rawData.cellphone
        },
        address: {
          street: rawData.street,
          house_number: rawData.house_number,
          city: rawData.city,
          postal_code: rawData.postal_code,
          location_data: {
          lat: rawData.lat,
          lng: rawData.lng
        }
        },
        course_ids: rawData.course_ids,
        certificate_ids: rawData.certificate_ids
      };

      console.log(formattedData);

      this.dialogRef.close(formattedData);
    }
  }
}
