import { TestBed, async } from '@angular/core/testing';
import { BookComponent } from './book.component';
describe('BookComponent', () => {
  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [
          BookComponent
      ],
    }).compileComponents();
  }));
  it('should create the component', async(() => {
    const fixture = TestBed.createComponent(BookComponent);
    const app = fixture.debugElement.componentInstance;
    expect(app).toBeTruthy();
  }));
});
