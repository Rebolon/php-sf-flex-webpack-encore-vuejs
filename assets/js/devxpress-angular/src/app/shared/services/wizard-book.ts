import { Injectable} from '@angular/core'
import {Observable} from "rxjs/Observable";
import 'rxjs/add/operator/filter'
import {Subject} from "rxjs/Subject";
import {BehaviorSubject} from "rxjs/BehaviorSubject";
import {Book} from "../../../entities/library/book";
import {Editors} from "../../../entities/library/editors";
import {Authors} from "../../../entities/library/authors";
import {ApiService} from '../../../services/api';
import {apiConfig} from "../../../../../lib/config";
import {options} from "../tools/form-options";
import {BookReviver} from "./reviver/library/bookReviver";
import {EditorsReviver} from "./reviver/library/editorsReviver";
import {AuthorsReviver} from "./reviver/library/authorsReviver";

@Injectable()
export class WizardBook {
    private _book: BehaviorSubject<Book> = new BehaviorSubject<Book>(null)
    book: Observable<any> = this._book.asObservable().filter(value => Boolean(value))
    protected apiConfig
    protected options = options

    constructor(private api: ApiService, private bookReviver: BookReviver, private editorsReviver: EditorsReviver, private authorsReviver: AuthorsReviver) {
      this.apiConfig = apiConfig
    }

    createBook(book: Book) {
        this._book.next(book)
    }

    updateBook(book: Book) {
        const currentBook = this._book.getValue()

        // only replace property from the new book
        Object.keys(book).forEach(prop => currentBook[prop] = book[prop])
        const newBook = this.bookReviver.main(currentBook)
        this._book.next(newBook)
    }

    setEditors(editors: Array<Editors>) {
        const book = this._book.getValue()
        book.setEdition(this.editorsReviver.main(editors))
        this._book.next(book)
    }

    addEditors(editors: Editors) {
        const book = this._book.getValue()
        book.addEdition(this.editorsReviver.main(editors))
        this._book.next(book)
    }

    setAuthors(authors: Array<Authors>) {
        const book = this._book.getValue()
        book.setAuthors(this.authorsReviver.main(authors))
        this._book.next(book)
    }

    addAuthors(authors: Authors) {
        const book = this._book.getValue()
        book.addAuthor(this.authorsReviver.main(authors))
        this._book.next(book)
    }

    /**
     *
     * @returns {Subject<any>}
     */
    save() {
        const res = new Subject()
        const book = this._book.getValue()

        // clean useless props
        // @todo there is an error in the ParamConverter if the item is empty !!!
        // should be useless
        if (book.editors && !book.editors.length) {
            delete book.editors
        }

        if (book.authors && !book.authors.length) {
            delete book.authors
        }
        // end of useless code (normally)

        const body = {
        book: book
        }
        if (book.id) {
        this.api
            .put('/booksiu/special_3', JSON.stringify(body))
            .subscribe(() => {
                res.next(book)
            }, err => {
                res.error(err)
            })
        } else {
        this.api
            .post('/booksiu/special_3', JSON.stringify(body))
            .subscribe(newBook => {
                book.id = newBook.id
                this._book.next(book)
                res.next(book)
            }, err => {
                res.error(err)
            })
        }

        return res
    }

    /**
     *
     * @returns {this}
     */
    reset() {
        const book = new Book()

        this._book.next(book)

        return this
    }

    /**
     *
     * @param {number} id
     * @returns {Subject<any>}
     */
    get(id: number) {
        const res = new Subject()

        this.api
            .get(`/books/${id}`)
            .subscribe((book) => {
                this._book.next(book)
                res.next(book)
            }, err => {
                res.error(err)
            })

        return res
    }
}
