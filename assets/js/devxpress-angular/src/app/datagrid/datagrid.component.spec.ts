import { TestBed, async } from '@angular/core/testing';
import { DatagridComponent } from './datagrid.component';
import {SharedModule} from "../shared/shared.module";
import {ApiService} from "../../services/api";
import {HttpClientTestingModule} from "@angular/common/http/testing";
import {ApiServiceMock} from "../../services/api-mock";
describe('DatagridComponent', () => {
  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [
          DatagridComponent
      ],
      imports: [
          HttpClientTestingModule,
          SharedModule,
      ],
      providers: [
          {provide: ApiService, useClass: ApiServiceMock}
      ]
    }).compileComponents();
  }));
  it('should create the component', async(() => {
    const fixture = TestBed.createComponent(DatagridComponent);
    const app = fixture.debugElement.componentInstance;
    expect(app).toBeTruthy();
  }));
});
