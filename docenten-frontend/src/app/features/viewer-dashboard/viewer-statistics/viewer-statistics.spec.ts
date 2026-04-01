import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ViewerStatistics } from './viewer-statistics';

describe('ViewerStatistics', () => {
  let component: ViewerStatistics;
  let fixture: ComponentFixture<ViewerStatistics>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ViewerStatistics]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ViewerStatistics);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
