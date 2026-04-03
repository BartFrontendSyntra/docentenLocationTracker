import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ViewerTeacherList } from './viewer-teacher-list';

describe('ViewerTeacherList', () => {
  let component: ViewerTeacherList;
  let fixture: ComponentFixture<ViewerTeacherList>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ViewerTeacherList]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ViewerTeacherList);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
