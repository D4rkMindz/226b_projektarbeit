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
            event.originalEvent.dataTransfer.setData("text", event.target.id);
            $this.game.removeShip();
        });
        $('[ship]').on('click', function (event) {
            event.currentTarget.classList.toggle('rotate');
        });
        $('[data-field]').on('drop', function (event) {
            const id = $('[ship]').attr('id');
            $this.game.placeShip(id);
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