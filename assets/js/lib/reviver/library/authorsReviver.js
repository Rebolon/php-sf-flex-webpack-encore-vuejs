import {Authors} from "../../entities/library/authors"
import {JobReviver} from "./jobReviver";
import {AuthorReviver} from "./authorReviver";
import {ListAbstractReviver} from "@rebolon/json-reviver";

export class AuthorsReviver extends ListAbstractReviver
{
    /**
     *
     * @param {JobReviver} jobReviver
     * @param {AuthorReviver} authorReviver
     */
    constructor (
        jobReviver,
        authorReviver
    ) {
        super()

        this.jobReviver = jobReviver
        this.authorReviver = authorReviver
    }

    /**
     *
     * @returns {string}
     */
    getNodeName() {
        return 'authors'
    }

    /**
     *
     * @returns {Object}
     */
    getNewEntity() {
        return new Authors()
    }

    /**
     * {@inheritdoc}
     * for this kind of json:
     * {
     *   "author": {
     *     "job": { ... },
     *     "author": { ... },
     *   }
     * }
     */
    getEzPropsName()
    {
        return ['id', ]
    }

    /**
     * {@inheritdoc}
     */
    getManyRelPropsName()
    {
        return {}
    }

    /**
     * {@inheritdoc}
     */
    getOneRelPropsName()
    {
        return {
            'role': {
                'reviver': this.jobReviver,
                'registryKey': 'role'
            },
            'author': {
                'reviver': this.authorReviver,
                'registryKey': 'author'
            }
        }
    }
}
