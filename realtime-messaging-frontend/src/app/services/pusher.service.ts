import { Injectable } from '@angular/core';
import Pusher from 'pusher-js';
import { Observable, Subject } from 'rxjs';

@Injectable({
  providedIn: 'root',
})
export class PusherService {
  pusher: Pusher;
  channel: any;
  private messageSubject = new Subject<any>();

  constructor() {
    this.pusher = new Pusher('ec2f2b4d7df634de764d', {
      cluster: 'eu',
      forceTLS: true,
    });
    this.pusher.connection.bind('connected', () => {
      console.log('Pusher connected successfully');
    });

    this.pusher.connection.bind('error', (err: any) => {
        console.error('Pusher connection error:', err);
    });

    this.pusher.connection.bind('disconnected', () => {
        console.warn('Pusher disconnected');
    });
  }

    listenForUpdates(userIdentifier: string): Observable<any> {
      const channelName = `conversation.${userIdentifier}`;
      console.log('Subscribing to channel:', channelName);

      this.channel = this.pusher.subscribe(channelName);
      this.channel.bind('message.created', (data: any) => {
          console.log('Received event:', data);
          this.messageSubject.next(data);
      });

      return this.messageSubject.asObservable();
    }
}