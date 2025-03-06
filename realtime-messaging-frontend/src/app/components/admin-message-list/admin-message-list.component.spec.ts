import { ComponentFixture, TestBed } from '@angular/core/testing';
import { AdminMessageListComponent } from './admin-message-list.component';
import { ApiService } from '../../services/api.service';
import { of } from 'rxjs';

describe('AdminMessageListComponent', () => {
  let component: AdminMessageListComponent;
  let fixture: ComponentFixture<AdminMessageListComponent>;
  let apiServiceMock: any;

  beforeEach(async () => {
    apiServiceMock = {
      getMessages: jasmine.createSpy('getMessages').and.returnValue(of([
        { id: 1, content: 'Test Message 1', status: 'pending' },
        { id: 2, content: 'Test Message 2', status: 'processed' },
      ])),
      markMessageAsCompleted: jasmine.createSpy('markMessageAsCompleted').and.returnValue(of({})),
    };

    await TestBed.configureTestingModule({
      imports: [AdminMessageListComponent],
      providers: [
        { provide: ApiService, useValue: apiServiceMock },
      ],
    }).compileComponents();

    fixture = TestBed.createComponent(AdminMessageListComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });

  it('should fetch and display pending messages', () => {
    expect(apiServiceMock.getMessages).toHaveBeenCalled();
    expect(component.messages.length).toBe(1);
    expect(component.messages[0].content).toBe('Test Message 1');
  });

  it('should mark a message as completed', () => {
    component.markAsCompleted(1);
    expect(apiServiceMock.markMessageAsCompleted).toHaveBeenCalledWith(1);
    expect(apiServiceMock.getMessages).toHaveBeenCalledTimes(2); // Called once in ngOnInit and once after marking as completed
  });
});