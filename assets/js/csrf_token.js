export default function getToken(loaderToActivate)
{
    let meta = document.querySelector('head meta[name=csrf_token]')
    let metaContent = meta ? meta.getAttribute('content') : ''

    return new Promise((resolve, reject) => {
        if (metaContent) {
            resolve(metaContent)
        } else {
            loaderToActivate.isLoading = true
            const uri = '/token'
            const myHeaders = new Headers()
            myHeaders.append("Accept", "application/json")
            myHeaders.append("Content-Type", "application/json")
            const myInit = {
              method: 'GET',
              headers: myHeaders,
              credentials: 'same-origin',
              mode: 'cors',
              cache: 'no-cache',
            }
            fetch(uri, myInit)
            // @todo manage http error
                .then(res => res.json())
                .then(res => {
                    metaContent = res
                    if (!meta) {
                        meta = document.createElement('meta')
                        meta.setAttribute('name', 'csrf_token')
                        document.querySelector('head').appendChild(meta)
                    }
                    meta.setAttribute('content', res)
                    loaderToActivate.isLoading = false
                    resolve(metaContent)
                })
        }
    })
}