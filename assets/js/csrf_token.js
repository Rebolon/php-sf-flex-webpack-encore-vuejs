export default function getToken(loaderToActivate) {
    let meta = document.querySelector('head meta[name=csrf_token]')

    return new Promise((resolve, reject) => {
        if (loaderToActivate) {
            loaderToActivate.isLoading = true
        }

        const uri = '/token'
        const myHeaders = new Headers()
        myHeaders.append('Accept', 'application/json')
        myHeaders.append('Content-Type', 'application/json')
        const myInit = {
            method: 'GET',
            headers: myHeaders,
            credentials: 'same-origin',
            mode: 'cors',
            cache: 'no-cache',
        }
        fetch(uri, myInit)
        // @todo manage http error
            .then(res => res.json()).then(res => {
            changeMeta(meta, res)
            if (loaderToActivate) {
                loaderToActivate.isLoading = false
            }
            resolve(res)
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
