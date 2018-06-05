const ACTIONS = {
    JOIN: 'join',
    HOST: 'host',
    PLACE_SHIP: 'place-ship',
    READY: 'ready',
    START: 'start',
    SHOT: 'shot',
    ERROR: 'error',
};

class Game {

    placeShip(dataId, startX, startY, endX, endY) {
    }

    removeShip(dataId, startX, startY, endX, endY) {
    }

    constructor(socket, username) {
        this.socket = socket;
        this.username = username;
        this.registerSockets();
        this.registerClick();
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

    registerClick() {
        $('[data-field]').on('click', function (event) {
            event.preventDefault();
            const row = $(this).data('row');
            const column = $(this).data('column');
            alert(`Clicked row ${row}, column ${column}`);
        });
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
        switch (data.type) {
            case ACTIONS.JOIN:
                alert('someone joined');
                console.log(data);
                break;
            case ACTIONS.HOST:
                const url = window.location.href + '/' + data.session_key;
                $('[data-id=invitation-link]').append(`<a href="${url}">${url}</a>`);
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
            case ACTIONS.ERROR:
                alert(data.message);
                console.log(data);
                break;
        }
    }
}