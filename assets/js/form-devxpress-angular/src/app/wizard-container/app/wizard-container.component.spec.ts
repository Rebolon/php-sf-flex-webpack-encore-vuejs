import {TestBed, async} from '@angular/core/testing';
import {WizardContainerComponent} from './wizard-container.component';
import {SharedModule} from "../../shared/shared.module";
import {WizardStepComponent} from "./wizard-step/wizard-step.component";
  import {RouterTestingModule} from "@angular/router/testing";
import {HttpClientTestingModule, HttpTestingController} from '@angular/common/http/testing';
import {ApiService} from '../../../services/api';
import {ApiServiceMock} from '../../../services/api-mock';

describe('WizardContainerComponent', () => {
  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [
          WizardContainerComponent,
          WizardStepComponent
      ],
      imports: [
          RouterTestingModule,
          HttpClientTestingModule,
          SharedModule,
      ],
      providers: [
        {provide: ApiService, useClass: ApiServiceMock},
      ]
    }).compileComponents();
  }));
  it('should create the component', async(() => {
    const fixture = TestBed.createComponent(WizardContainerComponent);
    const app = fixture.debugElement.componentInstance;
    expect(app).toBeTruthy();
  }));
});
