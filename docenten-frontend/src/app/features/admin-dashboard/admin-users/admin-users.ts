import { Component, OnInit, inject, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { UserService, User } from '@core/services/user-service';

import { MatTableModule } from '@angular/material/table';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatChipsModule } from '@angular/material/chips';

import { MatDialog, MatDialogModule } from '@angular/material/dialog';
import { AdminUserDialogComponent } from '@features/admin-dashboard/admin-user-dialog/admin-user-dialog'

@Component({
  selector: 'app-admin-users',
  standalone: true,
  imports: [
    CommonModule,
    MatTableModule,
    MatButtonModule,
    MatIconModule,
    MatChipsModule,
    MatDialogModule
  ],
  templateUrl: './admin-users.html',
  styleUrls: ['./admin-users.css']
})
export class AdminUsers implements OnInit {
  private userService = inject(UserService);
  private dialog = inject(MatDialog);

  users = signal<User[]>([]);
  isLoading = signal<boolean>(true);

  displayedColumns: string[] = ['name', 'email', 'role', 'actions'];

  async ngOnInit() {
    await this.loadUsers();
  }

  async loadUsers() {
    this.isLoading.set(true);
    try {
      const data = await this.userService.getAllUsers();
      this.users.set(data);
    } catch (error) {
      console.error('Failed to load users:', error);
    } finally {
      this.isLoading.set(false);
    }
  }


  async deleteUser(user: User) {
    // A simple browser confirmation before doing something destructive
    const confirmed = window.confirm(`Are you sure you want to delete ${user.name}?`);

    if (confirmed) {
      try {
        await this.userService.deleteUser(user.id);

        // Optimistic UI update: Remove the user from the signal immediately even before the API call completes
        this.users.update(currentUsers => currentUsers.filter(u => u.id !== user.id));
      } catch (error) {
        console.error('Failed to delete user:', error);
        alert('An error occurred while deleting the user.');
      }
    }
  }

  // TODO: implement user creation and editing
openUserForm(user?: User) {
    const dialogRef = this.dialog.open(AdminUserDialogComponent, {
      width: '400px',

      data: user
    });

    // When the dialog closes, it triggers this subscribe block
    dialogRef.afterClosed().subscribe(async (formData) => {
      // If formData exists, the user clicked "Save". If it's false, they clicked "Cancel".
      if (formData) {
        try {
          if (user) {
            // EDIT MODE: Call the update API
            const updatedUser = await this.userService.updateUser(user.id, formData);

            // Optimistically update the signal so the table refreshes instantly
            this.users.update(currentUsers =>
              currentUsers.map(u => u.id === user.id ? updatedUser : u)
            );
          } else {
            // CREATE MODE: Call the create API
            const newUser = await this.userService.createUser(formData);

            // Add the new user to the end of the signal array
            this.users.update(currentUsers => [...currentUsers, newUser]);
          }
        } catch (error) {
          console.error('Operation failed:', error);
          alert('Failed to save user. Check console for details.');
        }
      }
    });
  }
}
