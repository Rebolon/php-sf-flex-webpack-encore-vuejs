import {Book} from "../../entities/library/book"
import {ItemAbstractReviver} from "@rebolon/json-reviver/src";
import {AuthorsReviver} from "./authorsReviver";
import {SerieReviver} from "./serieReviver";
import {EditorsReviver} from "./editorsReviver";
import {Accessor} from "@rebolon/json-reviver/src/accessor";

export class BookReviver extends ItemAbstractReviver
{
    /**
     * @var AuthorsReviver
     */
    protected authorsReviver

    /**
     * @var EditorReviver
     */
    protected editorsReviver

    /**
     * @var SerieReviver
     */
    protected serieReviver

    /**
     *
     * @param {AuthorsReviver} authorsReviver
     * @param {EditorsReviver} editorsReviver
     * @param {SerieReviver} serieReviver
     */
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

    /**
     *
     * @returns {string}
     */
    getNodeName(): string {
        return 'book'
    }

    /**
     *
     * @returns {Object}
     */
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
    public getEzPropsName()
    {
        return ['id', 'title', 'description', 'indexInSerie', ]
    }

    /**
     * {@inheritdoc}
     */
    public getManyRelPropsName(): Object
    {
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
    public getOneRelPropsName(): Object
    {
        return {
            'serie': {
                'reviver': this.serieReviver,
                'registryKey': 'serie',
            },
        }
    }
}
