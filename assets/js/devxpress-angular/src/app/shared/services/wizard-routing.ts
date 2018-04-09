import {Injectable} from '@angular/core'
import {Observable} from "rxjs/Observable";
import 'rxjs/add/operator/filter'
import {Subject} from "rxjs/Subject";

@Injectable()
export class WizardRouting {
    private _cancel: Subject<string> = new Subject()
    cancel: Observable<any> = this._cancel.asObservable()

    private _back: Subject<string> = new Subject()
    back: Observable<any> = this._back.asObservable()

    private _next: Subject<string> = new Subject()
    next: Observable<any> = this._next.asObservable()

    private _end: Subject<string> = new Subject()
    end: Observable<any> = this._end.asObservable()

    goCancel(ev: Event) {
        ev.preventDefault()
        this._cancel.next()
    }

    goBack(ev: Event) {
        ev.preventDefault()
        this._back.next()
    }

    goNext(ev: Event) {
        ev.preventDefault()
        this._next.next()
    }

    goEnd(ev: Event) {
        ev.preventDefault()
        this._end.next()
    }
}
