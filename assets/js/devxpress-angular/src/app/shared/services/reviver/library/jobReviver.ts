import {Job} from "../../../../../entities/library/job"
import {ItemAbstractReviver} from "@rebolon/json-reviver";
import {Injectable} from "@angular/core";

@Injectable()
export class JobReviver extends ItemAbstractReviver
{
    /**
     *
     * @returns {string}
     */
    getNodeName(): string {
        return 'job'
    }

    /**
     *
     * @returns {Object}
     */
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
    public getEzPropsName()
    {
        return ['id', 'translationKey', ]
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
        return {}
    }
}
