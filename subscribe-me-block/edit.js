import { __ } from '@wordpress/i18n';
import { useBlockProps } from '@wordpress/block-editor';
import './editor.scss';

export default function Edit() {
	return (
		<div { ...useBlockProps() }>
			<div className="smb-subscribe-form">
				<h3>{ __( 'Subscribe to Our Newsletter', 'subscribe-me-block' ) }</h3>
				<div className="smb-form-group">
					<input
						type="email"
						className="smb-email-input"
						placeholder={ __( 'Enter your email address', 'subscribe-me-block' ) }
						disabled
					/>
					<button
						type="button"
						className="smb-subscribe-button"
						disabled
					>
						{ __( 'Subscribe Me', 'subscribe-me-block' ) }
					</button>
				</div>
				<p className="smb-editor-note">
					{ __( 'Preview: Subscription form (active on frontend)', 'subscribe-me-block' ) }
				</p>
			</div>
		</div>
	);
}
