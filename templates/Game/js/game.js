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

    placeShip(id, row, column) {
        const ship = $(`[ship]#${id}`);
        $(`.ship-${id}`).removeClass('ship-field').removeClass('ship-field-hit');

        const orientation = parseInt(ship.attr('orientation'));
        const length = ship.data('length');

        const startX = row;
        const startY = column;
        let endX = row;
        let endY = column;
        let shipRange;

        ship.removeAttr('style');
        // 0 = horizontal, 1 = vertical
        if (orientation === 1) {
            endX = row + length - 1;
            shipRange = range(row, endX);
            shipRange.forEach(function (el) {
                $(`[data-column="${column}"][data-row="${el}"]`).addClass('ship-field').addClass(`ship-${id}`);
            });
        } else {
            endY = column + length - 1;
            shipRange = range(column, endY);
            shipRange.forEach(function (el) {
                $(`[data-column="${el}"][data-row="${row}"]`).addClass('ship-field').addClass(`ship-${id}`);
            });
        }
        console.log(`Place Ship: dataId: ${id} ${startX}/${startY} ${endX}/${endY}`);
    }

    removeShip(dataId) {
        console.log(`Remove ship ${dataId}`);
    }

    constructor(socket, username) {
        this.socket = socket;
        this.username = username;
        this.registerSockets();
        this.registerShot();
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

    registerShot() {
        const $this = this;
        $('[data-field]').on('click', function (event) {
            event.preventDefault();
            const row = $(this).data('row');
            const column = $(this).data('column');
            // alert(`Clicked row ${row}, column ${column}`);

            const data = {
                x: column,
                y: row,
            };
            $this.send(data, ACTIONS.SHOT);
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