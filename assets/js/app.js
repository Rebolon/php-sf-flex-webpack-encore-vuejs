require('offline-plugin/runtime').install()

// you can also extract CSS - this will create a 'vendor.css' file
// this CSS will *not* be included in vuejs.css anymore
require('../css/app.scss')