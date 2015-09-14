<?php
/*===============
    Documentate Search Documents Widget
 ===============*/
 
//========= Custom Knowledgebase Search Widget
add_action( 'widgets_init', 'documentate_search_widgets' );
function documentate_search_widgets() {
    register_widget( 'Documentate_Search_Widget' );
}

//========= Custom Knowledgebase Search Widget Body
class Documentate_Search_Widget extends WP_Widget {
    
    //=======> Widget setup
    function __construct() {
        parent::__construct(
            'documentate_search_widget', // Base ID
            __( 'Knowledgebase Search', 'documentate' ), // Name
            array( 'description' => __('Document search widget', 'documentate'), 
                  'classname' => 'documentate' ) // Args
        );
    }

    
  //=======> How to display the widget on the screen.
    function widget($args, $widgetData) {
        extract($args);
        
        //=======> Our variables from the widget settings.
        $documentate_widget_search_title = $widgetData['txtKbeSearchHeading'];
        
        //=======> widget body
        echo $before_widget;
        echo '<div class="documentate_widget">';
        
            if($documentate_widget_search_title){
                echo '<h2>'.$documentate_widget_search_title.'</h2>';
            }
            get_document_search_form();

        echo "</div>";
        echo $after_widget;
    }
    
    //Update the widget 
    function update($new_widgetData, $old_widgetData) {
        $widgetData = $old_widgetData;
        //Strip tags from title and name to remove HTML 
        $widgetData['txtKbeSearchHeading'] = $new_widgetData['txtKbeSearchHeading'];
        return $widgetData;
    }
    
    function form($widgetData) {

        $defaults = array(
            'txtKbeSearchHeading' => __( 'Search Documents', 'documentate' ),
        );

        //Set up some default widget settings.
        $widgetData = wp_parse_args($widgetData, $defaults);
    ?>
        <p>
            <label for="<?php echo $this->get_field_id('txtKbeSearchHeading'); ?>"><?php _e('Search Title:','documentate'); ?></label>
            <input id="<?php echo $this->get_field_id('txtKbeSearchHeading'); ?>" name="<?php echo $this->get_field_name('txtKbeSearchHeading'); ?>" value="<?php echo $widgetData['txtKbeSearchHeading']; ?>" style="width:275px;" />
        </p>
<?php
    }
}
?>