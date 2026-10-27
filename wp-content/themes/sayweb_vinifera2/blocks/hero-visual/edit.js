( function ( wp ) {
	const registerBlockType = wp.blocks.registerBlockType;
	const el = wp.element.createElement;
	const useBlockProps = wp.blockEditor.useBlockProps;
	const __ = wp.i18n.__;

	registerBlockType( 'sayweb-vinifera/hero-visual', {
		apiVersion: 3,
		title: __( 'Vinifera Hero Visual', 'sayweb-vinifera' ),
		description: __( 'Visual hero con immagine principale e immagine flottante.', 'sayweb-vinifera' ),
		icon: 'format-image',
		category: 'design',
		supports: {
			html: false,
			reusable: false,
		},
		attributes: {
			mainImageUrl: { type: 'string' },
			floaterImageUrl: { type: 'string' },
			tapeValue: { type: 'string' },
			tapeText: { type: 'string' },
		},
		edit: function () {
			const blockProps = useBlockProps( {
				className: 'vinifera-hero-visual-block-editor',
			} );

			return el(
				'div',
				blockProps,
				el( 'p', { className: 'vinifera-hero-visual-block-editor__label' }, __( 'Vinifera Hero Visual', 'sayweb-vinifera' ) ),
				el( 'div', { className: 'vinifera-hero-visual-block-editor__preview' },
					el( 'div', { className: 'vinifera-hero-visual-block-editor__img vinifera-hero-visual-block-editor__img-main' } ),
					el( 'div', { className: 'vinifera-hero-visual-block-editor__img vinifera-hero-visual-block-editor__img-floater' } ),
					el( 'div', { className: 'vinifera-hero-visual-block-editor__tape' }, '10%' )
				)
			);
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp );
