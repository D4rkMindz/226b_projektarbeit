const host = window.location.hostname;
const socket = new WebSocket('ws://' + host + ':8000');

$(() => {
    const modal = $('#username');
    modal.modal({backdrop: 'static', keyboard: false})
    modal.modal('toggle');
    $('[data-id=accept-username]').on('click', (event) => {
        event.preventDefault();
        const username = $('[data-id=username]').val();
        if (!username) {
            $('#username-error').append('Required');
            return;
        }
        $('[data-id=display-username]').append(username);
        const game = new Game(socket, username);
        const gameId = $('[data-id=game-id]').val();
        game.join(gameId);
        modal.modal('hide');
    });
    $('[data-id=emit-test]').on('click', (event) => {
        event.preventDefault();
        const data = {
            type: ACTIONS.SHOT,
            x: 1,
            y: 1,
        };
        socket.send(JSON.stringify(data))
    })
});
