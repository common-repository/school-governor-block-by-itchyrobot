/* This section of the code registers a new block, sets an icon and a category, and indicates what type of fields it'll include. */
 ( function( blocks, element, serverSideRender ) {
    var el = element.createElement,
        registerBlockType = blocks.registerBlockType,
        ServerSideRender = serverSideRender;
 
wp.blocks.registerBlockType('itchyrobot-governor/ablock-governor', {
  title: 'School Governor\'s',
  icon: 'groups',
  category: 'common',
  attributes: {
   
  },
  
/* This configures how the content and color fields will work, and sets up the necessary elements */

  edit: function(props) {
     return (
                el( ServerSideRender, {
                    block: 'itchyrobot-governor/ablock-governor',
                    attributes: props.attributes,
                } )
            );
  },
  save: function(props) {
    return;
  }
});
}(
    window.wp.blocks,
    window.wp.element,
    window.wp.serverSideRender,
) );