<?php
/*===============
    Documentate Category Widget
 ===============*/
 
//========= Custom Knowledgebase Category Widget
add_action( 'widgets_init', 'documentate_category_widgets' );
function documentate_category_widgets() {
    register_widget( 'Documentate_Cat_Widget' );
}

//========= Custom Knowledgebase Category Widget Body
class Documentate_Cat_Widget extends WP_Widget {
 
    //=======> Widget setup.
    function __construct() {
        parent::__construct(
            'documentate_category_widget', // Base ID
            __( 'Knowledgebase Category', 'documentate' ), // Name
            array( 'description' => __( 'Widget to show document categories on the site', 'documentate' ), 
                    'classname' => 'documentate' ) // Args
        );
    }
	
     //=======> How to display the widget on the screen.
    function widget($args, $widgetData) {
        extract($args);
		
        //Our variables from the widget settings.
        $documentate_widget_cat_title = $widgetData['txtKbeCatHeading'];
        $documentate_widget_cat_count = $widgetData['txtKbeCatCount'];
		
        //=======> widget body
        echo $before_widget;
        echo '<div class="documentate_widget">';
        
            if ($documentate_widget_cat_title){
                echo '<h2>'.$documentate_widget_cat_title.'</h2>';
            }
			
            $documentate_cat_args = array(
                'number' 	=>  $documentate_widget_cat_count,
                'taxonomy'	=>  'docu_cat',
                'orderby'   =>  'terms_order',
                'order'     =>  'ASC'
            );
			
            $documentate_cats = get_categories($documentate_cat_args);
            echo "<ul>";
                foreach($documentate_cats as $docu_cat){
                    echo "<li>"
                            ."<a href=".get_term_link($docu_cat->slug, 'docu_cat')." title=".sprintf( __( "View all posts in %s" ), $docu_cat->name ).">"
                                .$docu_cat->name.
                             "</a>"
                         ."</li>";
                }
            echo "</ul>";
        
        echo "</div>";
        echo $after_widget;
    }
	
    //Update the widget 
    function update($new_widgetData, $old_widgetData) {
        $widgetData = $old_widgetData;
		
        //Strip tags from title and name to remove HTML 
        $widgetData['txtKbeCatHeading'] = $new_widgetData['txtKbeCatHeading'];
        $widgetData['txtKbeCatCount'] = $new_widgetData['txtKbeCatCount'];
		
        return $widgetData;
    }
    function form($widgetData) {
        //Set up some default widget settings.
        $widgetData = wp_parse_args((array) $widgetData);
?>
        <p>
            <label for="<?php echo $this->get_field_id('txtKbeCatHeading'); ?>"><?php _e('Category Title:','documentate') ?></label>
            <input id="<?php echo $this->get_field_id('txtKbeCatHeading'); ?>" name="<?php echo $this->get_field_name('txtKbeCatHeading'); ?>" value="<?php echo $widgetData['txtKbeCatHeading']; ?>" style="width:275px;" />
        </p>    
        <p>
            <label for="<?php echo $this->get_field_id('txtKbeCatCount'); ?>"><?php _e('Catgory Quantity:','documentate'); ?></label>
            <input id="<?php echo $this->get_field_id('txtKbeCatCount'); ?>" name="<?php echo $this->get_field_name('txtKbeCatCount'); ?>" value="<?php echo $widgetData['txtKbeCatCount']; ?>" style="width:275px;" />
        </p>
<?php
    }
}
?>