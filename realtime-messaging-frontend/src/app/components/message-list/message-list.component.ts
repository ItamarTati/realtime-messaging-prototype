import { Component, OnInit } from '@angular/core';
import { ApiService } from '../../services/api.service';
import { PusherService } from '../../services/pusher.service';
import { UserService } from '../../services/user.service';
import { CommonModule } from '@angular/common'; 
import { ToastrService } from 'ngx-toastr'; 

@Component({
  selector: 'app-message-list',
  standalone: true,
  imports: [CommonModule], 
  templateUrl: './message-list.component.html',
  styleUrls: ['./message-list.component.css'],
})
export class MessageListComponent implements OnInit {
  messages: any[] = [];

  constructor(
    private apiService: ApiService,
    private pusherService: PusherService,
    private userService: UserService,
    private toastr: ToastrService 
  ) {}

  ngOnInit() {

    this.fetchMessages();
    this.listenForUpdates();
  }

  fetchMessages() {
    this.apiService.getMessages().subscribe({
      next: (response: any) => {
        this.messages = response;
      },
      error: (err) => {
        console.error('Failed to fetch messages:', err);
        this.toastr.error('Failed to fetch messages.'); 
      },
    });
  }

  listenForUpdates() {
    const userIdentifier = this.userService.getUserIdentifier();
    const channelName = `conversation.${userIdentifier}`;
    console.log('Subscribing to channel:', channelName);
    const channel = this.pusherService.pusher.subscribe(channelName);
    channel.bind('message.created', (data: any) => {
      console.log('New message received:', data); 
      this.messages = [...this.messages, data.message]; 
      this.toastr.info('A new message has been processed!'); 
    });
  }
}