import { async, TestBed } from '@angular/core/testing';
import { Router } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { Subject } from 'rxjs';

import { LoginComponent } from './login.component';
import { User } from '../services/user';

describe('LoginComponent', () => {

  const fakeRouter = jasmine.createSpyObj('Router', ['navigate']);
  const fakeUserService = jasmine.createSpyObj('UserService', ['authenticate']);

  beforeEach(() => TestBed.configureTestingModule({
    imports: [FormsModule],
    declarations: [LoginComponent],
    providers: [
      { provide: User, useValue: fakeUserService },
      { provide: Router, useValue: fakeRouter }
    ]
  }));

  beforeEach(() => {
    fakeRouter.navigate.calls.reset();
    fakeUserService.authenticate.calls.reset();
  });

  it('should have a credentials field', () => {
    const fixture = TestBed.createComponent(LoginComponent);

    // when we trigger the change detection
    fixture.detectChanges();

    // then we should have a field credentials
    const componentInstance = fixture.componentInstance;
    expect(componentInstance.credentials)
      .withContext('Your component should have a field `credentials` initialized with an object').not.toBeNull();
    expect(componentInstance.credentials.login)
      .withContext('The `login` field of `credentials` should be initialized with an empty string').toBe('');
    expect(componentInstance.credentials.password)
      .withContext('The `password` field of `credentials` should be initialized with an empty string').toBe('');
  });

  it('should have a title', () => {
    const fixture = TestBed.createComponent(LoginComponent);

    // when we trigger the change detection
    fixture.detectChanges();

    // then we should have a title
    const element = fixture.nativeElement;
    expect(element.querySelector('h1')).withContext('The template should have a `h1` tag').not.toBeNull();
    expect(element.querySelector('h1').textContent).withContext('The title should be `Log in`').toContain('Log in');
  });

  it('should have a disabled button if the form is incomplete', async(() => {
    const fixture = TestBed.createComponent(LoginComponent);

    // when we trigger the change detection
    fixture.detectChanges();

    // then we should have a disabled button
    const element = fixture.nativeElement;

    fixture.whenStable().then(() => {
      fixture.detectChanges();
      expect(element.querySelector('button')).withContext('The template should have a button').not.toBeNull();
      expect(element.querySelector('button').hasAttribute('disabled'))
        .withContext('The button should be disabled if the form is invalid')
        .toBe(true);
    });
  }));

  it('should be possible to log in if the form is complete', async(() => {
    const fixture = TestBed.createComponent(LoginComponent);

    fixture.detectChanges();

    const element = fixture.nativeElement;

    fixture.whenStable().then(() => {
      const loginInput = element.querySelector('input[name="login"]');
      expect(loginInput).withContext('You should have an input with the name `login`').not.toBeNull();
      loginInput.value = 'login';
      loginInput.dispatchEvent(new Event('input'));
      const passwordInput = element.querySelector('input[name="password"]');
      expect(passwordInput).withContext('You should have an input with the name `password`').not.toBeNull();
      passwordInput.value = 'password';
      passwordInput.dispatchEvent(new Event('input'));

      // when we trigger the change detection
      fixture.detectChanges();

      // then we should have a submit button enabled
      expect(element.querySelector('button').hasAttribute('disabled'))
        .withContext('The button should be enabled if the form is valid').toBe(false);
    });
  }));

  it('should display error messages if fields are dirty and invalid', async(() => {
    const fixture = TestBed.createComponent(LoginComponent);

    // when we trigger the change detection
    fixture.detectChanges();

    // then we should have error fields
    const element = fixture.nativeElement;

    fixture.whenStable().then(() => {
      const loginInput = element.querySelector('input[name="login"]');
      expect(loginInput).withContext('You should have an input with the name `login`').not.toBeNull();
      loginInput.value = 'login';
      loginInput.dispatchEvent(new Event('input'));
      loginInput.value = '';
      loginInput.dispatchEvent(new Event('input'));
      fixture.detectChanges();
      const loginError = element.querySelector('div.form-group div');
      expect(loginError).withContext('You should have an error message if the login field is required and dirty').not.toBeNull();
      expect(loginError.textContent).withContext('The error message for the login field is incorrect').toBe('Login is required');

      const passwordInput = element.querySelector('input[name="password"]');
      expect(passwordInput).withContext('You should have an input with the name `password`').not.toBeNull();
      passwordInput.value = 'password';
      passwordInput.dispatchEvent(new Event('input'));
      passwordInput.value = '';
      passwordInput.dispatchEvent(new Event('input'));
      fixture.detectChanges();
      const passwordError = element.querySelector('div.form-group div');
      expect(passwordError).withContext('You should have an error message if the password field is required and dirty').not.toBeNull();
      expect(passwordError.textContent).withContext('The error message for the password field is incorrect').toBe('Login is required');
    });
  }));

  it('should call the user service and redirect if success', () => {
    const fixture = TestBed.createComponent(LoginComponent);

    fixture.detectChanges();

    const subject = new Subject<string>();
    fakeUserService.authenticate.and.returnValue(subject);

    const componentInstance = fixture.componentInstance;
    componentInstance.credentials.login = 'login';
    componentInstance.credentials.password = 'password';

    componentInstance.authenticate();

    // then we should have called the user service method
    expect(fakeUserService.authenticate).toHaveBeenCalledWith({
      login: 'login',
      password: 'password'
    });

    subject.next('');
    // and redirect to the home
    expect(componentInstance.authenticationFailed)
      .withContext('You should have a field `authenticationFailed` set to false if registration succeeded').toBe(false);
    expect(fakeRouter.navigate).toHaveBeenCalledWith(['/']);

  });

  it('should call the user service and display a message if failed', () => {
    const fixture = TestBed.createComponent(LoginComponent);

    fixture.detectChanges();

    const subject = new Subject<string>();
    fakeUserService.authenticate.and.returnValue(subject);

    const componentInstance = fixture.componentInstance;
    componentInstance.credentials.login = 'login';
    componentInstance.credentials.password = 'password';

    componentInstance.authenticate();

    // then we should have called the user service method
    expect(fakeUserService.authenticate).toHaveBeenCalledWith({
      login: 'login',
      password: 'password'
    });

    subject.error(new Error());
    // and not redirect to the home
    expect(fakeRouter.navigate).not.toHaveBeenCalled();
    expect(componentInstance.authenticationFailed)
      .withContext('You should have a field `authenticationFailed` set to true if registration failed').toBe(true);
  });

  it('should display a message if auth failed', () => {
    const fixture = TestBed.createComponent(LoginComponent);
    const componentInstance = fixture.componentInstance;
    componentInstance.authenticationFailed = true;

    fixture.detectChanges();

    const element = fixture.nativeElement;
    expect(element.querySelector('.alert'))
      .withContext('You should have a div with a class `alert` to display an error message')
      .not.toBeNull();
    expect(element.querySelector('.alert').textContent).toContain('Nope, try again');
  });
});
