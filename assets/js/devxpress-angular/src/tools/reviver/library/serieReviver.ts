import {Serie} from "../../../entities/library/serie"
import {ItemAbstractReviver} from '../itemAbstractReviver'

export class SerieReviver extends ItemAbstractReviver
{
    /**
     *
     * @returns {string}
     */
    getNodeName(): string {
        return 'serie'
    }

    /**
     *
     * @returns {Object}
     */
    getNewEntity(): Object {
        return new Serie()
    }

    /**
     * {@inheritdoc}
     * for this kind of json:
     * {
     *   "serie": {
     *     "name": "The serie name"
     *   }
     * }
     */
    public getEzPropsName()
    {
        return ['id', 'name', ]
    }

    /**
     * {@inheritdoc}
     */
    public getManyRelPropsName(): Object
    {
        // for instance i don't want to allow the creation of a serie with all embeded books, this is not a usual way of working
        // that's why i don't add books here
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
