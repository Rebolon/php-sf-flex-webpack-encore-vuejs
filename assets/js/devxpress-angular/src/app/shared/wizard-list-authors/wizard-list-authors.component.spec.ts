import { TestBed, async } from '@angular/core/testing';
import { WizardListAuthorsComponent } from './wizard-list-authors.component';
describe('WizardListAuthorsComponent', () => {
  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [
          WizardListAuthorsComponent
      ],
    }).compileComponents();
  }));
  it('should create the component', async(() => {
    const fixture = TestBed.createComponent(WizardListAuthorsComponent);
    const app = fixture.debugElement.componentInstance;
    expect(app).toBeTruthy();
  }));
});
