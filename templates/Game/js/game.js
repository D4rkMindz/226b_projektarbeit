const ACTIONS = {
    JOIN: 'join',
    JOIN_INFO: 'join-info',
    HOST: 'host',
    PLACE_SHIP: 'place-ship',
    REMOVE_SHIP: 'remove-ship',
    READY: 'ready',
    START: 'start',
    SHOT: 'shot',
    ERROR: 'error',
    LEAVE: 'leave',
};

const Game = function (socket, username) {

    this.constructor = (socket, username) => {
        this.socket = socket;
        this.username = username;
        this.myTurn = false;
        this.hasStarted = false;
        this.registerSockets();
        this.registerShot();
        console.log(`User joined the game as ${username}`);
    };

    this.placeShip = (id, x, y) => {
        const ship = $(`[ship]#${id}`);
        $(`.ship-${id}`).removeClass('ship-field').removeClass('ship-field-hit');

        const orientation = parseInt(ship.attr('orientation'));
        const length = ship.data('length');

        const startX = x;
        const startY = y;
        let endX = x;
        let endY = y;
        let shipRange;

        ship.removeAttr('style');
        // 0 = horizontal, 1 = vertical
        if (orientation === 1) {
            endX = x + length - 1;
            shipRange = range(x, endX);
            shipRange.forEach(function (el) {
                $(`[data-y="${y}"][data-x="${el}"]`).addClass('ship-field').addClass(`ship-${id}`);
            });
        } else {
            endY = y + length - 1;
            shipRange = range(y, endY);
            shipRange.forEach(function (el) {
                $(`[data-y="${el}"][data-x="${x}"]`).addClass('ship-field').addClass(`ship-${id}`);
            });
        }
        console.log(`Place Ship: dataId: ${id} ${startX}/${startY} ${endX}/${endY}`);
        const data = {
            startX: startX,
            startY: startY,
            endX: endX,
            endY: endY,
            id: id,
        };
        this.send(data, ACTIONS.PLACE_SHIP);
    };

    this.removeShip = (dataId) => {
        console.log(`Remove ship ${dataId}`);
        const data = {
            id: dataId,
        };
        this.send(data, ACTIONS.REMOVE_SHIP);
    };

    this.join = (gameId) => {
        if (!gameId) {
            console.log('no id provided')
        }
        const type = !!gameId ? ACTIONS.JOIN : ACTIONS.HOST;
        const data = {
            username: this.username,
            room_id: gameId,
        };
        this.send(data, type);
    };

    this.started = () => {
        return this.hasStarted;
    };

    this.registerShot = () => {
        const $this = this;
        $('[data-field]').on('click', function (event) {
            event.preventDefault();
            if (!$this.myTurn || !!$(this).attr('shot-fired')) {
                return;
            }
            const x = $(this).data('x');
            const y = $(this).data('y');

            const data = {
                x: x,
                y: y,
            };
            $this.send(data, ACTIONS.SHOT);
            $('.shootable').removeClass('shootable');
            $('[data-id=users-turn]>span').removeClass('hidden').text('Enemy');
            $this.myTurn = false;
        });
    };

    this.send = (data, type) => {
        data['type'] = type;
        this.socket.send(JSON.stringify(data));
    };

    this.registerSockets = () => {
        this.socket.onmessage = this.onMessage;
    };

    this.onMessage = (event) => {
        const $this = this;
        const data = JSON.parse(event.data);
        switch (data.type) {
            case ACTIONS.JOIN:
                alert(`${data['username']} joined`);
                console.log(data);
                break;
            case ACTIONS.JOIN_INFO:
                console.log(data);
                if (!!data['enemy_ready']) {
                    $('[data-id=enemy-status] > span').removeAttr('style').attr('style', 'color: green').text('Ready');
                }
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
                $('[data-id=enemy-status] > span').removeAttr('style').attr('style', 'color: green').text('Ready');
                break;
            case ACTIONS.START:
                $this.handleStart(data);
                break;
            case ACTIONS.SHOT:
                this.handleShot(data);
                break;
            case ACTIONS.LEAVE:
                alert(data['username'] + " left the game");
                break;
            case ACTIONS.ERROR:
                alert(data.message);
                console.log(data);
                break;
        }
    };

    this.handleStart = (data) => {
        if (data['beginner'] === this.username) {
            $('[data-id=users-turn]>span').removeClass('hidden').text('You');
            this.myTurn = true;
            $('[gamefield] td').addClass('shootable');
        }
        this.hasStarted = true;
        console.log(data);
    };

    this.handleShot = (data) => {
        console.log(data);
        const field = $(`[data-y=${data.y}][data-x=${data.x}]`);
        if (data['source'] !== this.username) {
            if (data['ship_status'] === 'hit') {
                field.addClass('ship-field-enemy-hit');
            } else if (data['ship_status'] === 'down') {
                field.addClass('ship-field-enemy-hit');
            } else {
                field.addClass('ship-field-enemy-missed');
            }
            $('[gamefield] td').addClass('shootable');
            $('[data-id=users-turn]>span').removeClass('hidden').text('You');
            this.myTurn = true;
        } else {
            $('.shootable').removeClass('shootable');
            $('[data-id=users-turn]>span').removeClass('hidden').text('Enemy');
            this.myTurn = false;
            field.attr('shot-fired','yes');
            if (data['ship_status'] === 'hit') {
                field.addClass('ship-field-you-hit');
            } else if (data['ship_status'] === 'down') {
                field.addClass('ship-field-you-hit');
                $(`[data-ship-id=${data['ship_id']}]`).css('color', 'white').css('background-color', 'red');
            } else {
                field.addClass('ship-field-you-missed');
            }
        }
        if (data['victory']) {
            this.myTurn = false;
            $('.shootable').removeClass('shootable');
            const elem = $('[data-id=game-victory-status]');
            if (data['winner'] === this.username) {
                elem.text('You Won!');
            } else {
                elem.text('You Lost!');
            }
        }
    };

    this.leave = () => {
        this.send({leave: true}, ACTIONS.LEAVE);
    };

    this.constructor(socket, username);
};