((Drupal) => {
	Drupal.behaviors.re_mgr_visualization = {
		attach: function (context, settings) {
			const blocks = document.querySelectorAll('.re-mgr-presentation');

			blocks.forEach(block => {
				const svgContainer = block.querySelector('.visualization-svg-container');

				if (svgContainer) {
					const svg = svgContainer.querySelector('svg');
					const entityKeyword = svg.dataset.entityKeyword;
					const svgPaths = svg.querySelectorAll('path');
					const backBtn = svgContainer.querySelector('.back-btn');
					const sellEntity = svg.dataset.sellEntityKeyword;
					const navigationContainer = svgContainer.querySelector('.navigation-container');

					if (entityKeyword === 'flat') {
						svgContainer.style.paddingBottom = '80px';
					}

					svgPaths.forEach(function(element) {
						once('visualization', element).forEach(function(element) {
							/* Hover & move event on stage with status. */
							if (
								element.classList[0] !== 'sold' &&
								(sellEntity === 'building' && entityKeyword === 'estate') ||
								(sellEntity === 'flat' && entityKeyword === 'floor')
							) {
								/* Mouseover event */
								element.addEventListener('mouseover', event => {

									const targetOpacity = event.target.parentNode.dataset.pathTargetOpacity;
									event.target.setAttribute('fill-opacity', targetOpacity);
									const flatId = event.target.dataset.relatedEntityId;
									const relatedTooltip = svgContainer.querySelector('#entity-id-' + flatId);

									if (relatedTooltip) {
										relatedTooltip.style.display = "inline-block";
									}
								});
								/* Mousemove event */
								element.addEventListener('mousemove', event => {
									const flatId = event.target.dataset.relatedEntityId;
									const relatedTooltip = svgContainer.querySelector('#entity-id-' + flatId);
									const clientY = event.clientY;
									const clientX = event.clientX;

									if (relatedTooltip) {
										relatedTooltip.style.top = clientY + 20 + 'px';
										relatedTooltip.style.left = clientX + 20 + 'px';
									}
								});

								/* Mouseout event */
								element.addEventListener('mouseout', event => {
									const targetOpacity = event.target.parentNode.dataset.pathTargetOpacity;
									event.target.setAttribute('fill-opacity', targetOpacity);
									const flatId = event.target.dataset.relatedEntityId;
									const relatedTooltip = svgContainer.querySelector('#entity-id-' + flatId);

									if (relatedTooltip) {
										relatedTooltip.style.display = "none";
									}
								});
							}
							/* Hover event standard stage. */
							else if (
								(sellEntity === 'building' && entityKeyword !== 'estate') ||
								(sellEntity === 'flat' && entityKeyword !== 'floor')
							) {
								element.addEventListener('mouseover', event => {
									const targetOpacity = event.target.parentNode.dataset.pathTargetOpacity;
									event.target.setAttribute('fill-opacity', targetOpacity);
								});

								element.addEventListener('mouseout', event => {
									event.target.setAttribute('fill-opacity', 0);
								});
							}

							/* Click event */
							if (element.classList[0] !== 'sold') {
								element.addEventListener('click', event => {
									const path = event.target;
									const svg = path.parentNode;
									const frontUrl = svgContainer.dataset.frontUrl;
									const blockId = svgContainer.dataset.blockId;
									const currentEntityKeyword = svg.dataset.entityKeyword;
									const chosenEntityId = path.dataset.relatedEntityId;
									const pathFill = svg.dataset.pathFill;
									const pathTargetOpacity = svg.dataset.pathTargetOpacity;
									const startingEntityKeyword = svg.dataset.startingEntityKeyword;
									const sellEntityKeyword = svg.dataset.sellEntityKeyword;
									const webformId = svg.dataset.webformId;
									const imageStyle = svg.dataset.imageStyle;
									let url = '/';

									if (frontUrl !== '') {
										url = `${frontUrl}`;
									}

									url += `re-mgr-visualization/next/${blockId}/${currentEntityKeyword}/${chosenEntityId}/${pathFill}/${pathTargetOpacity}/${startingEntityKeyword}/${sellEntityKeyword}/${webformId}`;

									if (imageStyle) {
										url += `/${imageStyle}`;
									}

									Drupal.ajax({url: url}).execute();
								});
							}
						});
					});

					/* Go back event */
					if (backBtn) {
						once('visualization', backBtn).forEach(function(element) {
							element.addEventListener('click', event => {
								const svg = event.target.parentNode.querySelector('svg');
								const frontUrl = svgContainer.dataset.frontUrl;
								const blockId = svgContainer.dataset.blockId;
								const currentEntityKeyword = svg.dataset.entityKeyword;
								const currentEntityId = svg.dataset.entityId;
								const pathFill = svg.dataset.pathFill;
								const pathTargetOpacity = svg.dataset.pathTargetOpacity;
								const startingEntityKeyword = svg.dataset.startingEntityKeyword;
								const sellEntityKeyword = svg.dataset.sellEntityKeyword;
								const webformId = svg.dataset.webformId;
								const imageStyle = svg.dataset.imageStyle;
								let url = '/';

								if (frontUrl !== '') {
									url = `${frontUrl}`;
								}

								url += `re-mgr-visualization/prev/${blockId}/${currentEntityKeyword}/${currentEntityId}/${pathFill}/${pathTargetOpacity}/${startingEntityKeyword}/${sellEntityKeyword}/${webformId}`;

								if (imageStyle) {
									url += `/${imageStyle}`;
								}

								Drupal.ajax({url: url}).execute();
							});
						});
					}

					/* Navigation handler */
					if (navigationContainer) {
						const selects = navigationContainer.querySelectorAll('select');

						once('visualization', selects).forEach(function(element) {
							element.addEventListener('change', function(event) {
								const svg = svgContainer.querySelector('svg');
								const frontUrl = svgContainer.dataset.frontUrl;
								const blockId = svgContainer.dataset.blockId;
								const selectedEntityKeyword = event.target.classList[0];
								const selectedEntityId = this.value;
								const pathFill = svg.dataset.pathFill;
								const pathTargetOpacity = svg.dataset.pathTargetOpacity;
								const startingEntityKeyword = svg.dataset.startingEntityKeyword;
								const sellEntityKeyword = svg.dataset.sellEntityKeyword;
								const webformId = svg.dataset.webformId;
								const imageStyle = svg.dataset.imageStyle;

								let url = '/';

								if (frontUrl !== '') {
									url = `${frontUrl}`;
								}

								url += `re-mgr-visualization/change/${blockId}/${selectedEntityKeyword}/${selectedEntityId}/${pathFill}/${pathTargetOpacity}/${startingEntityKeyword}/${sellEntityKeyword}/${webformId}`;

								if (imageStyle) {
									url += `/${imageStyle}`;
								}

								Drupal.ajax({url: url}).execute();
							});
						});
					}
				}
			});
		}
	};
})(Drupal);
