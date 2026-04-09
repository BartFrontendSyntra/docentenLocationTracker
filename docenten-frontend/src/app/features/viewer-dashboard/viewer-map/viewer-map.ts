import { Component, OnInit, AfterViewInit, inject, signal, computed, effect } from '@angular/core';
import { CommonModule } from '@angular/common';
import { TeacherService, Teacher } from '@core/services/teacher-service';
import * as L from 'leaflet';

// Material Imports
import { MatSelectModule } from '@angular/material/select';
import { MatSliderModule } from '@angular/material/slider';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatIconModule } from '@angular/material/icon';
import { MatButtonModule } from '@angular/material/button';

// Fix for Leaflet's missing default marker icons in Angular
L.Icon.Default.imagePath = 'https://unpkg.com/leaflet@1.9.4/dist/images/';

@Component({
  selector: 'app-viewer-map',
  standalone: true,
  imports: [
    CommonModule,
    MatSelectModule,
    MatSliderModule,
    MatFormFieldModule,
    MatInputModule,
    MatIconModule,
    MatButtonModule
  ],
  templateUrl: './viewer-map.html',
  styleUrls: ['./viewer-map.css']
})
export class ViewerMap implements OnInit, AfterViewInit {
  private teacherService = inject(TeacherService);
  private map!: L.Map;
  private markerGroup = L.layerGroup(); // Holds all our pins so we can clear them easily

  teachers = signal<Teacher[]>([]);
  searchQuery = signal<string>('');
  selectedCert = signal<string>('All');
  selectedCourse = signal<string>('All');
  maxDistance = signal<number>(150);
  myLocation = signal<{lat: number, lng: number} | null>({ lat: 50.99727256605152, lng: 5.535658697630291 }); // Hasselt default


  availableCerts = computed(() => {
    const allCerts = this.teachers().flatMap(t => t.certificates?.map(c => c.name) || []);
    return ['All', ...new Set(allCerts)];
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

    if (query) {
      result = result.filter(t =>
        t.first_name.toLowerCase().includes(query) || t.last_name.toLowerCase().includes(query)
      );
    }
    if (cert !== 'All') {
      result = result.filter(t => t.certificates?.some(c => c.name === cert));
    }
    if (course !== 'All') {
      result = result.filter(t => t.courses?.some(c => c.name === course));
    }
    if (myLoc && maxDist < 150) {
      result = result.filter(t => {
        if (!t.address?.location_data) return false;
        const distance = this.calculateDistance(myLoc.lat, myLoc.lng, t.address.location_data.lat, t.address.location_data.lng);
        return distance <= maxDist;
      });
    }

    return result;
  });

  constructor() {
    effect(() => {

      const teachersToMap = this.filteredTeachers();
      const currentRadius = this.maxDistance();
      const centerLoc = this.myLocation();

      if (this.map) {
        this.updateMapMarkers(teachersToMap, currentRadius, centerLoc);
      }
    });
  }

  async ngOnInit() {
    try {
      const data = await this.teacherService.getAllTeachers();
      this.teachers.set(data);
    } catch (error) {
      console.error('Failed to load teachers', error);
    }
  }

  ngAfterViewInit() {
    this.map = L.map('teacher-map').setView([50.99727256605152, 5.535658697630291], 14); // Centered on T2

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap contributors'
    }).addTo(this.map);

    this.markerGroup.addTo(this.map);

    this.updateMapMarkers(this.filteredTeachers(), this.maxDistance(), this.myLocation());
  }

  // Marker Logic
  private updateMapMarkers(
    teachers: Teacher[],
    maxDist: number,
    myLoc: {lat: number, lng: number} | null
  ) {
    // Wipe the map clean
    this.markerGroup.clearLayers();

    if (myLoc && maxDist < 150) {
      const searchRadius = L.circle([myLoc.lat, myLoc.lng], {
        color: '#3f51b5',
        fillColor: '#3f51b5',
        fillOpacity: 0.1,
        weight: 2,
        radius: maxDist * 1000 //  Kilometers to Meters
      });

      //Add a pin for "My Location" in the center
      const centerPin = L.circleMarker([myLoc.lat, myLoc.lng], {
        radius: 5,
        color: 'red',
        fillColor: 'red',
        fillOpacity: 1
      }).bindPopup('<strong>Search Center</strong>');

      // Add them to the group so they get cleared on the next update
      this.markerGroup.addLayer(searchRadius);
      this.markerGroup.addLayer(centerPin);
    }


    teachers.forEach(teacher => {
      if (teacher.address?.location_data) {
        const marker = L.marker([
          teacher.address.location_data.lat,
          teacher.address.location_data.lng
        ]);

        const popupContent = `
          <strong>${teacher.first_name} ${teacher.last_name}</strong><br>
          ${teacher.email}<br>
          <em>${teacher.address.city?.name || 'Unknown City'}</em>
        `;

        marker.bindPopup(popupContent);
        this.markerGroup.addLayer(marker);
      }
    });
  }

  // put search input value into our signal
  onSearchInput(event: Event) {
    this.searchQuery.set((event.target as HTMLInputElement).value);
  }

  // Haversine Formula (Same as before)
  private calculateDistance(lat1: number, lon1: number, lat2: number, lon2: number): number {
    const R = 6371;
    const dLat = this.deg2rad(lat2 - lat1);
    const dLon = this.deg2rad(lon2 - lon1);
    const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) + Math.cos(this.deg2rad(lat1)) * Math.cos(this.deg2rad(lat2)) * Math.sin(dLon / 2) * Math.sin(dLon / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c;
  }
  private deg2rad(deg: number): number { return deg * (Math.PI / 180); }
}
