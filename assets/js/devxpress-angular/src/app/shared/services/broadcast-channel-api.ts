import { Injectable} from '@angular/core'
import {Observable} from "rxjs/Observable";
import 'rxjs/add/operator/filter'
import {BehaviorSubject} from "rxjs/BehaviorSubject";
import {Subject} from "rxjs/Subject";

@Injectable()
export class BroadcastChannelApi {
    protected channel: BroadcastChannel

    protected _message: Subject<any> = new Subject()
    message: Observable<any> = this._message.asObservable().filter(value => Boolean(value))

    constructor() {
        console.info('SharedWorkerService', 'constructor')
        this.channel = new BroadcastChannel("second-screen")
        this.startListener()
    }

    /**
     *
     * @returns {Subject<any>}
     */
    protected startListener(): void {
        this.channel.onmessage = (ev) => {
            const data = ev.data

            if (!data) {
                throw new Error('message received without data')
            }

            if (!data.cmd) {
                throw new Error('message received without cmd')
            }

            switch (data.cmd) {
                case 'ping':
                    this.channel.postMessage({cmd: 'pong'})
                    break
                default:
            }

            this._message.next(data)
        }

        this.channel.onmessageerror = (ev) => {
            this._message.error({error: 'broadcast', msg: ev})
        }
    }

    ping() {
        const message = {cmd: "ping", message: "whatever"}
        this.channel.postMessage(message)
    }

    sayHello() {
        const message = {cmd: "hello", message: "whatever"}
        this.channel.postMessage(message)
    }
}
