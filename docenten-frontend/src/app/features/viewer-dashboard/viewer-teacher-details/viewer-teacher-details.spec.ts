import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ViewerTeacherDetails } from './viewer-teacher-details';

describe('ViewerTeacherDetails', () => {
  let component: ViewerTeacherDetails;
  let fixture: ComponentFixture<ViewerTeacherDetails>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ViewerTeacherDetails]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ViewerTeacherDetails);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
