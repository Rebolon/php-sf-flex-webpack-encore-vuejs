import { Component } from '@angular/core';
import {host} from '../../../../../lib/config.js';

@Component({
  selector: 'my-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.scss'],
})
export class LoginComponent {
  checkToken() {
      const rememberMe = localStorage.getItem('rememberMe');

      return rememberMe;
  }

  getRedirectUri() {
      return `http://${host}/demo/form/devxpress-angular`;
  }
}
