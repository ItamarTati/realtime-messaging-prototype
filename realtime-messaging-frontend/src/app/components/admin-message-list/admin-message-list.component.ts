import { Component, OnInit } from '@angular/core';
import { ApiService } from '../../services/api.service';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-admin-message-list',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './admin-message-list.component.html',
  styleUrls: ['./admin-message-list.component.css'],
})
export class AdminMessageListComponent implements OnInit {
  messages: any[] = [];

  constructor(private apiService: ApiService) {}

  ngOnInit() {
    this.fetchMessages();
  }

  fetchMessages() {
    this.apiService.getMessages().subscribe({
      next: (response: any) => {
        this.messages = response.filter((msg: any) => msg.status === 'pending');
      },
      error: (err) => {
        console.error('Failed to fetch messages:', err);
      },
    });
  }

  markAsCompleted(id: number) {
    this.apiService.markMessageAsCompleted(id).subscribe({
      next: () => {
        this.fetchMessages(); // Refresh the list
      },
      error: (err) => {
        console.error('Failed to mark message as completed:', err);
      },
    });
  }
}