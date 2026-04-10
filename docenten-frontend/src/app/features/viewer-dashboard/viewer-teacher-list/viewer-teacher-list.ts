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

  selectedCert = signal<string>('All');
  selectedCourse = signal<string>('All');
  maxDistance = signal<number>(25);

  // default location, needs to be changed to users preferred location or current location
  myLocation = signal<{lat: number, lng: number} | null>({ lat: 50.9383, lng: 5.3486 });

  availableCerts = computed(() => {
    const allCerts = this.teachers().flatMap(t => t.certificates?.map(c => c.name) || []);
    return ['All', ...new Set(allCerts)]; // makes a super usefull array, starting with "All" and then all unique cert names from the teachers list
  });

  availableCourses = computed(() => {
    const allCourses = this.teachers().flatMap(t => t.courses?.map(c => c.name) || []);
    return ['All', ...new Set(allCourses)];
  });

  filteredTeachers = computed(() => {
    let result = this.teachers();
    const query = this.searchQuery().toLowerCase().trim();
    const cert = this.selectedCert();
    const course = this.selectedCourse();
    const maxDist = this.maxDistance();
    const myLoc = this.myLocation();

    // Text Search
    if (query) {
      result = result.filter(t =>
        t.first_name.toLowerCase().includes(query) ||
        t.last_name.toLowerCase().includes(query)
      );
    }

    // Certificate Filter
    if (cert !== 'All') {
      result = result.filter(t => t.certificates?.some(c => c.name === cert));
    }

    // Course Filter
    if (course !== 'All') {
      result = result.filter(t => t.courses?.some(c => c.name === course));
    }

    // Distance Filter (Haversine)
    if (myLoc && maxDist < 250) { // If slider is at 250, we consider it "Any distance"
      result = result.filter(t => {
        if (!t.address?.location_data) return false; // Hide teachers with no location
        const distance = this.calculateDistance(
          myLoc.lat, myLoc.lng,
          t.address.location_data.lat, t.address.location_data.lng
        );
        return distance <= maxDist;
      });
    }

    return result;
  });

  async ngOnInit() {
    try {
      const data = await this.teacherService.getAllTeachers();
      this.teachers.set(data);
    } catch (error) {
      console.error('Failed to load teachers', error);
    } finally {
      this.isLoading.set(false);
    }
  }

  // change search query when the user is typing
  onSearchInput(event: Event) {
    const inputElement = event.target as HTMLInputElement;
    this.searchQuery.set(inputElement.value);
  }

  private calculateDistance(lat1: number, lon1: number, lat2: number, lon2: number): number {
    const R = 6371; // Earth's radius in km
    const dLat = this.deg2rad(lat2 - lat1);
    const dLon = this.deg2rad(lon2 - lon1);
    const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
              Math.cos(this.deg2rad(lat1)) * Math.cos(this.deg2rad(lat2)) *
              Math.sin(dLon / 2) * Math.sin(dLon / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c;
  }

  private deg2rad(deg: number): number {
    return deg * (Math.PI / 180);
  }
}
