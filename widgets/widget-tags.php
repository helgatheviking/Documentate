<?php
/*===============
    Documentate Tags Widget
 ===============*/
 
//========= Custom Knowledgebase Tags Widget
add_action( 'widgets_init', 'docu_tag_widgets' );
function docu_tag_widgets() {
    register_widget( 'Documentate_Tags_Widget' );
}

//========= Custom Knowledgebase Tags Widget Body
class Documentate_Tags_Widget extends WP_Widget {
    
    //=======> Widget setup
    function __construct() {
        parent::__construct(
            'docu_tag_widget', // Base ID
            __( 'Knowledgebase Tags', 'documentate' ), // Name
            array( 'description' => __( 'Widget to show document tags on the site', 'documentate' ), 
            'classname' => 'documentate' ) // Args
        );
    }
    
    //=======> How to display the widget on the screen.
    function widget($args, $widgetData) {
        extract($args);
        
        //=======> Our variables from the widget settings.
        $documentate_widget_tag_title = $widgetData['txtKbeTagsHeading'];
        $documentate_widget_tag_count = $widgetData['txtKbeTagsCount'];
        $documentate_widget_tag_style = $widgetData['txtKbeTagsStyle'];
        
        //=======> widget body
        echo $before_widget;
        echo '<div class="documentate_widget documentate_widget_document">';
        
                if($documentate_widget_tag_title){
                    echo '<h2>'.$documentate_widget_tag_title.'</h2>';
                }
        ?>
        		<div class="docu_tag_widget">
        <?php
					$args = array(
								'smallest'                  => 	12,
								'largest'                   => 	30,
								'unit'                      => 	'px',
								'number'                    => 	$documentate_widget_tag_count,
								'format'                    => 	$documentate_widget_tag_style,
								'separator'                 => 	"\n",
								'orderby'                   => 	'name',
								'order'                     => 	'ASC',
								'exclude'                   => 	null,
								'include'                   => 	null,
								'topic_count_text_callback' => 	default_topic_count_text,
								'link'                      => 	'view',
								'taxonomy'                  => 	Documentate_POST_TAGS,
								'echo'                      => 	true
							);
						
					wp_tag_cloud($args);
					
					wp_reset_query();
		?>
        		</div>
        <?php      
        echo "</div>";
        echo $after_widget;
    }
    
    //Update the widget 
    function update($new_widgetData, $old_widgetData) {
        $widgetData = $old_widgetData;
		
        //Strip tags from title and name to remove HTML 
        $widgetData['txtKbeTagsHeading'] = $new_widgetData['txtKbeTagsHeading'];
        $widgetData['txtKbeTagsCount'] = $new_widgetData['txtKbeTagsCount'];
        $widgetData['txtKbeTagsStyle'] = $new_widgetData['txtKbeTagsStyle'];
		
        return $widgetData;
    }
    
    function form($widgetData) {
        //Set up some default widget settings.
        $widgetData = wp_parse_args((array) $widgetData);
?>
        <p>
            <label for="<?php echo $this->get_field_id('txtKbeTagsHeading'); ?>"><?php _e('Tag Title:','documentate'); ?></label>
            <input id="<?php echo $this->get_field_id('txtKbeTagsHeading'); ?>" name="<?php echo $this->get_field_name('txtKbeTagsHeading'); ?>" value="<?php echo $widgetData['txtKbeTagsHeading']; ?>" style="width:275px;" />
        </p>    
        <p>
            <label for="<?php echo $this->get_field_id('txtKbeTagsCount'); ?>"><?php _e('Tags Quantity:','documentate'); ?></label>
            <input id="<?php echo $this->get_field_id('txtKbeTagsCount'); ?>" name="<?php echo $this->get_field_name('txtKbeTagsCount'); ?>" value="<?php echo $widgetData['txtKbeTagsCount']; ?>" style="width:275px;" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('txtKbeTagsStyle'); ?>"><?php _e('Tags Style:','documentate'); ?></label>
            <select id="<?php echo $this->get_field_id('txtKbeTagsStyle'); ?>" name="<?php echo $this->get_field_name('txtKbeTagsStyle'); ?>">
                <option <?php selected($widgetData['txtKbeTagsStyle'], 'flat') ?> value="flat"><?php _e('Flat','documentate'); ?></option>
                <option <?php selected($widgetData['txtKbeTagsStyle'], 'list') ?> value="list"><?php _e('List','documentate'); ?></option>
            </select>
        </p>
<?php
    }
}
?>