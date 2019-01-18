import {Book} from '../../../../../entities/library/book';
import {ItemAbstractReviver} from '@rebolon/json-reviver';
import {AuthorsReviver} from './authorsReviver';
import {SerieReviver} from './serieReviver';
import {EditorsReviver} from './editorsReviver';
import {Accessor} from '@rebolon/json-reviver';
import {Injectable} from '@angular/core';

@Injectable({
  providedIn: 'root'
})
export class BookReviver extends ItemAbstractReviver
{
    /**
     * @var AuthorsReviver
     */
    protected authorsReviver: AuthorsReviver

    /**
     * @var EditorReviver
     */
    protected editorsReviver: EditorsReviver

    /**
     * @var SerieReviver
     */
    protected serieReviver: SerieReviver

    constructor (
        authorsReviver: AuthorsReviver,
        editorsReviver: EditorsReviver,
        serieReviver: SerieReviver
    ) {
        super()

        this.authorsReviver = authorsReviver
        this.editorsReviver = editorsReviver
        this.serieReviver = serieReviver
    }

    getNodeName(): string {
        return 'book'
    }

    getNewEntity(): Object {
        return new Book()
    }

    /**
     * {@inheritdoc}
     * for this kind of json:
     * {
     *   "book": {
     *     "title": "The green lantern",
     *     "description": "Whatever you want",
     *     "index_in_serie": 15
     *   }
     * }
     */
    public getEzPropsName() {
        return ['id', 'title', 'description', 'indexInSerie', ]
    }

    public getManyRelPropsName(): Object {
        // for instance i don't want to allow the creation of reviews with all embeded reviews, this is not a usual way of working
        // that's why i don't add reviews here
        return {
            'authors': {
                'reviver': this.authorsReviver,
                'setter': 'addAuthor',
                'cb': function (relation, entity) {
                    Accessor('book', relation, entity)
                },
            },
            'editors': {
                'reviver': this.editorsReviver,
                'setter': 'addEdition',
                'cb': function (relation, entity) {
                    Accessor('book', relation, entity)
                },
            },
        }
    }

    /**
     * {@inheritdoc}
     *
     * registryKey could be used if we create an endpoint that allow batch POST/PUT of book with embedded serie
     */
    public getOneRelPropsName(): Object {
        return {
            'serie': {
                'reviver': this.serieReviver,
                'registryKey': 'serie',
            },
        }
    }
}
