import {Injectable} from '@angular/core';
import {Author} from '../../../../../entities/library/author';
import {ItemAbstractReviver} from '@rebolon/json-reviver';

@Injectable()
export class AuthorReviver extends ItemAbstractReviver
{
    getNodeName(): string {
        return 'author'
    }

   getNewEntity(): Object {
        return new Author()
    }

    /**
     * {@inheritdoc}
     * for this kind of json:
     * {
     *   "author": {
     *     "firstname": "Paul",
     *     "lastname": "Smith"
     *   }
     * }
     */
    public getEzPropsName() {
        return ['id', 'firstname', 'lastname', ]
    }

    public getManyRelPropsName(): Object {
        return {}
    }

    public getOneRelPropsName(): Object {
        return {}
    }
}
