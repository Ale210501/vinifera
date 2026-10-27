( function ( wp ) {
	const registerBlockType = wp.blocks.registerBlockType;
	const el = wp.element.createElement;
	const __ = wp.i18n.__;
	const useBlockProps = wp.blockEditor.useBlockProps;
	const InspectorControls = wp.blockEditor.InspectorControls;
	const ServerSideRender = wp.serverSideRender;
	const PanelBody = wp.components.PanelBody;
	const TextControl = wp.components.TextControl;

	const DEFAULTS = {
		tapeValue: '10%',
		tapeText: 'di sconto sul primo ordine se ti iscrivi alla newsletter',
	};

	function HeroTapeEdit( props ) {
		const attributes = props.attributes;
		const setAttributes = props.setAttributes;
		const blockProps = useBlockProps( { className: 'vinifera-hero-tape-editor' } );
		const current = Object.assign( {}, DEFAULTS, attributes );

		return el(
			'div',
			blockProps,
			el(
				InspectorControls,
				null,
				el(
					PanelBody,
					{ title: __( 'Tape Badge', 'sayweb-vinifera' ), initialOpen: true },
					el( TextControl, {
						label: __( 'Valore (es. 10%)', 'sayweb-vinifera' ),
						value: current.tapeValue,
						onChange: function ( value ) { setAttributes( { tapeValue: value } ); },
					} ),
					el( TextControl, {
						label: __( 'Testo descrittivo', 'sayweb-vinifera' ),
						value: current.tapeText,
						onChange: function ( value ) { setAttributes( { tapeText: value } ); },
					} )
				)
			),
			el( ServerSideRender, {
				block: 'sayweb-vinifera/hero-tape',
				attributes: current,
				httpMethod: 'POST',
			} )
		);
	}

	registerBlockType( 'sayweb-vinifera/hero-tape', {
		apiVersion: 3,
		title: __( 'Vinifera Hero Tape', 'sayweb-vinifera' ),
		description: __( 'Badge adesivo con valore percentuale e testo promo.', 'sayweb-vinifera' ),
		icon: 'tag',
		category: 'design',
		supports: { html: false, reusable: false },
		attributes: {
			tapeValue: { type: 'string', default: DEFAULTS.tapeValue },
			tapeText:  { type: 'string', default: DEFAULTS.tapeText },
		},
		edit: HeroTapeEdit,
		save: function () {
			return null;
		},
	} );
} )( window.wp );
