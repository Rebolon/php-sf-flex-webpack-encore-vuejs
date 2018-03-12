import { TestBed, async } from '@angular/core/testing';
import { DatagridComponent } from './datagrid.component';
describe('DatagridComponent', () => {
  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [
          DatagridComponent
      ],
    }).compileComponents();
  }));
  it('should create the component', async(() => {
    const fixture = TestBed.createComponent(DatagridComponent);
    const app = fixture.debugElement.componentInstance;
    expect(app).toBeTruthy();
  }));
});
