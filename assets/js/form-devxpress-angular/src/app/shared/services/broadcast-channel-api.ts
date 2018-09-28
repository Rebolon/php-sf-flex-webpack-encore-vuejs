import { Injectable} from '@angular/core'
import {Observable, Subject, BehaviorSubject} from 'rxjs';
import {filter} from 'rxjs/operators'
import {BookModel} from '../../../models/book.model';
import {Book} from '../../../entities/library/book';

@Injectable()
export class BroadcastChannelApi {
    protected channel: BroadcastChannel

    protected _message: Subject<any> = new Subject()
    message: Observable<any> = this._message.asObservable().pipe(filter(value => Boolean(value)))

    constructor() {
        console.info('SharedWorkerService', 'constructor')
        this.channel = new BroadcastChannel('second-screen');
        this.startListener()
    }

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
        const message = {cmd: 'ping', message: 'whatever'}
        this.channel.postMessage(message)
    }

    sayHello() {
        const message = {cmd: 'hello', message: 'whatever'}
        this.channel.postMessage(message)
    }

    sendBook(book: Book) {
        const message = {cmd: 'book', message: 'new Book', book: book}
        this.channel.postMessage(message)
    }
}
