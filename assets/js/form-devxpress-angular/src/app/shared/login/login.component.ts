import { Component } from '@angular/core';
import {host} from '../../../../../lib/config.js';
import {Router} from "@angular/router";
import {User} from "../services/user";

@Component({
  selector: 'my-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.scss'],
})
export class LoginComponent {
  public credentials = {
    login: '',
    password: ''
  };
  authenticationFailed = false;

  constructor(private router: Router, private userService: User) {
  }

  ngOnInit() {
  }

  authenticate() {
    this.authenticationFailed = false;
    this.userService.authenticate(this.credentials)
      .subscribe(
        () => this.router.navigate([this.getRedirectUri()]),
        () => this.authenticationFailed = true
      );
  }

  getRedirectUri() {
      return `http://${host}/demo/form/devxpress-angular`;
  }
}
