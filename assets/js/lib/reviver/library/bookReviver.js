import {Book} from "../../entities/library/book"
import {ItemAbstractReviver, Accessor} from "@rebolon/json-reviver";
import {AuthorsReviver} from "./authorsReviver";
import {SerieReviver} from "./serieReviver";
import {EditorsReviver} from "./editorsReviver";

export class BookReviver extends ItemAbstractReviver
{
    /**
     *
     * @param {AuthorsReviver} authorsReviver
     * @param {EditorsReviver} editorsReviver
     * @param {SerieReviver} serieReviver
     */
    constructor (
        authorsReviver,
        editorsReviver,
        serieReviver
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
    getNodeName() {
        return 'book'
    }

    /**
     *
     * @returns {Object}
     */
    getNewEntity() {
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
    getEzPropsName()
    {
        return ['id', 'title', 'description', 'indexInSerie', ]
    }

    /**
     * {@inheritdoc}
     */
    getManyRelPropsName()
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
    getOneRelPropsName()
    {
        return {
            'serie': {
                'reviver': this.serieReviver,
                'registryKey': 'serie',
            },
        }
    }
}
