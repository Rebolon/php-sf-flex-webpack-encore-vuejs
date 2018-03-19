import { TestBed, async } from '@angular/core/testing';
import { WizardSumupComponent } from './wizard-sumup.component';
describe('WizardSumupComponent', () => {
  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [
          WizardSumupComponent
      ],
    }).compileComponents();
  }));
  it('should create the component', async(() => {
    const fixture = TestBed.createComponent(WizardSumupComponent);
    const app = fixture.debugElement.componentInstance;
    expect(app).toBeTruthy();
  }));
});
