import {Authors} from '../../../../../entities/library/authors';
import {JobReviver} from './jobReviver';
import {AuthorReviver} from './authorReviver';
import {ListAbstractReviver} from '@rebolon/json-reviver';
import {Injectable} from '@angular/core';

@Injectable({
  providedIn: 'root'
})
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

    constructor (
        jobReviver: JobReviver,
        authorReviver: AuthorReviver
    ) {
        super()

        this.jobReviver = jobReviver
        this.authorReviver = authorReviver
    }

    getNodeName(): string {
        return 'authors'
    }

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
    public getEzPropsName() {
        return ['id', ]
    }

    public getManyRelPropsName(): Object {
        return {}
    }

    public getOneRelPropsName(): Object {
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
