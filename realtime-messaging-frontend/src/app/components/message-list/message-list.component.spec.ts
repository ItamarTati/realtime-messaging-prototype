import { ComponentFixture, TestBed } from '@angular/core/testing';
import { MessageListComponent } from './message-list.component';
import { ApiService } from '../../services/api.service';
import { PusherService } from '../../services/pusher.service';
import { UserService } from '../../services/user.service';
import { ToastrService } from 'ngx-toastr';
import { of } from 'rxjs';

describe('MessageListComponent', () => {
  let component: MessageListComponent;
  let fixture: ComponentFixture<MessageListComponent>;
  let apiServiceMock: any;
  let pusherServiceMock: any;
  let userServiceMock: any;
  let toastrServiceMock: any;

  beforeEach(async () => {
    apiServiceMock = {
      getMessages: jasmine.createSpy('getMessages').and.returnValue(of([
        { id: 1, content: 'Test Message 1', status: 'pending' },
      ])),
    };
    pusherServiceMock = {
      pusher: {
        subscribe: jasmine.createSpy('subscribe').and.returnValue({
          bind: jasmine.createSpy('bind'),
        }),
      },
    };
    userServiceMock = {
      getUserIdentifier: jasmine.createSpy('getUserIdentifier').and.returnValue('user-123'),
    };
    toastrServiceMock = {
      error: jasmine.createSpy('error'),
      info: jasmine.createSpy('info'),
    };

    await TestBed.configureTestingModule({
      imports: [MessageListComponent],
      providers: [
        { provide: ApiService, useValue: apiServiceMock },
        { provide: PusherService, useValue: pusherServiceMock },
        { provide: UserService, useValue: userServiceMock },
        { provide: ToastrService, useValue: toastrServiceMock },
      ],
    }).compileComponents();

    fixture = TestBed.createComponent(MessageListComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });

  it('should fetch and display messages', () => {
    expect(apiServiceMock.getMessages).toHaveBeenCalled();
    expect(component.messages.length).toBe(1);
    expect(component.messages[0].content).toBe('Test Message 1');
  });

  it('should listen for Pusher updates', () => {
    expect(pusherServiceMock.pusher.subscribe).toHaveBeenCalledWith('conversation.user-123');
  });
});