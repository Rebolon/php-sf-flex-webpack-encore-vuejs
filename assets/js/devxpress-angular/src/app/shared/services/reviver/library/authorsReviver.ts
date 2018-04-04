import {Authors} from "../../../../../entities/library/authors"
import {JobReviver} from "./jobReviver";
import {AuthorReviver} from "./authorReviver";
import {ListAbstractReviver} from "@rebolon/json-reviver";
import {Injectable} from "@angular/core";

@Injectable()
export class AuthorsReviver extends ListAbstractReviver
{
    /**
     * @var JobReviver
     */
    protected jobReviver: JobReviver

    /**
     * @var AuthorReviver
     */
    protected authorReviver: AuthorReviver

    /**
     *
     * @param {JobReviver} jobReviver
     * @param {AuthorReviver} authorReviver
     */
    constructor (
        jobReviver: JobReviver,
        authorReviver: AuthorReviver
    ) {
        super()

        this.jobReviver = jobReviver
        this.authorReviver = authorReviver
    }

    /**
     *
     * @returns {string}
     */
    getNodeName(): string {
        return 'authors'
    }

    /**
     *
     * @returns {Object}
     */
    getNewEntity(): Object {
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
    public getEzPropsName()
    {
        return ['id', ]
    }

    /**
     * {@inheritdoc}
     */
    public getManyRelPropsName(): Object
    {
        return {}
    }

    /**
     * {@inheritdoc}
     */
    public getOneRelPropsName(): Object
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
