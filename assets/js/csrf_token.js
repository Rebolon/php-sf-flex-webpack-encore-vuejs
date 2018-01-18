import axios from 'axios'

export default function getToken(loaderToActivate) {
    let meta = document.querySelector('head meta[name=csrf_token]')

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
        meta.setAttribute('name', 'csrf_token')
        document.querySelector('head').appendChild(meta)
    }
    meta.setAttribute('content', token)
}
