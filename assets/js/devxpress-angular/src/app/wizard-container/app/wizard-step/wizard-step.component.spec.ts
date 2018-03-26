import { TestBed, async } from '@angular/core/testing';
import { WizardComponent } from './wizard.component';
describe('WizardComponent', () => {
  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [
          WizardComponent
      ],
    }).compileComponents();
  }));
  it('should create the component', async(() => {
    const fixture = TestBed.createComponent(WizardComponent);
    const app = fixture.debugElement.componentInstance;
    expect(app).toBeTruthy();
  }));
});
