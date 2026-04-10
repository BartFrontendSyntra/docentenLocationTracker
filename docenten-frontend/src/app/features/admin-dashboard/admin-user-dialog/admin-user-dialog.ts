import { Component, inject, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators } from '@angular/forms';
import { User } from '@core/services/user-service';

// Material Imports
import { MatDialogRef, MAT_DIALOG_DATA, MatDialogModule } from '@angular/material/dialog';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatSelectModule } from '@angular/material/select';
import { MatButtonModule } from '@angular/material/button';

@Component({
  selector: 'app-admin-user-dialog',
  standalone: true,
  imports: [
    CommonModule,
    ReactiveFormsModule,
    MatDialogModule,
    MatFormFieldModule,
    MatInputModule,
    MatSelectModule,
    MatButtonModule
  ],
  templateUrl: './admin-user-dialog.html',
  styleUrls: ['./admin-user-dialog.css']
})
export class AdminUserDialogComponent implements OnInit {
  private fb = inject(FormBuilder);
  private dialogRef = inject(MatDialogRef<AdminUserDialogComponent>);

  // get the data from the users component as an input
  public incomingData: User | undefined = inject(MAT_DIALOG_DATA);

  userForm!: FormGroup;
  isEditMode = false;

  ngOnInit() {
    this.isEditMode = !!this.incomingData;

    this.userForm = this.fb.group({
      name: [this.incomingData?.name || '', [Validators.required, Validators.minLength(2)]],
      email: [this.incomingData?.email || '', [Validators.required, Validators.email]],

      role: [this.incomingData?.role || 'viewer', Validators.required],

      password: [
        '',
        this.isEditMode ? [] : [Validators.required, Validators.minLength(8)]
      ]
    });
  }

  onSubmit() {
    if (this.userForm.valid) {
      this.dialogRef.close(this.userForm.value);
    }
  }

  onCancel() {
    this.dialogRef.close(false);
  }
}
