( function ( blocks, element, blockEditor, components, serverSideRender ) {
	const el = element.createElement;
	const InspectorControls = blockEditor.InspectorControls;
	const PanelBody = components.PanelBody;
	const TextControl = components.TextControl;
	const TextareaControl = components.TextareaControl;
	const ServerSideRender = serverSideRender;

	blocks.registerBlockType( 'vinifera/contact-map', {
		edit: function ( props ) {
			const setAttributes = props.setAttributes;
			const attributes = props.attributes;

			return el( 'div', { className: 'vinifera-block-editor' },
				el( InspectorControls, {},
					el( PanelBody, { title: 'Mappa', initialOpen: true },
						el( TextControl, {
							label: 'Eyebrow',
							value: attributes.eyebrow,
							onChange: function ( value ) { setAttributes( { eyebrow: value } ); },
						} ),
						el( TextControl, {
							label: 'Titolo',
							value: attributes.title,
							onChange: function ( value ) { setAttributes( { title: value } ); },
						} ),
						el( TextareaControl, {
							label: 'Testo indirizzo',
							value: attributes.addressText,
							onChange: function ( value ) { setAttributes( { addressText: value } ); },
						} ),
						el( TextControl, {
							label: 'Testo segnaposto',
							value: attributes.pinText,
							onChange: function ( value ) { setAttributes( { pinText: value } ); },
						} ),
						el( TextControl, {
							label: 'Etichetta coordinate',
							value: attributes.coordsLabel,
							onChange: function ( value ) { setAttributes( { coordsLabel: value } ); },
						} ),
						el( TextControl, {
							label: 'Latitudine',
							value: attributes.latitude,
							onChange: function ( value ) { setAttributes( { latitude: value } ); },
						} ),
						el( TextControl, {
							label: 'Longitudine',
							value: attributes.longitude,
							onChange: function ( value ) { setAttributes( { longitude: value } ); },
						} ),
						el( TextControl, {
							label: 'Zoom',
							value: attributes.zoom,
							onChange: function ( value ) { setAttributes( { zoom: value } ); },
						} ),
						el( TextControl, {
							label: 'Testo link',
							value: attributes.linkLabel,
							onChange: function ( value ) { setAttributes( { linkLabel: value } ); },
						} ),
						el( TextControl, {
							label: 'URL link',
							value: attributes.linkUrl,
							onChange: function ( value ) { setAttributes( { linkUrl: value } ); },
						} )
					)
				),
				el( ServerSideRender, { block: 'vinifera/contact-map', attributes: attributes } )
			);
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.components, window.wp.serverSideRender );
