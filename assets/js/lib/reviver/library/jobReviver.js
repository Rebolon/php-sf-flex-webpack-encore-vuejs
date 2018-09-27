import {Job} from "../../entities/library/job"
import {ItemAbstractReviver} from "@rebolon/json-reviver";

export class JobReviver extends ItemAbstractReviver
{
    /**
     *
     * @returns {string}
     */
    getNodeName() {
        return 'job'
    }

    /**
     *
     * @returns {Object}
     */
    getNewEntity() {
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
    getEzPropsName()
    {
        return ['id', 'translationKey', ]
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
        return {}
    }
}
