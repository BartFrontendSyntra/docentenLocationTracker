import { Component, OnInit, inject, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { TeacherService, Teacher } from '@core/services/teacher-service';
import { UserService } from '@core/services/user-service';
import { StatCard } from '@shared/components/stat-card/stat-card';
import { StatChart } from '@shared/components/stat-chart/stat-chart';

@Component({
  selector: 'app-admin-statistics',
  standalone: true,
  imports: [CommonModule, StatCard, StatChart],
  templateUrl: './admin-statistics.html',
  styleUrls: ['./admin-statistics.css']
})
export class AdminStatistics implements OnInit {
  private teacherService = inject(TeacherService);
  private userService = inject(UserService);

  // Admin Specific Signals
  totalUsers = signal<number>(0);

  // General Signals
  totalTeachers = signal<number>(0);
  totalCourses = signal<number>(0);
  totalCerts = signal<number>(0);

  courseChartLabels = signal<string[]>([]);
  courseChartData = signal<number[]>([]);

  certChartLabels = signal<string[]>([]);
  certChartData = signal<number[]>([]);

  async ngOnInit() {
    try {
      const [teachers, users] = await Promise.all([
        this.teacherService.getAllTeachers(),
        this.userService.getAllUsers()
      ]);

      this.totalUsers.set(users.length);
      this.processData(teachers);
    } catch (error) {
      console.error('Failed to load dashboard statistics', error);
    }
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
