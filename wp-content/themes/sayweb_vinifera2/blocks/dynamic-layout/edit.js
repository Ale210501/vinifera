( function ( wp ) {
	const registerBlockType = wp.blocks.registerBlockType;
	const el = wp.element.createElement;
	const __ = wp.i18n.__;
	const useBlockProps = wp.blockEditor.useBlockProps;
	const InnerBlocks = wp.blockEditor.InnerBlocks;
	const InspectorControls = wp.blockEditor.InspectorControls;
	const ServerSideRender = wp.serverSideRender;
	const PanelBody = wp.components.PanelBody;
	const TextControl = wp.components.TextControl;
	const TextareaControl = wp.components.TextareaControl;

	const HEADER_BANNER_DEFAULTS = {
		announcePrefix: 'Spedizione gratuita oltre ',
		announceHighlight1: '100 EUR',
		announceMiddle: ' in tutta Italia',
		announceLocal: 'Consegna in giornata nei comuni limitrofi a Locorotondo',
		announceSuffix: '',
		announceHighlight2: '600+ etichette selezionate',
	};

	const HEADER_DEFAULTS = {
		rossiLabel: 'Rossi',
		bianchiLabel: 'Bianchi',
		spumantiLabel: 'Spumanti',
		rosatiLabel: 'Rosati',
		km0Label: 'Km 0',
		foodLabel: 'Food locale',
		aboutLabel: 'Chi siamo',
		contactLabel: 'Contattaci',
		waLabel: 'Parla con Stefano',
		cartCount: '0',
	};

	const FOOTER_DEFAULTS = {
		brandDescription:
			'Enoteca con store fisico nel centro storico di Locorotondo e spedizione in tutta Italia. 600 etichette selezionate, food artigianale, degustazioni guidate. Dal 2020.',
		addressLabel: 'Indirizzo',
		addressText: 'Via Eroi di Dogali 12, 70010\nLocorotondo (BA), Puglia',
		hoursLabel: 'Orari',
		hoursText: 'Lun-Dom - 10:00-23:00',
		contactsLabel: 'Contatti',
		contactsText: '+39 080 123 4567\ninfo@viniferaenoteca.it',
		copyrightText: 'Copyright 2026 Vinifera Enoteca - P.IVA 0000000000',
		legalText: 'Privacy - Cookie - Termini - Spedizioni e resi',
	};

	const HEADER_ROSSI_DEFAULTS = {
		label: 'Rossi',
		col1Title: 'Regione',
		col1Items: 'Sicilia\nPuglia\nCampania\nSardegna\nVeneto\nPiemonte\nToscana\nTrentino Alto Adige\nEmilia Romagna\nLombardia\nFriuli Venezia Giulia\nAbruzzo\nValle d\'Aosta\nMarche\nLazio',
		col1FeaturedCount: '4',
		col2Title: 'Tipologia',
		col2Items: 'Valpolicella\nNebbiolo\nBrunello\nAmarone\nBarolo\nBolgheri\nChianti Classico\nBarbera\nLagrein\nSangiovese\nPrimitivo\nSupertuscan\nMontepulciano\nBarbaresco',
		col2FeaturedCount: '0',
		col3Title: 'Abbinamento',
		col3Items: 'Carni bianche\nVini da grigliata\nCarni rosse\nFormaggi\nSalumi\nVini da dessert',
		col3FeaturedCount: '0',
	};

	const HEADER_BIANCHI_DEFAULTS = {
		label: 'Bianchi',
		col1Title: 'Regione',
		col1Items: 'Sicilia\nPuglia\nCampania\nFriuli Venezia Giulia\nTrentino Alto Adige\nVeneto\nPiemonte\nToscana\nMarche\nAbruzzo\nLombardia\nSardegna\nLazio\nValle d\'Aosta\nEmilia Romagna',
		col1FeaturedCount: '4',
		col2Title: 'Tipologia',
		col2Items: 'Vermentino\nFiano\nGreco di Tufo\nFalanghina\nChardonnay\nSauvignon\nVerdicchio\nGavi\nSoave\nFriulano\nPecorino\nRibolla Gialla\nGrillo\nTrebbiano',
		col2FeaturedCount: '0',
		col3Title: 'Abbinamento',
		col3Items: 'Antipasti di mare\nPesce\nCarni bianche\nFormaggi freschi\nAperitivo\nVini da dessert',
		col3FeaturedCount: '0',
	};

	const HEADER_SPUMANTI_DEFAULTS = {
		label: 'Spumanti',
		col1Title: 'Regione',
		col1Items: 'Lombardia\nTrentino Alto Adige\nVeneto\nChampagne\nPiemonte\nEmilia Romagna\nPuglia\nSicilia\nFriuli Venezia Giulia\nAbruzzo\nMarche\nSardegna',
		col1FeaturedCount: '4',
		col2Title: 'Tipologia',
		col2Items: 'Franciacorta\nTrento DOC\nProsecco\nChampagne\nMetodo Classico\nLambrusco\nAsti Spumante\nPas Dose\nBlanc de Blancs\nSpumante Rose',
		col2FeaturedCount: '0',
		col3Title: 'Abbinamento',
		col3Items: 'Aperitivo\nAntipasti di mare\nFritti\nPesce\nBrindisi e regalo\nVini da dessert',
		col3FeaturedCount: '0',
	};

	const HEADER_ROSATI_DEFAULTS = {
		label: 'Rosati',
		col1Title: 'Regione',
		col1Items: 'Puglia\nAbruzzo\nSicilia\nCalabria\nVeneto\nToscana\nCampania\nProvenza\nLombardia\nSardegna',
		col1FeaturedCount: '4',
		col2Title: 'Tipologia',
		col2Items: 'Cerasuolo d\'Abruzzo\nBardolino Chiaretto\nNegroamaro Rosato\nSalento Rosato\nProvenza\nEtna Rosato\nCiro Rosato\nBombino Nero',
		col2FeaturedCount: '0',
		col3Title: 'Abbinamento',
		col3Items: 'Aperitivo\nPizza\nCarni bianche\nPesce\nSalumi\nFormaggi freschi',
		col3FeaturedCount: '0',
	};

	const HEADER_LINKS_DEFAULTS = {
		km0Label: 'Km 0',
		km0Url: '#',
		foodLabel: 'Food locale',
		foodUrl: '#',
		aboutLabel: 'Chi siamo',
		aboutUrl: '#',
		contactLabel: 'Contattaci',
		contactUrl: '#',
	};

	const HEADER_ACTIONS_DEFAULTS = {
		waLabel: 'Parla con Stefano',
		waUrl: '#',
		cartCount: '0',
	};

	function HeaderBannerEdit( props ) {
		const attributes = props.attributes;
		const setAttributes = props.setAttributes;
		const blockProps = useBlockProps( { className: 'vinifera-dynamic-layout-editor' } );
		const current = Object.assign( {}, HEADER_BANNER_DEFAULTS, attributes );

		return el(
			'div',
			blockProps,
			el(
				InspectorControls,
				null,
				el(
					PanelBody,
					{ title: __( 'Header Texts', 'sayweb-vinifera' ), initialOpen: true },
					el( TextControl, {
						label: __( 'Promo Prefix', 'sayweb-vinifera' ),
						value: current.announcePrefix,
						onChange: function ( value ) {
							setAttributes( { announcePrefix: value } );
						},
					} ),
					el( TextControl, {
						label: __( 'Promo Highlight 1', 'sayweb-vinifera' ),
						value: current.announceHighlight1,
						onChange: function ( value ) {
							setAttributes( { announceHighlight1: value } );
						},
					} ),
					el( TextControl, {
						label: __( 'Promo Middle', 'sayweb-vinifera' ),
						value: current.announceMiddle,
						onChange: function ( value ) {
							setAttributes( { announceMiddle: value } );
						},
					} ),
					el( TextControl, {
						label: __( 'Promo Local Delivery', 'sayweb-vinifera' ),
						value: current.announceLocal,
						onChange: function ( value ) {
							setAttributes( { announceLocal: value } );
						},
					} ),
					el( TextControl, {
						label: __( 'Promo Suffix', 'sayweb-vinifera' ),
						value: current.announceSuffix,
						onChange: function ( value ) {
							setAttributes( { announceSuffix: value } );
						},
					} ),
					el( TextControl, {
						label: __( 'Promo Highlight 2', 'sayweb-vinifera' ),
						value: current.announceHighlight2,
						onChange: function ( value ) {
							setAttributes( { announceHighlight2: value } );
						},
					} ),
				)
			),
			el( ServerSideRender, {
				block: 'sayweb-vinifera/dynamic-header-banner',
				attributes: current,
				httpMethod: 'POST',
			} )
		);
	}

	function HeaderEdit( props ) {
		const attributes = props.attributes;
		const setAttributes = props.setAttributes;
		const blockProps = useBlockProps( { className: 'vinifera-dynamic-layout-editor' } );
		const current = Object.assign( {}, HEADER_DEFAULTS, attributes );

		return el(
			'div',
			blockProps,
			el(
				InspectorControls,
				null,
				el(
					PanelBody,
					{ title: __( 'Header Menu', 'sayweb-vinifera' ), initialOpen: true },
					el( TextControl, {
						label: __( 'Rossi Label', 'sayweb-vinifera' ),
						value: current.rossiLabel,
						onChange: function ( value ) {
							setAttributes( { rossiLabel: value } );
						},
					} ),
					el( TextControl, {
						label: __( 'Bianchi Label', 'sayweb-vinifera' ),
						value: current.bianchiLabel,
						onChange: function ( value ) {
							setAttributes( { bianchiLabel: value } );
						},
					} ),
					el( TextControl, {
						label: __( 'Spumanti Label', 'sayweb-vinifera' ),
						value: current.spumantiLabel,
						onChange: function ( value ) {
							setAttributes( { spumantiLabel: value } );
						},
					} ),
					el( TextControl, {
						label: __( 'Rosati Label', 'sayweb-vinifera' ),
						value: current.rosatiLabel,
						onChange: function ( value ) {
							setAttributes( { rosatiLabel: value } );
						},
					} ),
					el( TextControl, {
						label: __( 'Km 0 Label', 'sayweb-vinifera' ),
						value: current.km0Label,
						onChange: function ( value ) {
							setAttributes( { km0Label: value } );
						},
					} ),
					el( TextControl, {
						label: __( 'Food Locale Label', 'sayweb-vinifera' ),
						value: current.foodLabel,
						onChange: function ( value ) {
							setAttributes( { foodLabel: value } );
						},
					} ),
					el( TextControl, {
						label: __( 'About Label', 'sayweb-vinifera' ),
						value: current.aboutLabel,
						onChange: function ( value ) {
							setAttributes( { aboutLabel: value } );
						},
					} ),
					el( TextControl, {
						label: __( 'Contact Label', 'sayweb-vinifera' ),
						value: current.contactLabel,
						onChange: function ( value ) {
							setAttributes( { contactLabel: value } );
						},
					} ),
					el( TextControl, {
						label: __( 'WhatsApp CTA', 'sayweb-vinifera' ),
						value: current.waLabel,
						onChange: function ( value ) {
							setAttributes( { waLabel: value } );
						},
					} ),
					el( TextControl, {
						label: __( 'Cart Count', 'sayweb-vinifera' ),
						value: current.cartCount,
						onChange: function ( value ) {
							setAttributes( { cartCount: value } );
						},
					} )
				)
			),
			el( ServerSideRender, {
				block: 'sayweb-vinifera/dynamic-header',
				attributes: current,
				httpMethod: 'POST',
			} )
		);
	}

	function FooterEdit( props ) {
		const attributes = props.attributes;
		const setAttributes = props.setAttributes;
		const blockProps = useBlockProps( { className: 'vinifera-dynamic-layout-editor' } );
		const current = Object.assign( {}, FOOTER_DEFAULTS, attributes );

		return el(
			'div',
			blockProps,
			el(
				InspectorControls,
				null,
				el(
					PanelBody,
					{ title: __( 'Footer Texts', 'sayweb-vinifera' ), initialOpen: true },
					el( TextareaControl, {
						label: __( 'Brand Description', 'sayweb-vinifera' ),
						value: current.brandDescription,
						onChange: function ( value ) {
							setAttributes( { brandDescription: value } );
						},
					} ),
					el( TextControl, {
						label: __( 'Address Label', 'sayweb-vinifera' ),
						value: current.addressLabel,
						onChange: function ( value ) {
							setAttributes( { addressLabel: value } );
						},
					} ),
					el( TextareaControl, {
						label: __( 'Address Text', 'sayweb-vinifera' ),
						value: current.addressText,
						onChange: function ( value ) {
							setAttributes( { addressText: value } );
						},
					} ),
					el( TextControl, {
						label: __( 'Hours Label', 'sayweb-vinifera' ),
						value: current.hoursLabel,
						onChange: function ( value ) {
							setAttributes( { hoursLabel: value } );
						},
					} ),
					el( TextControl, {
						label: __( 'Hours Text', 'sayweb-vinifera' ),
						value: current.hoursText,
						onChange: function ( value ) {
							setAttributes( { hoursText: value } );
						},
					} ),
					el( TextControl, {
						label: __( 'Contacts Label', 'sayweb-vinifera' ),
						value: current.contactsLabel,
						onChange: function ( value ) {
							setAttributes( { contactsLabel: value } );
						},
					} ),
					el( TextareaControl, {
						label: __( 'Contacts Text', 'sayweb-vinifera' ),
						value: current.contactsText,
						onChange: function ( value ) {
							setAttributes( { contactsText: value } );
						},
					} ),
					el( TextControl, {
						label: __( 'Copyright', 'sayweb-vinifera' ),
						value: current.copyrightText,
						onChange: function ( value ) {
							setAttributes( { copyrightText: value } );
						},
					} ),
					el( TextControl, {
						label: __( 'Legal Links Text', 'sayweb-vinifera' ),
						value: current.legalText,
						onChange: function ( value ) {
							setAttributes( { legalText: value } );
						},
					} )
				)
			),
			el( ServerSideRender, {
				block: 'sayweb-vinifera/dynamic-footer',
				attributes: current,
				httpMethod: 'POST',
			} )
		);
	}

	function BrandEdit( props ) {
		const attributes = props.attributes;
		const setAttributes = props.setAttributes;
		const blockProps = useBlockProps( { className: 'vinifera-dynamic-layout-editor' } );
		const current = Object.assign( { brandUrl: '/' }, attributes );

		return el(
			'div',
			blockProps,
			el(
				InspectorControls,
				null,
				el(
					PanelBody,
					{ title: __( 'Brand Link', 'sayweb-vinifera' ), initialOpen: true },
					el( TextControl, {
						label: __( 'Brand URL', 'sayweb-vinifera' ),
						value: current.brandUrl,
						onChange: function ( value ) {
							setAttributes( { brandUrl: value } );
						},
					} )
				)
			),
			el( ServerSideRender, {
				block: 'sayweb-vinifera/dynamic-header-brand',
				attributes: current,
				httpMethod: 'POST',
			} )
		);
	}

	function HeaderShellEdit() {
		const blockProps = useBlockProps( { className: 'vinifera-dynamic-layout-editor' } );

		return el(
			'div',
			blockProps,
			el( InnerBlocks, {
				template: [
					[ 'sayweb-vinifera/dynamic-header-brand' ],
					[ 'sayweb-vinifera/dynamic-header-nav' ],
					[ 'sayweb-vinifera/dynamic-header-actions' ],
				],
				templateLock: false,
			} )
		);
	}

	function HeaderNavEdit() {
		const blockProps = useBlockProps( { className: 'vinifera-dynamic-layout-editor' } );

		return el(
			'div',
			blockProps,
			el( InnerBlocks, {
				template: [
					[ 'sayweb-vinifera/dynamic-header-rossi' ],
					[ 'sayweb-vinifera/dynamic-header-bianchi' ],
					[ 'sayweb-vinifera/dynamic-header-spumanti' ],
					[ 'sayweb-vinifera/dynamic-header-rosati' ],
					[ 'sayweb-vinifera/dynamic-header-links' ],
				],
				templateLock: false,
			} )
		);
	}

	function buildAttributeSchema( defaults ) {
		const attributes = {};

		Object.keys( defaults ).forEach( function ( key ) {
			attributes[ key ] = { type: 'string', default: defaults[ key ] };
		} );

		return attributes;
	}

	function createMegaEdit( blockName, title, defaults ) {
		return function ( props ) {
			const attributes = props.attributes;
			const setAttributes = props.setAttributes;
			const blockProps = useBlockProps( { className: 'vinifera-dynamic-layout-editor' } );
			const current = Object.assign( {}, defaults, attributes );

			return el(
				'div',
				blockProps,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: title, initialOpen: true },
						el( TextControl, {
							label: __( 'Label', 'sayweb-vinifera' ),
							value: current.label,
							onChange: function ( value ) {
								setAttributes( { label: value } );
							},
						} ),
						el( TextControl, {
							label: __( 'Column 1 Title', 'sayweb-vinifera' ),
							value: current.col1Title,
							onChange: function ( value ) {
								setAttributes( { col1Title: value } );
							},
						} ),
						el( TextareaControl, {
							label: __( 'Column 1 Items', 'sayweb-vinifera' ),
							value: current.col1Items,
							onChange: function ( value ) {
								setAttributes( { col1Items: value } );
							},
						} ),
						el( TextControl, {
							label: __( 'Column 1 Featured Count', 'sayweb-vinifera' ),
							value: current.col1FeaturedCount,
							onChange: function ( value ) {
								setAttributes( { col1FeaturedCount: value } );
							},
						} ),
						el( TextControl, {
							label: __( 'Column 2 Title', 'sayweb-vinifera' ),
							value: current.col2Title,
							onChange: function ( value ) {
								setAttributes( { col2Title: value } );
							},
						} ),
						el( TextareaControl, {
							label: __( 'Column 2 Items', 'sayweb-vinifera' ),
							value: current.col2Items,
							onChange: function ( value ) {
								setAttributes( { col2Items: value } );
							},
						} ),
						el( TextControl, {
							label: __( 'Column 2 Featured Count', 'sayweb-vinifera' ),
							value: current.col2FeaturedCount,
							onChange: function ( value ) {
								setAttributes( { col2FeaturedCount: value } );
							},
						} ),
						el( TextControl, {
							label: __( 'Column 3 Title', 'sayweb-vinifera' ),
							value: current.col3Title,
							onChange: function ( value ) {
								setAttributes( { col3Title: value } );
							},
						} ),
						el( TextareaControl, {
							label: __( 'Column 3 Items', 'sayweb-vinifera' ),
							value: current.col3Items,
							onChange: function ( value ) {
								setAttributes( { col3Items: value } );
							},
						} ),
						el( TextControl, {
							label: __( 'Column 3 Featured Count', 'sayweb-vinifera' ),
							value: current.col3FeaturedCount,
							onChange: function ( value ) {
								setAttributes( { col3FeaturedCount: value } );
							},
						} )
					)
				),
				el( ServerSideRender, {
					block: blockName,
					attributes: current,
					httpMethod: 'POST',
				} )
			);
		};
	}

	function createLinksEdit( blockName, title, defaults ) {
		return function ( props ) {
			const attributes = props.attributes;
			const setAttributes = props.setAttributes;
			const blockProps = useBlockProps( { className: 'vinifera-dynamic-layout-editor' } );
			const current = Object.assign( {}, defaults, attributes );

			return el(
				'div',
				blockProps,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: title, initialOpen: true },
						el( TextControl, {
							label: __( 'Km 0 Label', 'sayweb-vinifera' ),
							value: current.km0Label,
							onChange: function ( value ) {
								setAttributes( { km0Label: value } );
							},
						} ),
						el( TextControl, {
							label: __( 'Km 0 URL', 'sayweb-vinifera' ),
							value: current.km0Url,
							onChange: function ( value ) {
								setAttributes( { km0Url: value } );
							},
						} ),
						el( TextControl, {
							label: __( 'Food Locale Label', 'sayweb-vinifera' ),
							value: current.foodLabel,
							onChange: function ( value ) {
								setAttributes( { foodLabel: value } );
							},
						} ),
						el( TextControl, {
							label: __( 'Food Locale URL', 'sayweb-vinifera' ),
							value: current.foodUrl,
							onChange: function ( value ) {
								setAttributes( { foodUrl: value } );
							},
						} ),
						el( TextControl, {
							label: __( 'About Label', 'sayweb-vinifera' ),
							value: current.aboutLabel,
							onChange: function ( value ) {
								setAttributes( { aboutLabel: value } );
							},
						} ),
						el( TextControl, {
							label: __( 'About URL', 'sayweb-vinifera' ),
							value: current.aboutUrl,
							onChange: function ( value ) {
								setAttributes( { aboutUrl: value } );
							},
						} ),
						el( TextControl, {
							label: __( 'Contact Label', 'sayweb-vinifera' ),
							value: current.contactLabel,
							onChange: function ( value ) {
								setAttributes( { contactLabel: value } );
							},
						} )
						,
						el( TextControl, {
							label: __( 'Contact URL', 'sayweb-vinifera' ),
							value: current.contactUrl,
							onChange: function ( value ) {
								setAttributes( { contactUrl: value } );
							},
						} )
					)
				),
				el( ServerSideRender, {
					block: blockName,
					attributes: current,
					httpMethod: 'POST',
				} )
			);
		};
	}

	function createActionsEdit( blockName, title, defaults ) {
		return function ( props ) {
			const attributes = props.attributes;
			const setAttributes = props.setAttributes;
			const blockProps = useBlockProps( { className: 'vinifera-dynamic-layout-editor' } );
			const current = Object.assign( {}, defaults, attributes );

			return el(
				'div',
				blockProps,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: title, initialOpen: true },
						el( TextControl, {
							label: __( 'WhatsApp CTA', 'sayweb-vinifera' ),
							value: current.waLabel,
							onChange: function ( value ) {
								setAttributes( { waLabel: value } );
							},
						} ),
						el( TextControl, {
							label: __( 'WhatsApp URL', 'sayweb-vinifera' ),
							value: current.waUrl,
							onChange: function ( value ) {
								setAttributes( { waUrl: value } );
							},
						} ),
						el( TextControl, {
							label: __( 'Cart Count', 'sayweb-vinifera' ),
							value: current.cartCount,
							onChange: function ( value ) {
								setAttributes( { cartCount: value } );
							},
						} )
					)
				),
				el( ServerSideRender, {
					block: blockName,
					attributes: current,
					httpMethod: 'POST',
				} )
			);
		};
	}

	registerBlockType( 'sayweb-vinifera/dynamic-header', {
		apiVersion: 3,
		title: __( 'Vinifera Dynamic Header', 'sayweb-vinifera' ),
		icon: 'menu',
		category: 'design',
		attributes: {
			rossiLabel: { type: 'string', default: HEADER_DEFAULTS.rossiLabel },
			bianchiLabel: { type: 'string', default: HEADER_DEFAULTS.bianchiLabel },
			spumantiLabel: { type: 'string', default: HEADER_DEFAULTS.spumantiLabel },
			rosatiLabel: { type: 'string', default: HEADER_DEFAULTS.rosatiLabel },
			km0Label: { type: 'string', default: HEADER_DEFAULTS.km0Label },
			foodLabel: { type: 'string', default: HEADER_DEFAULTS.foodLabel },
			aboutLabel: { type: 'string', default: HEADER_DEFAULTS.aboutLabel },
			contactLabel: { type: 'string', default: HEADER_DEFAULTS.contactLabel },
			waLabel: { type: 'string', default: HEADER_DEFAULTS.waLabel },
			cartCount: { type: 'string', default: HEADER_DEFAULTS.cartCount },
		},
		edit: HeaderEdit,
		save: function () {
			return null;
		},
	} );

	registerBlockType( 'sayweb-vinifera/dynamic-header-rossi', {
		apiVersion: 3,
		title: __( 'Vinifera Header Rossi', 'sayweb-vinifera' ),
		icon: 'menu',
		category: 'design',
		attributes: buildAttributeSchema( HEADER_ROSSI_DEFAULTS ),
		edit: createMegaEdit( 'sayweb-vinifera/dynamic-header-rossi', __( 'Header Rossi', 'sayweb-vinifera' ), HEADER_ROSSI_DEFAULTS ),
		save: function () {
			return null;
		},
	} );

	registerBlockType( 'sayweb-vinifera/dynamic-header-bianchi', {
		apiVersion: 3,
		title: __( 'Vinifera Header Bianchi', 'sayweb-vinifera' ),
		icon: 'menu',
		category: 'design',
		attributes: buildAttributeSchema( HEADER_BIANCHI_DEFAULTS ),
		edit: createMegaEdit( 'sayweb-vinifera/dynamic-header-bianchi', __( 'Header Bianchi', 'sayweb-vinifera' ), HEADER_BIANCHI_DEFAULTS ),
		save: function () {
			return null;
		},
	} );

	registerBlockType( 'sayweb-vinifera/dynamic-header-spumanti', {
		apiVersion: 3,
		title: __( 'Vinifera Header Spumanti', 'sayweb-vinifera' ),
		icon: 'menu',
		category: 'design',
		attributes: buildAttributeSchema( HEADER_SPUMANTI_DEFAULTS ),
		edit: createMegaEdit( 'sayweb-vinifera/dynamic-header-spumanti', __( 'Header Spumanti', 'sayweb-vinifera' ), HEADER_SPUMANTI_DEFAULTS ),
		save: function () {
			return null;
		},
	} );

	registerBlockType( 'sayweb-vinifera/dynamic-header-rosati', {
		apiVersion: 3,
		title: __( 'Vinifera Header Rosati', 'sayweb-vinifera' ),
		icon: 'menu',
		category: 'design',
		attributes: buildAttributeSchema( HEADER_ROSATI_DEFAULTS ),
		edit: createMegaEdit( 'sayweb-vinifera/dynamic-header-rosati', __( 'Header Rosati', 'sayweb-vinifera' ), HEADER_ROSATI_DEFAULTS ),
		save: function () {
			return null;
		},
	} );

	registerBlockType( 'sayweb-vinifera/dynamic-header-links', {
		apiVersion: 3,
		title: __( 'Vinifera Header Links', 'sayweb-vinifera' ),
		icon: 'admin-links',
		category: 'design',
		attributes: buildAttributeSchema( HEADER_LINKS_DEFAULTS ),
		edit: createLinksEdit( 'sayweb-vinifera/dynamic-header-links', __( 'Header Links', 'sayweb-vinifera' ), HEADER_LINKS_DEFAULTS ),
		save: function () {
			return null;
		},
	} );

	registerBlockType( 'sayweb-vinifera/dynamic-header-actions', {
		apiVersion: 3,
		title: __( 'Vinifera Header Actions', 'sayweb-vinifera' ),
		icon: 'admin-generic',
		category: 'design',
		attributes: buildAttributeSchema( HEADER_ACTIONS_DEFAULTS ),
		edit: createActionsEdit( 'sayweb-vinifera/dynamic-header-actions', __( 'Header Actions', 'sayweb-vinifera' ), HEADER_ACTIONS_DEFAULTS ),
		save: function () {
			return null;
		},
	} );

	registerBlockType( 'sayweb-vinifera/dynamic-header-banner', {
		apiVersion: 3,
		title: __( 'Vinifera Header Banner', 'sayweb-vinifera' ),
		icon: 'info-outline',
		category: 'design',
		attributes: {
			announcePrefix: { type: 'string', default: HEADER_BANNER_DEFAULTS.announcePrefix },
			announceHighlight1: { type: 'string', default: HEADER_BANNER_DEFAULTS.announceHighlight1 },
			announceMiddle: { type: 'string', default: HEADER_BANNER_DEFAULTS.announceMiddle },
			announceLocal: { type: 'string', default: HEADER_BANNER_DEFAULTS.announceLocal },
			announceSuffix: { type: 'string', default: HEADER_BANNER_DEFAULTS.announceSuffix },
			announceHighlight2: { type: 'string', default: HEADER_BANNER_DEFAULTS.announceHighlight2 },
		},
		edit: HeaderBannerEdit,
		save: function () {
			return null;
		},
	} );

	registerBlockType( 'sayweb-vinifera/dynamic-header-brand', {
		apiVersion: 3,
		title: __( 'Vinifera Header Brand', 'sayweb-vinifera' ),
		icon: 'admin-home',
		category: 'design',
		attributes: {
			brandUrl: { type: 'string', default: '/' },
		},
		edit: BrandEdit,
		save: function () {
			return null;
		},
	} );

	registerBlockType( 'sayweb-vinifera/dynamic-header-shell', {
		apiVersion: 3,
		title: __( 'Vinifera Header Shell', 'sayweb-vinifera' ),
		icon: 'layout',
		category: 'design',
		edit: HeaderShellEdit,
		save: function () {
			return el(
				'header',
				{ className: 'topbar' },
				el( 'div', { className: 'wrap' }, el( InnerBlocks.Content ) )
			);
		},
	} );

	registerBlockType( 'sayweb-vinifera/dynamic-header-nav', {
		apiVersion: 3,
		title: __( 'Vinifera Header Nav', 'sayweb-vinifera' ),
		icon: 'menu',
		category: 'design',
		edit: HeaderNavEdit,
		save: function () {
			return el( 'nav', { className: 'topnav' }, el( InnerBlocks.Content ) );
		},
	} );

	registerBlockType( 'sayweb-vinifera/dynamic-footer', {
		apiVersion: 3,
		title: __( 'Vinifera Dynamic Footer', 'sayweb-vinifera' ),
		icon: 'admin-site-alt3',
		category: 'design',
		attributes: {
			brandDescription: { type: 'string', default: FOOTER_DEFAULTS.brandDescription },
			addressLabel: { type: 'string', default: FOOTER_DEFAULTS.addressLabel },
			addressText: { type: 'string', default: FOOTER_DEFAULTS.addressText },
			hoursLabel: { type: 'string', default: FOOTER_DEFAULTS.hoursLabel },
			hoursText: { type: 'string', default: FOOTER_DEFAULTS.hoursText },
			contactsLabel: { type: 'string', default: FOOTER_DEFAULTS.contactsLabel },
			contactsText: { type: 'string', default: FOOTER_DEFAULTS.contactsText },
			copyrightText: { type: 'string', default: FOOTER_DEFAULTS.copyrightText },
			legalText: { type: 'string', default: FOOTER_DEFAULTS.legalText },
		},
		edit: FooterEdit,
		save: function () {
			return null;
		},
	} );
} )( window.wp );
