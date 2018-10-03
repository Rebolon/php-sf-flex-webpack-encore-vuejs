import { Injectable} from '@angular/core'
import {Observable, forkJoin, Subject, BehaviorSubject} from "rxjs";
import {filter, mergeMap} from "rxjs/operators"
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

        /**
         * The chosen pattern is the following:
         *  * the first api call will retreive a Book and will expose only own properties
         *  * for each relationship properties (authors, editors, ...) it will do further api call
         *  * some api call needs more to be complete (authors needs author AND role)
         *  * on each response of the su calls it will add extra info into main book
         *
         * This is one pattern from many existing ones ;-)
         *
         */
        this.api
            .get(`/books/${id}`)
            .subscribe((book) => {
                // revive a lite book
                const bookToRevive = (({id, title, description, indexInSerie}) => ({id, title, description, indexInSerie}))(book)
                const revivedBook = this.bookReviver.main(bookToRevive)

                this._book.next(revivedBook)
                res.next(revivedBook)

                if (book.editors.length) {
                    for (let edition of book.editors) {
                        this.getEdition(id)
                            .subscribe((book: Book) => {
                                this._book.next(book)
                                res.next(book)
                            })
                    }
                }

                if (book.authors.length) {
                    for (let author of book.authors) {
                        this.getAuthors(id)
                            .subscribe((book: Book) => {
                                this._book.next(book)
                                res.next(book)
                            })
                    }
                }
            }, err => {
                res.error(err)
            })

        return res
    }

    getEdition(id: number) {
        const res = new Subject()

        this.api
            .get(`/project_book_editions/${id}`)
            .mergeMap(edition => {
                return this.api
                    .get(edition.editor)
                    .map(editor => {
                        edition.editor = editor

                        return edition
                    })
            })
            .subscribe((edition) => {
                const book = this._book.getValue()
                const revivedEdition = this.editorsReviver.main(edition).pop()
                book.addEdition(revivedEdition)

                this._book.next(book)
                res.next(book)
            }, err => {
                res.error(err)
            })

        return res
    }

    getAuthors(id: number) {
        const res = new Subject()

        this.api
            .get(`/project_book_creations/${id}`)
            .mergeMap(authors => {
                return forkJoin(
                    this.api
                        .get(authors.author)
                        .map(author => {
                            authors.author = author

                            return authors
                        }),
                    this.api
                        .get(authors.role)
                        .map(role => {
                            authors.role = role

                            return authors
                        })
                )
            })
            .subscribe((authors) => {
                const book = this._book.getValue()
                const revivedAuthors = this.authorsReviver.main(authors).pop()
                book.addAuthor(revivedAuthors)

                this._book.next(book)
                res.next(book)
            }, err => {
                res.error(err)
            })

        return res
    }
}
