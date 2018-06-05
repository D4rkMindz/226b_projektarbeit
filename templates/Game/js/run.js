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
    const data = ev.dataTransfer.getData("text");
    ev.target.appendChild(document.getElementById(data));
}
