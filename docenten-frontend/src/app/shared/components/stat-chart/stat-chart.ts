import { Component, Input, OnChanges, SimpleChanges, ViewChild } from '@angular/core';
import { BaseChartDirective } from 'ng2-charts';
import { ChartConfiguration } from 'chart.js';

@Component({
  selector: 'app-stat-chart',
  standalone: true,
  imports: [BaseChartDirective],
  templateUrl: './stat-chart.html',
})
export class StatChart implements OnChanges {
  @Input({ required: true }) title!: string;
  @Input({ required: true }) labels!: string[];
  @Input({ required: true }) data!: number[];
  @Input() color: string = '#3f51b5';

  @ViewChild(BaseChartDirective) chart?: BaseChartDirective;

  chartData: ChartConfiguration<'bar'>['data'] = {
    labels: [],
    datasets: []
  };

  chartOptions: ChartConfiguration<'bar'>['options'] = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { display: false } }
  };

  ngOnChanges(changes: SimpleChanges) {
    if (changes['labels'] || changes['data']) {
      this.chartData = {
        labels: this.labels,
        datasets: [{
          data: this.data,
          backgroundColor: this.color + '80',
          borderColor: this.color,
          borderWidth: 1,
          borderRadius: 4
        }]
      };
      this.chart?.update();
    }
  }
}
