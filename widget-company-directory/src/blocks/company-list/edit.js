/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * WordPress dependencies
 */
import {
	useBlockProps,
	InspectorControls,
	RichText
} from '@wordpress/block-editor';
import {
	PanelBody,
	SelectControl,
	Button,
	Spinner,
	Notice
} from '@wordpress/components';
import { useState, useEffect } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';

/**
 * Internal dependencies
 */
import './editor.css';

export default function Edit({ attributes, setAttributes }) {
	const { title, selectedCompanies } = attributes;
	const [companies, setCompanies] = useState([]);
	const [isLoading, setIsLoading] = useState(true);
	const [error, setError] = useState(null);

	// Fetch companies from REST API
	useEffect(() => {
		setIsLoading(true);
		apiFetch({ path: '/wp/v2/company?per_page=100' })
			.then((data) => {
				setCompanies(data);
				setIsLoading(false);
			})
			.catch(() => {
				setError(__('Failed to load companies.', 'widget-company-directory'));
				setIsLoading(false);
			});
	}, []);

	const addCompany = (id) => {
		if (!selectedCompanies.includes(id)) {
			setAttributes({ selectedCompanies: [...selectedCompanies, id] });
		}
	};

	const removeCompany = (id) => {
		setAttributes({
			selectedCompanies: selectedCompanies.filter((cid) => cid !== id),
		});
	};

	const blockProps = useBlockProps();

	return (
		<div {...blockProps}>
			<InspectorControls>
				<PanelBody title={__('Company List Settings', 'widget-company-directory')}>
					{isLoading && <Spinner />}
					{error && <Notice status="error">{error}</Notice>}

					{!isLoading && companies.length > 0 ? (
						<SelectControl
							label={__('Add a company', 'widget-company-directory')}
							value=""
							options={[
								{ label: __('Select a company', 'widget-company-directory'), value: '' },
								...(companies?.map((c) => ({
									label: c.title.rendered,
									value: c.id,
								})) || []),
							]}
							onChange={(val) => {
								if (val) addCompany(parseInt(val));
							}}
						/>
					) : (
						!isLoading && (
							<Notice status="info">
								{__('No companies found. Make sure you have created some Company posts.', 'widget-company-directory')}
							</Notice>
						)
					)}
				</PanelBody>
			</InspectorControls>

			<RichText
				tagName="h3"
				value={title}
				className="company-list-title"
				onChange={(newTitle) => setAttributes({ title: newTitle })}
				placeholder={__('Add list title...', 'widget-company-directory')}
			/>

			<div className="recommended-list">
				{selectedCompanies.length === 0 && (
					<p>{__('No companies selected yet.', 'widget-company-directory')}</p>
				)}
				<ul>
					{selectedCompanies.map((id) => {
						const company = companies.find((c) => c.id === id);
						return (
							<li key={id}>
								{company ? company.title.rendered : __('Unknown Company', 'widget-company-directory')}
								<Button
									isDestructive
									variant="link"
									onClick={() => removeCompany(id)}
									className="remove-company-btn"
								>
									{__('Remove', 'widget-company-directory')}
								</Button>
							</li>
						);
					})}
				</ul>
			</div>
		</div>
	);
}