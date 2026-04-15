import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { Teacher } from '@core/services/teacher-service';

// Material Imports
import { MAT_DIALOG_DATA, MatDialogModule } from '@angular/material/dialog';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatChipsModule } from '@angular/material/chips';
import { MatDividerModule } from '@angular/material/divider';

@Component({
  selector: 'app-viewer-teacher-dialog',
  standalone: true,
  imports: [
    CommonModule,
    MatDialogModule,
    MatButtonModule,
    MatIconModule,
    MatChipsModule,
    MatDividerModule
  ],
  templateUrl: './viewer-teacher-details.html',
  styleUrls: ['./viewer-teacher-details.css']
})
export class ViewerTeacherDetails {
  public teacher: Teacher = inject(MAT_DIALOG_DATA);
}
