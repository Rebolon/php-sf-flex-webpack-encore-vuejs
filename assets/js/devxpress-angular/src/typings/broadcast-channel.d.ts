// https://html.spec.whatwg.org/multipage/web-messaging.html#broadcastchannel
// https://developer.mozilla.org/nl/docs/Web/API/BroadcastChannel


interface BroadcastChannelEvent<Message> extends MessageEvent {
    data: Message;
}

declare class BroadcastChannel<Message=any> extends EventTarget {
    /**
     * Returns a new BroadcastChannel object via which messages for the given channel name can be sent and received.
     */
    constructor(name: string);
    /**
     * Returns the channel name (as passed to the constructor).
     */
    readonly name: string;
    /**
     * Sends the given message to other BroadcastChannel objects set up for this channel.
     * @param message - Messages can be structured objects, e.g. nested objects and arrays.
     */
    postMessage: (message: Message) => void;
    onmessage: (message: BroadcastChannelEvent<Message>) => void;
    onmessageerror: (message: BroadcastChannelEvent<Message>) => void;
    /**
     * Closes the BroadcastChannel object, opening it up to garbage collection.
     */
    close(): void;
}

interface Window {

    /**
     * Returns a new BroadcastChannel object via which messages for the given channel name can be sent and received.
     */
    BroadcastChannel: new<Message>(name: string) => BroadcastChannel<Message>;
}
