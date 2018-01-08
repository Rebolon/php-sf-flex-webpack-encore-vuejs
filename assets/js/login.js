export default function isLoggedIn(loaderToActivate)
{
    return new Promise((resolve, reject) => {
        if (loaderToActivate && loaderToActivate.isLoading) {
          loaderToActivate.isLoading = true
        }

        const uri = '/demo/login/isloggedin'
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
            .then(res => {
                if (res.status !== 200) {
                  resetLoginInfo()
                  reject()

                  return
                }

                res.json()
            })
            .then(res => {
                if (loaderToActivate && loaderToActivate.isLoading) {
                  loaderToActivate.isLoading = false
                }

                resolve(true)
            })
    })
}

export const resetLoginInfo = function() {
  localStorage.removeItem('isLoggedIn')
}
