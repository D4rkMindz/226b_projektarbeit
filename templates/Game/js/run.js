const startup = new Startup();

$(async () => {
    startup.registerHandlers();
    let username = localStorage.getItem('username');
    const sessionKey = $('[data-id=game-id]').val();
    if (!username) {
        username = await startup.promptUsername()
    }
    await startup.loadGame(username, sessionKey);
});

function allowDrop(ev) {
    ev.preventDefault();
}

function drop(ev) {
    ev.preventDefault();
    const id = ev.dataTransfer.getData("text");
    ev.target.appendChild(document.getElementById(id));
    const ship = $(`[ship]#${id}`);
    const length = parseInt(ship.data('length'));
    ship.attr('style', `width: ${length * 30}px`);
    toggleStart();
}

function range(start, end) {
    if (start > end) {
        const t = end;
        end = start;
        start = t;
    }
    const list = [];
    for (let i = start; i <= end; i++) {
        list.push(i);
    }

    return list;
}

function toggleStart() {
    const result = $('[ship-container]:has([ship])');
    const btn = $('[data-id=start]');
    if (result.length > 0) {
        btn.addClass('hidden');
    } else {
        btn.removeClass('hidden');
    }
}