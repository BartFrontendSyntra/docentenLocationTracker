import { Component, OnInit, inject, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { TeacherService, Teacher } from '@core/services/teacher-service';
import { StatCard } from '@shared/components/stat-card/stat-card';
import { StatChart } from '@shared/components/stat-chart/stat-chart';

@Component({
  selector: 'app-viewer-statistics',
  standalone: true,
  imports: [CommonModule, StatCard, StatChart],
  templateUrl: './viewer-statistics.html',
  styleUrls: ['./viewer-statistics.css']
})
export class ViewerStatistics implements OnInit {
  private teacherService = inject(TeacherService);

  // Signals for the UI
  totalTeachers = signal<number>(0);
  totalCourses = signal<number>(0);
  totalCerts = signal<number>(0);

  courseChartLabels = signal<string[]>([]);
  courseChartData = signal<number[]>([]);

  certChartLabels = signal<string[]>([]);
  certChartData = signal<number[]>([]);

  async ngOnInit() {
    const teachers = await this.teacherService.getAllTeachers();
    this.processData(teachers);
  }

  private processData(teachers: Teacher[]) {
    this.totalTeachers.set(teachers.length);

    const courseCounts: Record<string, number> = {};
    const certCounts: Record<string, number> = {};

    teachers.forEach(teacher => {

      teacher.courses?.forEach(course => {
        courseCounts[course.name] = (courseCounts[course.name] || 0) + 1;
      });

      teacher.certificates?.forEach(cert => {
        certCounts[cert.name] = (certCounts[cert.name] || 0) + 1;
      });
    });

    this.totalCourses.set(Object.keys(courseCounts).length);
    this.totalCerts.set(Object.keys(certCounts).length);

    this.courseChartLabels.set(Object.keys(courseCounts));
    this.courseChartData.set(Object.values(courseCounts));

    this.certChartLabels.set(Object.keys(certCounts));
    this.certChartData.set(Object.values(certCounts));
  }
}
