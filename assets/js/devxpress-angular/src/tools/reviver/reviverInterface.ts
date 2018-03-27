import {EntityInterface} from "./library/entityInterface";

export interface ReviverInterface
{
    /**
     *
     * @returns {string}
     */
    getNodeName(): string

    /**
     *
     * @returns EntityInterface
     */
    getNewEntity(): EntityInterface

    /**
     * @return string
     */
    getIdProperty(): string

    /**
     * List of accessible properties (int/string/date string converted into date from it's setter per exemple/date/boolean/...)
     *
     * @return array
     */
    getEzPropsName(): Array<string>

    /**
     * List of properties that contain sub-entities in a Many-To-Many ways
     *
     * @return array
     */
    getManyRelPropsName(): Object

    /**
     * List of properties that contain sub-entities in a Many-To-One way
     *
     * @return array
     */
    getOneRelPropsName(): Object

    /**
     * @param jsonOrArray
     * @param propertyPath
     * @return mixed array|EntityInterface
     */
    initFromRequest(jsonOrArray, propertyPath)
}
