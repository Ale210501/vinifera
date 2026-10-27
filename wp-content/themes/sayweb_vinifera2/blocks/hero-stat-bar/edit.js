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
		stat1Value: '4,9 / 5',
		stat1Label: '387 recensioni Google',
		stat2Value: '600+',
		stat2Label: 'etichette in catalogo',
		stat3Value: '120+',
		stat3Label: 'produttori selezionati',
	};

	function HeroStatBarEdit( props ) {
		const attributes = props.attributes;
		const setAttributes = props.setAttributes;
		const blockProps = useBlockProps( { className: 'vinifera-hero-stat-bar-editor' } );
		const current = Object.assign( {}, DEFAULTS, attributes );

		return el(
			'div',
			blockProps,
			el(
				InspectorControls,
				null,
				el(
					PanelBody,
					{ title: __( 'Statistiche Hero', 'sayweb-vinifera' ), initialOpen: true },
					el( TextControl, {
						label: __( 'Stat 1 – Valore', 'sayweb-vinifera' ),
						value: current.stat1Value,
						onChange: function ( value ) { setAttributes( { stat1Value: value } ); },
					} ),
					el( TextControl, {
						label: __( 'Stat 1 – Etichetta', 'sayweb-vinifera' ),
						value: current.stat1Label,
						onChange: function ( value ) { setAttributes( { stat1Label: value } ); },
					} ),
					el( TextControl, {
						label: __( 'Stat 2 – Valore', 'sayweb-vinifera' ),
						value: current.stat2Value,
						onChange: function ( value ) { setAttributes( { stat2Value: value } ); },
					} ),
					el( TextControl, {
						label: __( 'Stat 2 – Etichetta', 'sayweb-vinifera' ),
						value: current.stat2Label,
						onChange: function ( value ) { setAttributes( { stat2Label: value } ); },
					} ),
					el( TextControl, {
						label: __( 'Stat 3 – Valore', 'sayweb-vinifera' ),
						value: current.stat3Value,
						onChange: function ( value ) { setAttributes( { stat3Value: value } ); },
					} ),
					el( TextControl, {
						label: __( 'Stat 3 – Etichetta', 'sayweb-vinifera' ),
						value: current.stat3Label,
						onChange: function ( value ) { setAttributes( { stat3Label: value } ); },
					} )
				)
			),
			el( ServerSideRender, {
				block: 'sayweb-vinifera/hero-stat-bar',
				attributes: current,
				httpMethod: 'POST',
			} )
		);
	}

	registerBlockType( 'sayweb-vinifera/hero-stat-bar', {
		apiVersion: 3,
		title: __( 'Vinifera Hero Stat Bar', 'sayweb-vinifera' ),
		description: __( 'Tre statistiche (valore + etichetta) nella sezione hero.', 'sayweb-vinifera' ),
		icon: 'chart-bar',
		category: 'design',
		supports: { html: false, reusable: false },
		attributes: {
			stat1Value: { type: 'string', default: DEFAULTS.stat1Value },
			stat1Label: { type: 'string', default: DEFAULTS.stat1Label },
			stat2Value: { type: 'string', default: DEFAULTS.stat2Value },
			stat2Label: { type: 'string', default: DEFAULTS.stat2Label },
			stat3Value: { type: 'string', default: DEFAULTS.stat3Value },
			stat3Label: { type: 'string', default: DEFAULTS.stat3Label },
		},
		edit: HeroStatBarEdit,
		save: function () {
			return null;
		},
	} );
} )( window.wp );
