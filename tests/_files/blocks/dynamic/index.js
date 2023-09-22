import { registerBlockType } from '@wordpress/blocks'
import { useBlockProps, RichText, BlockControls } from '@wordpress/block-editor'
import {__} from '@wordpress/i18n'
import { ToolbarGroup, ToolbarButton } from '@wordpress/components';
import { addFilter } from '@wordpress/hooks'
import { hasBlockSupport, getBlockSupport } from '@wordpress/blocks'
import { createHigherOrderComponent } from '@wordpress/compose'
import { Platform } from '@wordpress/element'


// Register the block
registerBlockType('test/test', {
  edit: function ({ attributes, setAttributes }) {
    const blockProps = useBlockProps()

    return <div {...blockProps}>
      <RichText
        {...blockProps}
        tagName="h2" // The tag here is the element output and editable in the admin
        value={attributes.content} // Any existing content, either from the database or an attribute default
        allowedFormats={['core/bold', 'core/italic']} // Allow the content to be made bold or italic, but do not allow other formatting options
        onChange={(content) => setAttributes({ content })} // Store updated content as a block attribute
        placeholder={__('Heading...')} // Display this text before any content has been added by the user
      />
    </div>
  },
  save: function ({ attributes }) {
    const blockProps = useBlockProps.save()

    return <div {...blockProps}>
      <RichText.Content tagName="h2" value={attributes.content}/>
    </div>

  },
})


export const withBlockControl = createHigherOrderComponent(
  (BlockEdit) => {
    return (props) => {
      // Figure out if this is a RichText component
      const isRichText = true

      console.log({'props':props})

      if ( isRichText && props.isSelected) {
        const isWeb = Platform.OS === 'web'
        const richTextEl = 'h2' // Figure this out
        return (
          <>
            <BlockEdit {...props} />
            {isWeb && (
              <BlockControls>
                <ToolbarGroup>
                  <ToolbarButton text={richTextEl} disabled/>
                </ToolbarGroup>
              </BlockControls>
            )}
          </>
        )
      }

      return <BlockEdit {...props} />
    }
  },
  'withBlockControl'
)

addFilter(
  'editor.BlockEdit',
  'magventure/heading/with-block-control',
  withBlockControl
)
