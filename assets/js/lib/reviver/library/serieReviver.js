import {Serie} from "../../entities/library/serie"
import {ItemAbstractReviver} from "@rebolon/json-reviver";

export class SerieReviver extends ItemAbstractReviver
{
    /**
     *
     * @returns {string}
     */
    getNodeName() {
        return 'serie'
    }

    /**
     *
     * @returns {Object}
     */
    getNewEntity() {
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
    public getManyRelPropsName()
    {
        // for instance i don't want to allow the creation of a serie with all embeded books, this is not a usual way of working
        // that's why i don't add books here
        return {}
    }

    /**
     * {@inheritdoc}
     */
    public getOneRelPropsName()
    {
        return {}
    }
}
