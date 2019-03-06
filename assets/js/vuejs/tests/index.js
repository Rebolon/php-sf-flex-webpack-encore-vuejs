const testsContext = require.context(".", true, /\.spec$/)

//console.log(testsContext);

testsContext.keys().forEach(testsContext)
