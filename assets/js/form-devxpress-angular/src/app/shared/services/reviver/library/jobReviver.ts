import {Job} from '../../../../../entities/library/job';
import {ItemAbstractReviver} from '@rebolon/json-reviver';
import {Injectable} from '@angular/core';

@Injectable()
export class JobReviver extends ItemAbstractReviver
{
    getNodeName(): string {
        return 'job'
    }

    getNewEntity(): Object {
        return new Job()
    }

    /**
     * {@inheritdoc}
     * for this kind of json:
     * {
     *   "role": {
     *     "translationKey": 'WRITER'
     *   }
     * }
     */
    public getEzPropsName() {
        return ['id', 'translationKey', ]
    }

    public getManyRelPropsName(): Object {
        return {}
    }

    /**
     * {@inheritdoc}
     */
    public getOneRelPropsName(): Object {
        return {}
    }
}
