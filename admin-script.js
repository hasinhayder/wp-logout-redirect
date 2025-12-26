/**
 * WP Logout Redirect - Admin Panel JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
	'use strict';

	// Elements
	const urlInput = document.getElementById('wplr_redirect_url');
	const pageSelector = document.getElementById('wplr_page_selector');
	const presetButtons = document.querySelectorAll('.wplr-preset-btn');
	const copyButton = document.querySelector('.wplr-copy-btn');
	const codeContent = document.querySelector('.wplr-code-content code');
	const previewUrl = document.querySelector('.wplr-preview-url');

	// Store initial preview URL (the default fallback)
	let defaultPreviewUrl = wplrData.homeUrl || '';
	if (previewUrl) {
		// Get the text content before the SVG
		const textNode = previewUrl.childNodes[0];
		if (textNode && textNode.nodeType === Node.TEXT_NODE) {
			defaultPreviewUrl = textNode.textContent.trim();
		}
	}

	/**
	 * Update URL preview when input changes
	 */
	function updatePreview() {
		if (!previewUrl || !urlInput) return;

		const url = urlInput.value.trim();
		const displayUrl = url || defaultPreviewUrl;

		// Get the SVG icon
		const svg = previewUrl.querySelector('svg');
		previewUrl.innerHTML = '';
		previewUrl.appendChild(document.createTextNode(displayUrl));
		if (svg) {
			previewUrl.appendChild(svg);
		}

		// Update href
		previewUrl.href = displayUrl;
	}

	/**
	 * Sync page selector with URL input
	 * When URL matches a page, select it in dropdown
	 */
	function syncPageSelector() {
		if (!pageSelector || !urlInput) return;

		const currentUrl = urlInput.value.trim();
		let matchingOption = null;

		// Find matching option
		for (let i = 0; i < pageSelector.options.length; i++) {
			if (pageSelector.options[i].value === currentUrl) {
				matchingOption = pageSelector.options[i];
				break;
			}
		}

		// Select matching option or reset to first (empty) option
		if (matchingOption) {
			pageSelector.value = currentUrl;
		} else {
			pageSelector.selectedIndex = 0;
		}
	}

	/**
	 * Handle page selector changes
	 */
	if (pageSelector) {
		pageSelector.addEventListener('change', function() {
			const selectedUrl = this.value;

			if (selectedUrl) {
				// Update URL input with selected page's URL
				urlInput.value = selectedUrl;
			} else {
				// Clear if "Select a page..." is chosen
				urlInput.value = '';
			}

			// Update preview
			updatePreview();
		});
	}

	/**
	 * Handle preset button clicks
	 */
	presetButtons.forEach(button => {
		button.addEventListener('click', function() {
			const targetUrl = this.dataset.url;

			if (this.classList.contains('wplr-preset-clear')) {
				// Clear input to use default
				urlInput.value = '';
			} else {
				// Set preset URL
				urlInput.value = targetUrl;
			}

			// Update preview and sync page selector
			updatePreview();
			syncPageSelector();

			// Visual feedback
			this.style.transform = 'scale(0.96)';
			setTimeout(() => {
				this.style.transform = '';
			}, 150);

			// Focus input for further editing
			urlInput.focus();
		});
	});

	/**
	 * Copy code to clipboard
	 */
	if (copyButton && codeContent) {
		copyButton.addEventListener('click', async function() {
			const code = codeContent.textContent;
			let success = false;

			// Try Clipboard API first
			if (navigator.clipboard && navigator.clipboard.writeText) {
				try {
					await navigator.clipboard.writeText(code);
					success = true;
				} catch (err) {
					console.error('Clipboard API failed:', err);
				}
			}

			// Fallback for older browsers or non-secure contexts
			if (!success) {
				try {
					const textArea = document.createElement("textarea");
					textArea.value = code;
					
					// Ensure textarea is not visible but part of DOM
					textArea.style.position = "fixed";
					textArea.style.left = "-9999px";
					textArea.style.top = "0";
					document.body.appendChild(textArea);
					
					textArea.focus();
					textArea.select();
					
					success = document.execCommand('copy');
					document.body.removeChild(textArea);
				} catch (err) {
					console.error('Fallback copy failed:', err);
				}
			}

			if (success) {
				// Success feedback
				this.classList.add('copied');

				// Change icon to checkmark
				this.innerHTML = `
					<svg width="14" height="14" viewBox="0 0 14 14" fill="none">
						<path d="M11.666 3.5L5.25 9.91667L2.333 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
				`;

				// Reset after delay
				setTimeout(() => {
					this.classList.remove('copied');
					this.innerHTML = `
						<svg width="14" height="14" viewBox="0 0 14 14" fill="none">
							<rect x="2" y="2" width="8" height="8" rx="1" stroke="currentColor" stroke-width="1.5"/>
							<path d="M5 9V11C5 11.8 5.2 12 6 12H11C11.8 12 12 11.8 12 11V6C12 5.2 11.8 5 11 5H9" stroke="currentColor" stroke-width="1.5"/>
						</svg>
					`;
				}, 2000);
			}
		});
	}

	/**
	 * Live preview updates on input
	 */
	if (urlInput) {
		let debounceTimer;
		urlInput.addEventListener('input', function() {
			clearTimeout(debounceTimer);
			debounceTimer = setTimeout(() => {
				updatePreview();
				syncPageSelector();
			}, 150);
		});
	}

	/**
	 * Enhanced submit button with loading state
	 */
	const submitButton = document.querySelector('.wplr-submit-btn');
	const form = document.querySelector('.wplr-form');

	if (submitButton && form) {
		form.addEventListener('submit', function() {
			const btnText = submitButton.querySelector('.wplr-btn-text');
			const btnIcon = submitButton.querySelector('.wplr-btn-icon');
			const originalText = btnText.textContent;

			// Loading state
			submitButton.disabled = true;
			btnText.textContent = wplrData.saving || 'Saving...';
			submitButton.style.opacity = '0.7';

			// Spin the icon
			if (btnIcon) {
				btnIcon.style.animation = 'wplrSpin 0.8s linear infinite';
			}
		});
	}

	// Initial sync on page load
	if (urlInput && pageSelector) {
		syncPageSelector();
	}
});