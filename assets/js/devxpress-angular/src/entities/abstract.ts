import {CacheKey} from "../app/wizard-container/enums/cache-key";

export abstract class EntityAbstract {
    // @todo how to type generics for Type params ?
    static initialize(type, cacheKey?: CacheKey) {
        let objToRestore = new type()

        if (!cacheKey) {
            return objToRestore
        }

        const cache = localStorage.getItem(cacheKey)
        if (cache) {
            objToRestore = type.initializeFromJson(objToRestore, cache)
        }

        return objToRestore
    }

    static initializeFromJson(type, cache) {
        let objToRestore
        const typeOf = typeof type

        switch (typeOf) {
            case 'object':
                objToRestore = type
                break
            case 'function':
                objToRestore = new type()
                break
            default:
                throw new Error('type must be a function or an object')
        }

        if (!cache) {
            return objToRestore
        }

        const cacheToRestore = JSON.parse(cache)

        // @todo the problem is to restore nested entity, for instance it's just json object, but it lacks all
        // domain code for those entities ex. book has editor => book will have a property editor but not an Object
        // editor !!! maybe the sytem used in ParamConverter on the backend could be reproducable here
        for (let prop in cacheToRestore) {
            objToRestore[prop] = cacheToRestore[prop]
        }

        return objToRestore
    }
}