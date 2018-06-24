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
            toggleStart();
        });
        $('[ship]').on('click', function (event) {
            if ($this.game.started()) {
                return;
            }
            event.currentTarget.classList.toggle('rotate');

            const id = event.currentTarget.id;
            const ship = $(`[ship]#${id}`);
            const orientation = parseInt(ship.attr('orientation'));
            if (orientation === 1) {
                ship.attr('orientation', 0);
            } else {
                ship.attr('orientation', 1);
            }

            const x = $(event.currentTarget.parentElement).data('x') || 0;
            const y = $(event.currentTarget.parentElement).data('y') || 0;
            $this.game.removeShip(id);
            $this.game.placeShip(id, x, y);
            toggleStart();
        });
        $('[data-field]').on('drop', function (event) {
            const id = event.originalEvent.dataTransfer.getData('text');
            const x = $(this).data('x');
            const y = $(this).data('y');

            $this.game.placeShip(id, x, y);
            toggleStart();
        });
        $(window).on('beforeunload', function (e) {
            e.preventDefault();
            const reload = window.confirm('Confirm reload') || true;
            if (reload) {
                $this.game.leave();
            }
            return reload;
        });

        $('[data-id=start]').on('click', (event) => {
            event.preventDefault();
            const willStart = confirm('Are you really ready?');
            if (!willStart) {
                return;
            }
            const data = {
                type: ACTIONS.READY,
            };
            this.socket.send(JSON.stringify(data));
            $(this).addClass('hidden');
            $('[ship]').attr('draggable', false);
            console.log('Starting game...');
            $('[data-id=self-status] > span').removeAttr('style').attr('style', 'color: green').text('Ready');
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