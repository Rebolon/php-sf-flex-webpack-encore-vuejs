self.addEventListener("install", () => {
    self.skipWaiting()
})

// code from assets/js/devxpress-angular/src/tools/shared-worker.js
let clients = new Array()
let nextId = clients.length
const broadcast = (clients, message) => {
    let length = clients.length
    for (let i = 0; i < length; i++) {
        let port = clients[i]
        port.postMessage(message)
    }
}

self.addEventListener("connect", function (e) {
    let port = e.ports[0]
    nextId++
    clients[nextId].push({id: nextId, port: port})
    port.addEventListener("message", function (e) {
        let data = e.data;

        if (!data.cmd) {
            // how to debug shared workers ?
            console.error('no cmd defined in message')
            return
        }

        if (!data.id) {
            // how to debug shared workers ?
            console.error('no id defined in message')
            return
        }

        switch(data.type) {
            case "ping":
                const client = clients.find(client => client.id === data.id)
                if (client) {
                    data.message = "pong"
                    client.port.postMessage(data)
                } else {
                    data.message = `unknown client ${client.id}`
                    broadcast(clients, data)
                }
                break
            default:
                broadcast(clients, data);
                break
        }
    })

    // should we move it at bottom or at top ?
    port.start();

    broadcast(clients, {"id":nextId, "cmd": "connected"});

}, false)

