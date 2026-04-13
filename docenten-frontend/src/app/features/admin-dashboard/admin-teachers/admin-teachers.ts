import { Component, OnInit, inject, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { TeacherService, Teacher } from '@core/services/teacher-service';

// Material Imports
import { MatTableModule } from '@angular/material/table';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatDialog, MatDialogModule } from '@angular/material/dialog';
import { MatTooltipModule } from '@angular/material/tooltip';
import { AdminTeacherDialog } from '../admin-teacher-dialog/admin-teacher-dialog';

@Component({
  selector: 'app-admin-teachers',
  standalone: true,
  imports: [
    CommonModule,
    MatTableModule,
    MatButtonModule,
    MatIconModule,
    MatDialogModule,
    MatTooltipModule
  ],
  templateUrl: './admin-teachers.html',
  styleUrls: ['./admin-teachers.css']
})
export class AdminTeachers implements OnInit {
  private teacherService = inject(TeacherService);
  private dialog = inject(MatDialog);

  // State Signals
  teachers = signal<Teacher[]>([]);
  isLoading = signal<boolean>(true);

  displayedColumns: string[] = ['name', 'email', 'company_number', 'actions'];

  async ngOnInit() {
    await this.loadTeachers();
  }

  async loadTeachers() {
    this.isLoading.set(true);
    try {
      const data = await this.teacherService.getAllTeachers();
      this.teachers.set(data);
    } catch (error) {
      console.error('Failed to load teachers:', error);
    } finally {
      this.isLoading.set(false);
    }
  }

  async deleteTeacher(teacher: Teacher) {
    const confirmed = window.confirm(`Are you sure you want to completely remove ${teacher.first_name} ${teacher.last_name}?`);

    if (confirmed) {
      try {
        await this.teacherService.deleteTeacher(teacher.id);
        this.teachers.update(current => current.filter(t => t.id !== teacher.id));
      } catch (error) {
        console.error('Failed to delete teacher:', error);
        alert('An error occurred while deleting the teacher.');
      }
    }
  }

  openTeacherForm(teacher?: Teacher) {
    const dialogRef = this.dialog.open(AdminTeacherDialog, {
      width: '700px', // We make this one wider to accommodate the tabs comfortably
      data: teacher,
      disableClose: true // Prevents closing if they accidentally click outside the modal
    });

    dialogRef.afterClosed().subscribe(async (formData) => {
      // If formData exists, they clicked "Save" and the form was valid
      if (formData) {
        try {
          if (teacher) {
            // --- EDIT MODE ---
            const updatedTeacher = await this.teacherService.updateTeacher(teacher.id, formData);

            // Instantly update the UI
            this.teachers.update(currentTeachers =>
              currentTeachers.map(t => t.id === teacher.id ? updatedTeacher : t)
            );
          } else {
            // --- CREATE MODE ---
            const newTeacher = await this.teacherService.createTeacher(formData);

            // Instantly add to the UI
            this.teachers.update(currentTeachers => [...currentTeachers, newTeacher]);
          }
        } catch (error) {
          console.error('Operation failed:', error);
          alert('Failed to save the instructor profile. Check the console for details.');
        }
      }
    });
  }
}
