class Startup {
    constructor() {
        this.host = window.location.hostname;
        this.socket = new WebSocket('ws://' + this.host + ':8000');
        this.game = null;
    }

    async loadGame(username, sessionId) {
        if (this.socket.readyState !== 1) {
            await this.waitForConnection(500);
        }
        localStorage.setItem('username', username);
        $('[data-id=display-username]').append(username);

        this.game = new Game(this.socket, username);
        this.game.join(sessionId);
    }

    promptUsername() {
        return new Promise(resolve => {
            const modal = $('#username');
            modal.modal({backdrop: 'static', keyboard: false});
            modal.modal('toggle');
            $('[data-id=accept-username]').on('click', (event) => {
                event.preventDefault();
                const username = $('[data-id=username]').val();
                if (!username) {
                    $('#username-error').append('Required');
                    return;
                }
                resolve(username);
                modal.modal('hide');
            });
        });
    }

    registerHandlers() {
        const $this = this;
        $('[ship]').on('dragstart', function (event) {
            const id = event.target.id;
            event.originalEvent.dataTransfer.setData("text", id);
            $(`.ship-${id}`).removeClass('ship-field').removeClass('ship-field-hit');
            $this.game.removeShip(id);
        });
        $('[ship]').on('click', function (event) {
            event.currentTarget.classList.toggle('rotate');

            const id = event.currentTarget.id;
            const ship = $(`[ship]#${id}`);
            const orientation = parseInt(ship.attr('orientation'));
            if (orientation === 1) {
                ship.attr('orientation', 0);
            } else {
                ship.attr('orientation', 1);
            }

            const row = $(event.currentTarget.parentElement).data('row');
            const column = $(event.currentTarget.parentElement).data('column');

           $this.game.placeShip(id, row, column);
        });
        $('[data-field]').on('drop', function (event) {
            const id = event.originalEvent.dataTransfer.getData('text');
            const row = $(this).data('row');
            const column = $(this).data('column');

            $this.game.placeShip(id, row, column);
        });
        $(window).on('beforeunload', function (e) {
            e.preventDefault();
            return window.confirm('Confirm reload');
        });

        $('[data-id=emit-test]').on('click', (event) => {
            event.preventDefault();
            const data = {
                type: ACTIONS.SHOT,
                x: 1,
                y: 1,
            };
            this.socket.send(JSON.stringify(data))
        });
    }

    waitForConnection(interval) {
        return new Promise((resolve) => {
            setInterval(() => {
                if (this.socket.readyState === 1) {
                    clearInterval();
                    resolve();
                }
            }, interval);
        });
    }
}