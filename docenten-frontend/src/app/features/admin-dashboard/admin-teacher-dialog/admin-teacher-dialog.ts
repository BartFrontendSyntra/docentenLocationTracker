import { Component, inject, OnInit, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { HttpClient } from '@angular/common/http';
import { firstValueFrom } from 'rxjs';
import { Teacher } from '@core/services/teacher-service';

// Material Imports
import { MatDialogRef, MAT_DIALOG_DATA, MatDialogModule } from '@angular/material/dialog';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatSelectModule } from '@angular/material/select';
import { MatButtonModule } from '@angular/material/button';
import { MatTabsModule } from '@angular/material/tabs';

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
    MatTabsModule
  ],
  templateUrl: './admin-teacher-dialog.html',
  styleUrls: ['./admin-teacher-dialog.css']
})
export class AdminTeacherDialog implements OnInit {
  private fb = inject(FormBuilder);
  private http = inject(HttpClient);
  private dialogRef = inject(MatDialogRef<AdminTeacherDialog>);
  public incomingTeacher: Teacher | undefined = inject(MAT_DIALOG_DATA);

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
      course_ids: [existingCourseIds],
      certificate_ids: [existingCertIds]
    });

    await this.loadDropdownData();
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
          house_number: rawData.house_number
        },
        course_ids: rawData.course_ids,
        certificate_ids: rawData.certificate_ids
      };

      this.dialogRef.close(formattedData);
    }
  }
}
