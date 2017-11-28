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
            fetch(uri)
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