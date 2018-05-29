$(async () => {
    const startup = new Startup();
    startup.registerHandlers();
    let username = localStorage.getItem('username');
    const sessionKey = $('[data-id=game-id]').val();
    if (!username) {
        username = await startup.promptUsername()
    }
    await startup.loadGame(username, sessionKey);
});