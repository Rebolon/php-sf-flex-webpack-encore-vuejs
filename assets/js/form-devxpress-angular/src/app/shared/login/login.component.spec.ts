import { TestBed, async } from '@angular/core/testing';
import { LoginComponent } from './login.component';
import {RouterTestingModule} from "@angular/router/testing";
describe('LoginComponent', () => {
  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [
          LoginComponent
      ],
      imports: [
          RouterTestingModule,
      ],
    }).compileComponents();
  }));
  it('should create the component', async(() => {
    const fixture = TestBed.createComponent(LoginComponent);
    const app = fixture.debugElement.componentInstance;
    expect(app).toBeTruthy();
  }));
});
