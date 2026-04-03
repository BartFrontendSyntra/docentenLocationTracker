import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ViewerMap } from './viewer-map';

describe('ViewerMap', () => {
  let component: ViewerMap;
  let fixture: ComponentFixture<ViewerMap>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ViewerMap]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ViewerMap);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
