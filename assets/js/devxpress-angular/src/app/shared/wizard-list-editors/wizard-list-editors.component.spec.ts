import { TestBed, async } from '@angular/core/testing';
import { WizardListEditorsComponent } from './wizard-list-editors.component';
describe('WizardListEditorsComponent', () => {
  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [
          WizardListEditorsComponent
      ],
    }).compileComponents();
  }));
  it('should create the component', async(() => {
    const fixture = TestBed.createComponent(WizardListEditorsComponent);
    const app = fixture.debugElement.componentInstance;
    expect(app).toBeTruthy();
  }));
});
