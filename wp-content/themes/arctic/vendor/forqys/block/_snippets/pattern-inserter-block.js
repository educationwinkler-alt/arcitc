const { registerBlockType } = wp.blocks;
const { Button } = wp.components;
const { useRef } = wp.element;
const { useBlockProps } = wp.blockEditor;
const { parse } = wp.blocks;


registerBlockType('your-plugin/your-custom-block', {
    title: 'Custom Block',
    category: 'common',
    attributes: {
        pattern: {
            type: 'string',
            default: '',
        },
    },
    edit: () => {
        const editorRef = useRef(null);

        const predefinedPatterns = [
            {
                label: 'Pattern 1',
                pattern: '<!-- wp:heading --><h2>Hello Pattern</h2><!-- /wp:heading --><!-- wp:paragraph --><p>This is a simple pattern.</p><!-- /wp:paragraph --><!-- wp:image --><figure class="wp-block-image"><img src="http://example.com/wp-content/uploads/2022/01/image.jpg" alt=""/></figure><!-- /wp:image -->'
            },
            {
                label: 'Pattern 2',
                pattern: '<!-- wp:image --><figure class="wp-block-image"><img src="http://example.com/wp-content/uploads/2022/01/image.jpg" alt=""/></figure><!-- /wp:image --><!-- wp:heading --><h2>Hello Pattern</h2><!-- /wp:heading --><!-- wp:paragraph --><p>This is a simple pattern.</p><!-- /wp:paragraph -->'
            },
        ];

        const handleButtonClick = (pattern) => {
            if (editorRef.current) {
                const parsedBlocks = parse(pattern);
                wp.data.dispatch('core/editor').insertBlocks(parsedBlocks);
            }
        };

        return (
            <div { ...useBlockProps() } ref={editorRef}>
                {predefinedPatterns.map((item, index) => (
                    <Button key={index} onClick={() => handleButtonClick(item.pattern)}>
                        {item.label}
                    </Button>
                ))}
            </div>
        );
    },
    save: ({ attributes }) => {
        // Rendering in frontend is handled by PHP function, so saving the block content is not necessary
        return null;
    },
});
