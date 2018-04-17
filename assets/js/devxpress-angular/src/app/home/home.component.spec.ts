import { TestBed, async } from '@angular/core/testing';
import { HomeComponent } from './home.component';
import {RouterTestingModule} from "@angular/router/testing";
describe('HomeComponent', () => {
  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [
          HomeComponent
      ],
      imports: [
          RouterTestingModule,
      ],
    }).compileComponents();
  }));
  it('should create the component', async(() => {
    const fixture = TestBed.createComponent(HomeComponent);
    const app = fixture.debugElement.componentInstance;
    expect(app).toBeTruthy();
  }));
});
