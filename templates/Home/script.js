class Home {
	constructor() {
		setTimeout(() => {
			alert('JavaScript works');
		}, 1000);
	}
}

$(() => {
	new Home();
});