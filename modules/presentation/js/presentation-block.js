(() => {

	const blocks = document.querySelectorAll('.re-mgr-presentation');

	blocks.forEach(block => {
		const tabs = block.querySelectorAll('.re-mgr-tab-btn');

		tabs.forEach(tab => {
			tab.addEventListener('click', event => {
				tabs.forEach(tab => {
					tab.classList.remove('active-tab');
				});
				event.target.classList.add('active-tab');
			});
		});
	});

})();
