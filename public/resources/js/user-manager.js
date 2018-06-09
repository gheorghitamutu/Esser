var show = function (elem) {
	elem.style.display = 'grid';
};

var hide = function (elem) {
	elem.style.display = 'none';
};

var toggle = function (elem) {

	if (window.getComputedStyle(elem).display === 'grid') {
		hide(elem);
		return;
	}
	show(elem);

};

document.addEventListener('click', function (event) {

	if (!event.target.classList.contains('toggle')) return;
	event.preventDefault();
	var content = document.querySelector(event.target.hash);
	if (!content) return;
	toggle(content);

}, false);