import { Component, Input } from '@angular/core';
import { MatCardModule } from '@angular/material/card';
import { MatIconModule } from '@angular/material/icon';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-stat-card',
  standalone: true,
  imports: [CommonModule, MatCardModule, MatIconModule],
  templateUrl: './stat-card.html',
  styleUrls: ['./stat-card.css']
})
export class StatCard {
  @Input({ required: true }) title!: string;
  @Input({ required: true }) value!: number | string;
  @Input() icon: string = 'analytics';
  @Input() color: string = '#3f51b5';
}
