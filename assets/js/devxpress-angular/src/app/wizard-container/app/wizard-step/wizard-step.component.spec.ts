import { TestBed, async } from '@angular/core/testing';
import { WizardStepComponent } from './wizard-step.component';
describe('WizardStepComponent', () => {
  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [
          WizardStepComponent
      ],
    }).compileComponents();
  }));
  it('should create the component', async(() => {
    const fixture = TestBed.createComponent(WizardStepComponent);
    const app = fixture.debugElement.componentInstance;
    expect(app).toBeTruthy();
  }));
});
