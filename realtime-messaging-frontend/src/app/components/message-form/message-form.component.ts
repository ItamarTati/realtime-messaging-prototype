import { Component } from '@angular/core';
import { ApiService } from '../../services/api.service';
import { UserService } from '../../services/user.service';
import { FormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common'
import { ToastrService } from 'ngx-toastr'; // Import ToastrService

@Component({
  selector: 'app-message-form',
  standalone: true,
  imports: [FormsModule, CommonModule],
  templateUrl: './message-form.component.html',
  styleUrls: ['./message-form.component.css'],
})
export class MessageFormComponent {
  content: string = '';
  error: string = '';

  constructor(
    private apiService: ApiService, 
    private userService: UserService,     
    private toastr: ToastrService // Inject ToastrService
  ) {}

  sendMessage() {
    const userIdentifier = this.userService.getUserIdentifier();
  
    if (!userIdentifier) {
      this.error = 'User identifier not found. Please refresh the page.';
      return;
    }
  
    this.apiService.sendMessage(this.content).subscribe({
      next: () => {
        this.content = '';
        this.error = '';
        this.toastr.success('Message sent successfully!');
      },
      error: (err) => {
        this.error = err.error?.message || 'Failed to send message.';
        this.toastr.error('Failed to send message.');
      },
    });
  }
}