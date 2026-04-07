import { Component, inject, OnInit, signal, computed } from '@angular/core';
import { CommonModule } from '@angular/common';

// Material Imports
import { MatSelectModule } from '@angular/material/select';
import { MatSliderModule } from '@angular/material/slider';
import { MatCardModule } from '@angular/material/card';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatIconModule } from '@angular/material/icon';
import { MatButtonModule } from '@angular/material/button';
import { Teacher, TeacherService } from '@app/core/services/teacher-service';

@Component({
  selector: 'app-viewer-teacher-list',
  imports: [CommonModule,
    MatCardModule,
    MatFormFieldModule,
    MatInputModule,
    MatIconModule,
    MatButtonModule,
    MatSliderModule,
    MatSelectModule],
  standalone: true,
  templateUrl: './viewer-teacher-list.html',
  styleUrl: './viewer-teacher-list.css',
})
export class ViewerTeacherList {

private teacherService = inject(TeacherService);

  teachers = signal<Teacher[]>([]);
  searchQuery = signal<string>('');
  isLoading = signal<boolean>(true);

  

  filteredTeachers = computed(() => {
    const query = this.searchQuery().toLowerCase().trim();
    const allTeachers = this.teachers();

    if (!query) {
      return allTeachers;
    }

    return allTeachers.filter(teacher =>
      teacher.first_name.toLowerCase().includes(query) ||
      teacher.last_name.toLowerCase().includes(query) ||
      teacher.email.toLowerCase().includes(query)
    );
  });

  async ngOnInit() {
    try {
      const data = await this.teacherService.getAllTeachers();
      this.teachers.set(data);
    } catch (error) {
      console.error('Failed to load teachers', error);
    } finally {
      this.isLoading.set(false);
      console.log(this.teachers());

    }
  }

  // change search query when the user is typing
  onSearchInput(event: Event) {
    const inputElement = event.target as HTMLInputElement;
    this.searchQuery.set(inputElement.value);
  }
}
