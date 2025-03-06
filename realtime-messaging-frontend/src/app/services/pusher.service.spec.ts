import { TestBed } from '@angular/core/testing';
import { PusherService } from './pusher.service';

describe('PusherService', () => {
  let service: PusherService;

  beforeEach(() => {
    TestBed.configureTestingModule({});
    service = TestBed.inject(PusherService);
  });

  it('should be created', () => {
    expect(service).toBeTruthy();
  });

  it('should subscribe to a Pusher channel', () => {
    const userIdentifier = 'user-123';
    const channelName = `conversation.${userIdentifier}`;

    const observable = service.listenForUpdates(userIdentifier);
    expect(observable).toBeTruthy(); 
  });
});