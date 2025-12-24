import { useBlockProps } from '@wordpress/block-editor';

export default function save() {
	return (
		<div { ...useBlockProps.save() }>
			<div className="smb-subscribe-form">
				<h3>Subscribe to Our Newsletter</h3>
				<div className="smb-form-group">
					<input
						type="email"
						className="smb-email-input"
						placeholder="Enter your email address"
						required
					/>
					<button
						type="button"
						className="smb-subscribe-button"
					>
						Subscribe Me
					</button>
				</div>
				<div className="smb-message" style={{ display: 'none' }}></div>
			</div>
		</div>
	);
}
