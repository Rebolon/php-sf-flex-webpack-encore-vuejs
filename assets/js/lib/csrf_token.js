import axios from 'axios'
import { csrf_parameter } from './config'

const metaKey = csrf_parameter

export const getTokenFromMeta = function () {
    let meta = document.querySelector(`head meta[name=${metaKey}]`)

    return meta ? meta.getAttribute('content') : undefined;
}

export default function getToken(loaderToActivate) {
    let meta = getTokenFromMeta()

    return new Promise((resolve, reject) => {
        if (loaderToActivate) {
            loaderToActivate.isLoading = true
        }

        const uri = '/token'
        axios.get(uri)
            .then(res => {
                changeMeta(meta, res.data)
                resolve(res.data)
            })
            .catch(err => {
                reject(err)
            })
            .finally(() => {
                if (loaderToActivate) {
                    loaderToActivate.isLoading = false
                }
            })
    })
}

const changeMeta = function(meta, token) {
    if (!meta) {
        meta = document.createElement('meta')
        meta.setAttribute('name', metaKey)
        document.querySelector('head').appendChild(meta)
    }
    meta.setAttribute('content', token)
}
