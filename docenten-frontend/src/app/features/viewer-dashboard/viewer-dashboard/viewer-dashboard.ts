import { Component, inject, signal } from '@angular/core';
import { Router, RouterModule } from '@angular/router';
import { AuthService } from '@core/auth/auth-service';


import { MatToolbarModule } from '@angular/material/toolbar';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatCardModule } from '@angular/material/card';
import { MatSidenavModule } from '@angular/material/sidenav';
import { MatListModule } from '@angular/material/list';

@Component({
  selector: 'app-viewer-dashboard',
  standalone: true,
  imports: [MatToolbarModule, MatButtonModule, MatIconModule, MatCardModule, MatSidenavModule, MatListModule, RouterModule],
  templateUrl: './viewer-dashboard.html',
  styleUrl: './viewer-dashboard.css',
})
export class ViewerDashboard {
  private authService = inject(AuthService);
  private router = inject(Router);

  currentUser = this.authService.currentUser;

  teachers = signal([
    {
      id: 1,
      first_name: 'John',
      last_name: 'Doe',
      email: 'john.doe@example.com',
      company_number: 'BE0123456789',
      telephone: '02/123.45.67',
      cellphone: '0470/12.34.56',
      address_id: 1,
      address: {
        id: 1,
        street: 'Main Street',
        number: '123',
        box: 'A',
        zip_code: '1000',
        city: { id: 1, name: 'Brussels', postal_code: '1000' }
      },
      courses: [
        { id: 1, name: 'Angular for Beginners' },
        { id: 2, name: 'Advanced Laravel' }
      ],
      certificates: [
        { id: 1, name: 'Certified Web Developer' }
      ]
    },
    {
      id: 2,
      first_name: 'Jane',
      last_name: 'Smith',
      email: 'jane.smith@example.com',
      company_number: 'BE0987654321',
      telephone: '03/987.65.43',
      cellphone: '0490/98.76.54',
      address_id: 2,
      address: {
        id: 2,
        street: 'Tech Lane',
        number: '42',
        box: null,
        zip_code: '9000',
        city: { id: 2, name: 'Ghent', postal_code: '9000' }
      },
      courses: [
        { id: 3, name: 'Vue.js Mastery' }
      ],
      certificates: []
    }
  ]);

  logout() {
    this.authService.logout();
    this.router.navigate(['/login']);
  }
}
