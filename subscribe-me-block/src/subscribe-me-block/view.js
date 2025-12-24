/**
 * Frontend JavaScript for Subscribe Me Block
 * Handles email validation, AJAX submission, and user feedback
 */

document.addEventListener('DOMContentLoaded', function() {
	// Get all subscribe form blocks on the page
	const subscribeForms = document.querySelectorAll('.wp-block-smb-subscribe-me-block');
	
	subscribeForms.forEach(function(formContainer) {
		const emailInput = formContainer.querySelector('.smb-email-input');
		const subscribeButton = formContainer.querySelector('.smb-subscribe-button');
		const messageDiv = formContainer.querySelector('.smb-message');
		
		if (!emailInput || !subscribeButton || !messageDiv) {
			return;
		}
		
		// Handle subscribe button click
		subscribeButton.addEventListener('click', function(e) {
			e.preventDefault();
			
			const email = emailInput.value.trim();
			
			// Clear previous messages
			messageDiv.style.display = 'none';
			messageDiv.textContent = '';
			messageDiv.className = 'smb-message';
			
			// Validate email
			if (!email) {
				showMessage('Please enter your email address.', 'error');
				return;
			}
			
			// Basic email validation regex
			const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
			if (!emailRegex.test(email)) {
				showMessage('Please enter a valid email address.', 'error');
				return;
			}
			
			// Disable button and show loading state
			subscribeButton.disabled = true;
			subscribeButton.textContent = 'Subscribing...';
			
			// Send AJAX request
			const formData = new FormData();
			formData.append('action', 'smb_subscribe');
			formData.append('email', email);
			formData.append('nonce', smbAjax.nonce);
			
			fetch(smbAjax.ajaxUrl, {
				method: 'POST',
				body: formData
			})
			.then(response => response.json())
			.then(data => {
				if (data.success) {
					showMessage(data.data.message, 'success');
					emailInput.value = '';
				} else {
					showMessage(data.data.message, 'error');
				}
			})
			.catch(error => {
				showMessage('An error occurred. Please try again.', 'error');
				console.error('Subscription error:', error);
			})
			.finally(() => {
				// Re-enable button
				subscribeButton.disabled = false;
				subscribeButton.textContent = 'Subscribe Me';
			});
		});
		
		// Allow Enter key to submit
		emailInput.addEventListener('keypress', function(e) {
			if (e.key === 'Enter') {
				subscribeButton.click();
			}
		});
		
		function showMessage(message, type) {
			messageDiv.textContent = message;
			messageDiv.className = 'smb-message smb-message-' + type;
			messageDiv.style.display = 'block';
		}
	});
});
