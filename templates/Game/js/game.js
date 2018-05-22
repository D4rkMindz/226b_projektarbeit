const ACTIONS = {
    JOIN: 'join',
    HOST: 'host',
    PLACE_SHIP: 'place-ship',
    READY: 'ready',
    START: 'start',
    SHOT: 'shot',
};

class Game {

    constructor(socket, username) {
        this.socket = socket;
        this.username = username;
        this.registerSockets();
        console.log(`User joined the game as ${username}`);
    }

    join(gameId) {
        if (!gameId) {
            console.log('no id provided')
        }
        const type = !!gameId ? ACTIONS.JOIN : ACTIONS.HOST;
        const data = {
            username: this.username,
            room_id: gameId,
        };
        this.send(data, type);
    }

    send(data, type) {
        data['type'] = type;
        this.socket.send(JSON.stringify(data));
    }

    registerSockets() {
        this.socket.onmessage = this.onMessage;
    }

    onMessage(event) {
        const data = JSON.parse(event.data);
        console.log(data);
        switch (data.type) {
            case ACTIONS.JOIN:
                alert('someone joined');
                console.log(data);
                break;
            case ACTIONS.HOST:
                console.log(data);
                break;
            case ACTIONS.PLACE_SHIP:
                console.log(data);
                break;
            case ACTIONS.READY:
                console.log(data);
                break;
            case ACTIONS.START:
                console.log(data);
                break;
            case ACTIONS.SHOT:
                console.log(data);
                break;
        }
    }
}