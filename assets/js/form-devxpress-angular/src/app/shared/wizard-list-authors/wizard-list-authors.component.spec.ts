import { TestBed, async } from '@angular/core/testing';
import { WizardListAuthorsComponent } from './wizard-list-authors.component';
import {SharedModule} from '../shared.module';
describe('WizardListAuthorsComponent', () => {
  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [
      ],
      imports: [
          SharedModule,
      ]
    }).compileComponents();
  }));
  it('should create the component', async(() => {
    const fixture = TestBed.createComponent(WizardListAuthorsComponent);
    const app = fixture.debugElement.componentInstance;
    expect(app).toBeTruthy();
  }));
});
