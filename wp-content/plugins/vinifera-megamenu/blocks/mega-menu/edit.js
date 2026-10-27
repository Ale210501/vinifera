( function ( wp ) {
	const registerBlockType = wp.blocks.registerBlockType;
	const el = wp.element.createElement;
	const __ = wp.i18n.__;
	const useBlockProps = wp.blockEditor.useBlockProps;
	const InspectorControls = wp.blockEditor.InspectorControls;
	const ServerSideRender = wp.serverSideRender;
	const PanelBody = wp.components.PanelBody;
	const TextControl = wp.components.TextControl;
	const Button = wp.components.Button;

	const DEFAULTS = {
		label: 'Rossi',
		url: '#',
		col1Title: 'Regione',
		col1Items: 'Sicilia|#\nPuglia|#\nCampania|#\nSardegna|#\nVedi tutti|#',
		col2Title: 'Tipologia',
		col2Items: 'Valpolicella|#\nNebbiolo|#\nBrunello|#\nAmarone|#\nVedi tutti|#',
		col3Title: 'Abbinamento',
		col3Items: 'Carni bianche|#\nVini da grigliata|#\nCarni rosse|#\nFormaggi|#\nVedi tutti|#',
	};

	function parseItems( raw ) {
		if ( ! raw ) {
			return [];
		}

		return raw
			.split( /\r\n|\r|\n/ )
			.map( function ( line ) {
				const trimmed = ( line || '' ).trim();
				if ( ! trimmed ) {
					return null;
				}

				const parts = trimmed.split( '|' );
				const label = ( parts[ 0 ] || '' ).trim();
				const url = ( parts.slice( 1 ).join( '|' ) || '#' ).trim();

				if ( ! label ) {
					return null;
				}

				return {
					label: label,
					url: url || '#',
				};
			} )
			.filter( Boolean );
	}

	function stringifyItems( items ) {
		return items
			.map( function ( item ) {
				const label = ( item.label || '' ).trim();
				const url = ( item.url || '#' ).trim();
				if ( ! label ) {
					return null;
				}

				return label + '|' + ( url || '#' );
			} )
			.filter( Boolean )
			.join( '\n' );
	}

	function renderItemRows( attrKey, current, setAttributes ) {
		const parsed = parseItems( current[ attrKey ] );
		const items = parsed.length ? parsed : [ { label: '', url: '#' } ];

		function updateItem( index, field, value ) {
			const next = items.map( function ( item, itemIndex ) {
				if ( itemIndex !== index ) {
					return item;
				}

				const changed = Object.assign( {}, item );
				changed[ field ] = value;
				return changed;
			} );

			setAttributes( ( function () {
				const attrs = {};
				attrs[ attrKey ] = stringifyItems( next );
				return attrs;
			} )() );
		}

		function removeItem( index ) {
			const next = items.filter( function ( _item, itemIndex ) {
				return itemIndex !== index;
			} );

			setAttributes( ( function () {
				const attrs = {};
				attrs[ attrKey ] = stringifyItems( next );
				return attrs;
			} )() );
		}

		function addItem() {
			const next = items.concat( [ { label: __( 'Nuova voce', 'sayweb-megamenu' ), url: '#' } ] );

			setAttributes( ( function () {
				const attrs = {};
				attrs[ attrKey ] = stringifyItems( next );
				return attrs;
			} )() );
		}

		return [].concat(
			items.map( function ( item, index ) {
				return el(
					'div',
					{ key: attrKey + '-row-' + index, style: { marginBottom: '12px' } },
					el( TextControl, {
						label: __( 'Label', 'sayweb-megamenu' ) + ' ' + ( index + 1 ),
						value: item.label,
						onChange: function ( value ) {
							updateItem( index, 'label', value );
						},
					} ),
					el( TextControl, {
						label: __( 'URL', 'sayweb-megamenu' ) + ' ' + ( index + 1 ),
						value: item.url,
						onChange: function ( value ) {
							updateItem( index, 'url', value );
						},
					} ),
					el(
						Button,
						{
							isDestructive: true,
							onClick: function () {
								removeItem( index );
							},
						},
						__( 'Rimuovi voce', 'sayweb-megamenu' )
					)
				);
			} ),
			el(
				Button,
				{ key: attrKey + '-add', variant: 'secondary', onClick: addItem },
				__( 'Aggiungi voce', 'sayweb-megamenu' )
			)
		);
	}

	function MegaMenuEdit( props ) {
		const attributes = props.attributes;
		const setAttributes = props.setAttributes;
		const current = Object.assign( {}, DEFAULTS, attributes );
		const blockProps = useBlockProps( { className: 'vinifera-mega-menu-editor' } );

		return el(
			'div',
			blockProps,
			el(
				InspectorControls,
				null,
				el(
					PanelBody,
					{ title: __( 'Voce Menu', 'sayweb-megamenu' ), initialOpen: true },
					el( TextControl, {
						label: __( 'Label', 'sayweb-megamenu' ),
						value: current.label,
						onChange: function ( value ) {
							setAttributes( { label: value } );
						},
					} ),
					el( TextControl, {
						label: __( 'URL', 'sayweb-megamenu' ),
						value: current.url,
						onChange: function ( value ) {
							setAttributes( { url: value } );
						},
					} )
				),
				el(
					PanelBody,
					{ title: __( 'Colonna 1', 'sayweb-megamenu' ), initialOpen: false },
					el( TextControl, {
						label: __( 'Titolo', 'sayweb-megamenu' ),
						value: current.col1Title,
						onChange: function ( value ) {
							setAttributes( { col1Title: value } );
						},
					} ),
					renderItemRows( 'col1Items', current, setAttributes )
				),
				el(
					PanelBody,
					{ title: __( 'Colonna 2', 'sayweb-megamenu' ), initialOpen: false },
					el( TextControl, {
						label: __( 'Titolo', 'sayweb-megamenu' ),
						value: current.col2Title,
						onChange: function ( value ) {
							setAttributes( { col2Title: value } );
						},
					} ),
					renderItemRows( 'col2Items', current, setAttributes )
				),
				el(
					PanelBody,
					{ title: __( 'Colonna 3', 'sayweb-megamenu' ), initialOpen: false },
					el( TextControl, {
						label: __( 'Titolo', 'sayweb-megamenu' ),
						value: current.col3Title,
						onChange: function ( value ) {
							setAttributes( { col3Title: value } );
						},
					} ),
					renderItemRows( 'col3Items', current, setAttributes )
				)
			),
			el( ServerSideRender, {
				block: 'vinifera/mega-menu-category',
				attributes: current,
				httpMethod: 'POST',
			} )
		);
	}

	registerBlockType( 'vinifera/mega-menu-category', {
		apiVersion: 3,
		title: __( 'Sayweb Mega Menu', 'sayweb-megamenu' ),
		icon: 'menu',
		category: 'design',
		attributes: {
			label: { type: 'string', default: DEFAULTS.label },
			url: { type: 'string', default: DEFAULTS.url },
			col1Title: { type: 'string', default: DEFAULTS.col1Title },
			col1Items: { type: 'string', default: DEFAULTS.col1Items },
			col2Title: { type: 'string', default: DEFAULTS.col2Title },
			col2Items: { type: 'string', default: DEFAULTS.col2Items },
			col3Title: { type: 'string', default: DEFAULTS.col3Title },
			col3Items: { type: 'string', default: DEFAULTS.col3Items },
		},
		edit: MegaMenuEdit,
		save: function () {
			return null;
		},
	} );

	/* ── vinifera/nav-link ─────────────────────────────────────── */
	function NavLinkEdit( props ) {
		const attributes  = props.attributes;
		const setAttributes = props.setAttributes;
		const blockProps  = useBlockProps( { className: 'vinifera-nav-link-editor' } );
		return el(
			'div',
			blockProps,
			el(
				InspectorControls,
				null,
				el(
					PanelBody,
					{ title: __( 'Link di navigazione', 'sayweb-megamenu' ), initialOpen: true },
					el( TextControl, {
						label: __( 'Label', 'sayweb-megamenu' ),
						value: attributes.label || 'Link',
						onChange: function ( v ) { setAttributes( { label: v } ); },
					} ),
					el( TextControl, {
						label: __( 'URL', 'sayweb-megamenu' ),
						value: attributes.url || '#',
						onChange: function ( v ) { setAttributes( { url: v } ); },
					} )
				)
			),
			el( ServerSideRender, {
				block: 'vinifera/nav-link',
				attributes: attributes,
				httpMethod: 'POST',
			} )
		);
	}

	registerBlockType( 'vinifera/nav-link', {
		apiVersion: 3,
		title: __( 'Nav Link', 'sayweb-megamenu' ),
		icon: 'admin-links',
		category: 'design',
		attributes: {
			label: { type: 'string', default: 'Link' },
			url:   { type: 'string', default: '#' },
		},
		edit: NavLinkEdit,
		save: function () { return null; },
	} );

	/* ── vinifera/header-actions ───────────────────────────────── */
	function HeaderActionsEdit( props ) {
		const attributes    = props.attributes;
		const setAttributes = props.setAttributes;
		const blockProps    = useBlockProps( { className: 'vinifera-header-actions-editor' } );
		return el(
			'div',
			blockProps,
			el(
				InspectorControls,
				null,
				el(
					PanelBody,
					{ title: __( 'Pulsante WhatsApp', 'sayweb-megamenu' ), initialOpen: true },
					el( TextControl, {
						label: __( 'URL WhatsApp', 'sayweb-megamenu' ),
						value: attributes.waUrl || '/contattaci/#whatsapp',
						onChange: function ( v ) { setAttributes( { waUrl: v } ); },
					} ),
					el( TextControl, {
						label: __( 'Testo pulsante', 'sayweb-megamenu' ),
						value: attributes.waText || 'Parla con Stefano',
						onChange: function ( v ) { setAttributes( { waText: v } ); },
					} )
				)
			),
			el( ServerSideRender, {
				block: 'vinifera/header-actions',
				attributes: attributes,
				httpMethod: 'POST',
			} )
		);
	}

	registerBlockType( 'vinifera/header-actions', {
		apiVersion: 3,
		title: __( 'Header Actions (legacy)', 'sayweb-megamenu' ),
		icon: 'star-filled',
		category: 'design',
		attributes: {
			waUrl:  { type: 'string', default: '/contattaci/#whatsapp' },
			waText: { type: 'string', default: 'Parla con Stefano' },
		},
		edit: HeaderActionsEdit,
		save: function () { return null; },
	} );

	/* ── vinifera/mobile-toggle ────────────────────────────────── */
	registerBlockType( 'vinifera/mobile-toggle', {
		apiVersion: 3,
		title: __( 'Header – Menu Mobile', 'sayweb-megamenu' ),
		icon: 'menu',
		category: 'design',
		attributes: {},
		edit: function ( props ) {
			const bp = useBlockProps( { className: 'vinifera-mobile-toggle-editor' } );
			return el( 'div', bp,
				el( ServerSideRender, { block: 'vinifera/mobile-toggle', attributes: {}, httpMethod: 'POST' } )
			);
		},
		save: function () { return null; },
	} );

	/* ── vinifera/header-search ────────────────────────────────── */
	registerBlockType( 'vinifera/header-search', {
		apiVersion: 3,
		title: __( 'Header – Cerca', 'sayweb-megamenu' ),
		icon: 'search',
		category: 'design',
		attributes: {
			placeholder: { type: 'string', default: 'Cerca vini, produttori, zone\u2026' },
		},
		edit: function ( props ) {
			const bp = useBlockProps( { className: 'vinifera-header-search-editor' } );
			return el( 'div', bp,
				el( InspectorControls, null,
					el( PanelBody, { title: __( 'Cerca', 'sayweb-megamenu' ), initialOpen: true },
						el( TextControl, {
							label: __( 'Testo placeholder', 'sayweb-megamenu' ),
							value: props.attributes.placeholder,
							onChange: function ( v ) { props.setAttributes( { placeholder: v } ); },
						} )
					)
				),
				el( ServerSideRender, { block: 'vinifera/header-search', attributes: props.attributes, httpMethod: 'POST' } )
			);
		},
		save: function () { return null; },
	} );

	/* ── vinifera/header-account ───────────────────────────────── */
	registerBlockType( 'vinifera/header-account', {
		apiVersion: 3,
		title: __( 'Header – Account', 'sayweb-megamenu' ),
		icon: 'admin-users',
		category: 'design',
		attributes: {
			url:   { type: 'string', default: '' },
			label: { type: 'string', default: 'Il mio account' },
		},
		edit: function ( props ) {
			const bp = useBlockProps( { className: 'vinifera-header-account-editor' } );
			return el( 'div', bp,
				el( InspectorControls, null,
					el( PanelBody, { title: __( 'Account', 'sayweb-megamenu' ), initialOpen: true },
						el( TextControl, {
							label: __( 'URL (vuoto = WooCommerce auto)', 'sayweb-megamenu' ),
							value: props.attributes.url,
							onChange: function ( v ) { props.setAttributes( { url: v } ); },
						} ),
						el( TextControl, {
							label: __( 'Aria label', 'sayweb-megamenu' ),
							value: props.attributes.label,
							onChange: function ( v ) { props.setAttributes( { label: v } ); },
						} )
					)
				),
				el( ServerSideRender, { block: 'vinifera/header-account', attributes: props.attributes, httpMethod: 'POST' } )
			);
		},
		save: function () { return null; },
	} );

	/* ── vinifera/header-cart ──────────────────────────────────── */
	registerBlockType( 'vinifera/header-cart', {
		apiVersion: 3,
		title: __( 'Header – Carrello', 'sayweb-megamenu' ),
		icon: 'cart',
		category: 'design',
		attributes: {
			url: { type: 'string', default: '' },
		},
		edit: function ( props ) {
			const bp = useBlockProps( { className: 'vinifera-header-cart-editor' } );
			return el( 'div', bp,
				el( InspectorControls, null,
					el( PanelBody, { title: __( 'Carrello', 'sayweb-megamenu' ), initialOpen: true },
						el( TextControl, {
							label: __( 'URL carrello (vuoto = WooCommerce auto)', 'sayweb-megamenu' ),
							value: props.attributes.url,
							onChange: function ( v ) { props.setAttributes( { url: v } ); },
						} )
					)
				),
				el( ServerSideRender, { block: 'vinifera/header-cart', attributes: props.attributes, httpMethod: 'POST' } )
			);
		},
		save: function () { return null; },
	} );

	/* ── vinifera/header-whatsapp ──────────────────────────────── */
	registerBlockType( 'vinifera/header-whatsapp', {
		apiVersion: 3,
		title: __( 'Header – WhatsApp CTA', 'sayweb-megamenu' ),
		icon: 'format-chat',
		category: 'design',
		attributes: {
			waUrl:  { type: 'string', default: '/contattaci/#whatsapp' },
			waText: { type: 'string', default: 'Parla con Stefano' },
		},
		edit: function ( props ) {
			const bp = useBlockProps( { className: 'vinifera-header-whatsapp-editor' } );
			return el( 'div', bp,
				el( InspectorControls, null,
					el( PanelBody, { title: __( 'Pulsante WhatsApp', 'sayweb-megamenu' ), initialOpen: true },
						el( TextControl, {
							label: __( 'URL', 'sayweb-megamenu' ),
							value: props.attributes.waUrl,
							onChange: function ( v ) { props.setAttributes( { waUrl: v } ); },
						} ),
						el( TextControl, {
							label: __( 'Testo pulsante', 'sayweb-megamenu' ),
							value: props.attributes.waText,
							onChange: function ( v ) { props.setAttributes( { waText: v } ); },
						} )
					)
				),
				el( ServerSideRender, { block: 'vinifera/header-whatsapp', attributes: props.attributes, httpMethod: 'POST' } )
			);
		},
		save: function () { return null; },
	} );

} )( window.wp );
