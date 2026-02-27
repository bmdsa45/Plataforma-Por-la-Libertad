import { ComponentFixture, TestBed } from '@angular/core/testing';

import { HealthMonitor } from './health-monitor';

describe('HealthMonitor', () => {
  let component: HealthMonitor;
  let fixture: ComponentFixture<HealthMonitor>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [HealthMonitor],
    }).compileComponents();

    fixture = TestBed.createComponent(HealthMonitor);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
