import {ReviverInterface} from './reviverInterface'
import {EntityInterface} from "./library/entityInterface"
import {accessor} from "../accessor";

const registry = []
const propertyPath: Array<any> = []

/**
 * @todo there is maybe a way to mutualize the 3 methods buildWith*
 *
 * Class AbstractConverter
 */
export abstract class ItemAbstractReviver implements ReviverInterface {
    /**
     * Default name of the id property
     * @var string
     */
    protected idProperty = 'id'

    /**
     * main entry point
     * {@inheritdoc}
     */
    main(content) {
        if (!content) {
            return false
        }

        let raw
        try {
            const json = typeof content == 'string' ? JSON.parse(content) : content
            raw = this.initFromRequest(json, this.getNodeName())
        } catch (e) {
            throw e
        }

        return raw
    }

    /**
     * this is for the tsc compiler because it considers it should implements ReviverInterface methods
     * @returns {Array<string>}
     */
    abstract getEzPropsName(): Array<string>

    /**
     * this is for the tsc compiler because it considers it should implements ReviverInterface methods
     * @returns {Array<string>}
     */
    abstract getManyRelPropsName(): Object

    /**
     * this is for the tsc compiler because it considers it should implements ReviverInterface methods
     * @returns {Array<string>}
     */
    abstract getOneRelPropsName(): Object

    /**
     * this is for the tsc compiler because it considers it should implements ReviverInterface methods
     * @returns {string}
     */
    getNodeName(): string {
        console.log('toto')
        return 'toto'
    }

    /**
     * this is for the tsc compiler because it considers it should implements ReviverInterface methods
     * @returns {Object}
     */
    abstract getNewEntity(): EntityInterface

    /**
     * @inheritdoc
     */
    public getIdProperty(): string {
        return this.idProperty
    }

    /**
     * @inheritdoc
     */
    public initFromRequest(jsonOrArray, propertyPathToAdd) {
        try {
            propertyPath.push(propertyPathToAdd)

            const json = this.checkJsonOrArray(jsonOrArray)

            const entity = this.buildEntity(json)

            propertyPath.pop()

            return entity
        } catch (e) {
            throw e
        }
    }

    /**
     * @param json
     * @return mixed
     * @throws RuntimeException
     * @throws \TypeError
     */
    protected buildEntity(json) {
        const entity = this.getNewEntity()

        this.buildWithEzProps(json, entity)
        this.buildWithManyRelProps(json, entity)
        this.buildWithOneRelProps(json, entity)

        return entity
    }

    /**
     * Used for simple property that is not linked to other entities with relation like ManyTo OneTo...
     *
     * @param array json
     * @param EntityInterface entity
     * @return EntityInterface
     */
    private buildWithEzProps(json: Array<any>, entity: EntityInterface): EntityInterface {
        const ezProps = this.getEzPropsName()
        for (let prop of ezProps) {
            if (!json.hasOwnProperty(prop)
                || json[prop] === null) {
                console.info(`Unknown property ${prop} for node ${this.getNodeName()}`)
                continue
            }

            accessor(prop, entity, json)
        }

        return entity
    }

    /**
     * @param array json
     * @param EntityInterface entity
     * @return EntityInterface
     * @throws RuntimeException
     */
    private buildWithManyRelProps(json: Array<any>, entity: EntityInterface): EntityInterface {
        const relManyProps = this.getManyRelPropsName()
        for (let prop in relManyProps) {
            if (!json.hasOwnProperty(prop)
                || json[prop] === null) {
                console.info(`Unknown property ${prop} for node ${this.getNodeName()}`)
                continue
            }

            let operationsInfo = relManyProps[prop]

            this.checkOperationsInfo(operationsInfo, 'getManyRelPropsName')

            const relations = operationsInfo['converter'].initFromRequest(json[prop], prop)

            // I don't fond a quick way to use the propertyAccessor so i keep this for instance
            const methodName = operationsInfo.hasOwnProperty('setter') ? operationsInfo['setter'] : null
            for (let relation of relations) {
                if (operationsInfo.hasOwnProperty('cb')) {
                    if (typeof operationsInfo['cb'] != 'function') {
                        throw new Error('cb in operations info must be callable (a function)')
                    }

                    operationsInfo['cb'](relation, entity)
                }

                if (methodName) {
                    // the setter is a function or just a prop name ?
                    if (typeof entity[methodName] == 'function') {
                        entity[methodName](relation)
                    } else {
                        entity[methodName] = relation
                    }
                } else {
                    try {
                        accessor(prop, entity, relation)
                    } catch (e) {
                        // @todo manage this with a log + a report to user with explanation on what have not been processed
                    }
                }
            }
        }

        return entity
    }

    /**
     * @todo: if json is an object : creation, if it's a string : retreive the entity with doctrine and add it to entity
     *
     * @param array json
     * @param EntityInterface entity
     * @return EntityInterface
     *
     * @throws RuntimeException
     */
    private buildWithOneRelProps(json: Array<any>, entity: EntityInterface): EntityInterface {
        const oneRelProps = this.getOneRelPropsName()
        for (let prop in oneRelProps) {
            if (!json.hasOwnProperty(prop)
                || json[prop] === null) {
                console.info(`Unknown property ${prop} for node ${this.getNodeName()}`)
                continue
            }

            const operationsInfo = oneRelProps[prop]
            this.checkOperationsInfo(operationsInfo, 'getOneRelPropsName')

            const relation = operationsInfo['converter'].initFromRequest(json[prop], prop)
            const relationRegistered = this.useRegistry(relation, operationsInfo)

            if (operationsInfo.hasOwnProperty('setter')) {
                const methodName = operationsInfo['setter']
                if (!entity.hasOwnProperty(methodName)) {
                    throw new Error(`methodName (${methodName}) must exists in entity ${this.getNodeName()}`)
                }

                entity[methodName](relationRegistered)
            } else {
                try {
                    accessor(prop, entity, relationRegistered)
                } catch (e) {
                    // @todo manage this with a log + a report to user with explanation on what have not been processed
                }
            }
        }

        return entity
    }

    /**
     * @param jsonOrArray
     * @return mixed
     * @throws ValidationException
     */
    protected checkJsonOrArray(jsonOrArray) {
        let json
        try {
            json = typeof jsonOrArray == 'string' ? JSON.parse(jsonOrArray) : jsonOrArray
            if (!json) {
                throw new Error
            }
        } catch (e) {
            const itemName = Object.values(propertyPath).pop()

            throw new Error(`jsonOrArray for ${itemName} must be a String or Array`)
        }

        return json
    }

    /**
     * @param operationsInfo
     * @param methodName
     * @throws RuntimeException
     */
    protected checkOperationsInfo(operationsInfo, methodName): void {
        if (!operationsInfo.hasOwnProperty('converter')) {
            throw new Error(`Library ParamConverter::{methodName} must return an associative array
                 with the key as the Entity props name also used in HTTP Request Json node, and the value must contain
                 an array with converter key, and a setter if you don\'t want to use default propertyAccess`)
        }

        // instanceof doesn't exists in typescript, so i just check the initFromRequest
        if (typeof operationsInfo['converter'] != 'object'
            || typeof operationsInfo['converter'].initFromRequest != 'function') {
            throw new Error('converter should be an object that implements ConverterInterface')
        }
    }

    /**
     * @param relation
     * @param operationsInfo
     * @return mixed
     */
    protected useRegistry(relation, operationsInfo) {
        if (operationsInfo.hasOwnProperty('registryKey')) {
            if (!registry.hasOwnProperty(operationsInfo['registryKey'])) {
                registry[operationsInfo['registryKey']] = []
            }

            const serialized = JSON.stringify(relation)
            if (registry[operationsInfo['registryKey']].hasOwnProperty(serialized)) {
                relation = registry[operationsInfo['registryKey']][serialized]
            } else {
                registry[operationsInfo['registryKey']][serialized] = relation
            }
        }

        return relation
    }

    /**
     * @return string
     */
    protected getPropertyPath(): string {
        const raw = propertyPath.join('.')

        return raw.replace('.[', '[')
    }

    /**
     * for ManyToMany reviver, they need to access it
     * @returns {Array<any>}
     */
    protected getPropertyPathContent(): Array<any> {
        return propertyPath
    }
}
