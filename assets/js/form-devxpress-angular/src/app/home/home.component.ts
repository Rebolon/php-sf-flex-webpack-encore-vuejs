import { Component } from '@angular/core'
import {host} from '../../../../lib/config.js'

@Component({
  selector: 'my-home',
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.scss']
})
export class HomeComponent {
  checkToken() {
      const rememberMe = localStorage.getItem('rememberMe')

      return rememberMe
  }

  getRedirectUri() {
      return `http://${host}/demo/form/devxpress-angular`
  }
}
