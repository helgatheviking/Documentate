<?php
/*===============
    Documentate Documents Widget
 ===============*/
 
//========= Custom Knowledgebase Document Widget
add_action( 'widgets_init', 'documentate_document_widgets' );
function documentate_document_widgets() {
    register_widget( 'Documentate_Document_Widget' );
}

//========= Custom Knowledgebase Document Widget Body
class Documentate_Document_Widget extends WP_Widget {
    
    //=======> Widget setup
    function __construct() {
        parent::__construct(
            'documentate_document_widget', // Base ID
            __( 'Knowledgebase Document', 'documentate' ), // Name
            array( 'description' => __( 'Widget to show documents on the site', 'documentate' ), 
                        'classname' => 'documentate' ) // Args
        );
    }
    
    //=======> How to display the widget on the screen.
    function widget($args, $widgetData) {
        extract($args);
        
        //=======> Our variables from the widget settings.
        $documentate_widget_document_title = $widgetData['DocumentHeading'];
        $documentate_widget_document_count = $widgetData['DocumentCount'];
        $documentate_widget_document_order = $widgetData['DocumentOrder'];
        $documentate_widget_document_orderby = $widgetData['DocumentOrderBy'];
        
        //=======> widget body
        echo $before_widget;
        echo '<div class="documentate_widget documentate_widget_document">';
        
                if($documentate_widget_document_title){
                    echo '<h2>'.$documentate_widget_document_title.'</h2>';
                }
                
                if($documentate_widget_document_orderby == 'popularity'){
                    $documentate_widget_document_args = array( 
                        'posts_per_page' => $documentate_widget_document_count, 
                        'post_type'  => 'documentate',
                        'orderby' => 'meta_value_num',
                        'order'	=>	$documentate_widget_document_order,
                        'meta_key' => 'documentate_post_views_count'
                    );
                }
                else{
                    $documentate_widget_document_args = array(
                        'post_type' => 'documentate',
                        'posts_per_page' => $documentate_widget_document_count,
                        'order' => $documentate_widget_document_order,
                        'orderby' => $documentate_widget_document_orderby
                   );
                }
                
                $documentate_widget_documents = new WP_Query($documentate_widget_document_args);
                if($documentate_widget_documents->have_posts()) :
            ?>
                <ul>
            <?php
                    while($documentate_widget_documents->have_posts()) :
                        $documentate_widget_documents->the_post();
            ?>
                        <li>
                            <a href="<?php the_permalink() ?>" title="<?php the_title_attribute() ?>">
                                <?php the_title() ?>
                            </a>
                        </li>
            <?php
                    endwhile;
            ?>
                </ul>
            <?php
                endif;
                
                wp_reset_query();
                
        echo "</div>";
        echo $after_widget;
    }
    
    //Update the widget 
    function update($new_widgetData, $old_widgetData) {
        $widgetData = $old_widgetData;
		
        //Strip tags from title and name to remove HTML 
        $widgetData['DocumentHeading'] = $new_widgetData['DocumentHeading'];
        $widgetData['DocumentCount'] = $new_widgetData['DocumentCount'];
        $widgetData['DocumentOrder'] = $new_widgetData['DocumentOrder'];
        $widgetData['DocumentOrderBy'] = $new_widgetData['DocumentOrderBy'];
		
        return $widgetData;
    }
    
    function form($widgetData) {
        //Set up some default widget settings.
        $widgetData = wp_parse_args((array) $widgetData);
?>
        <p>
            <label for="<?php echo $this->get_field_id('DocumentHeading'); ?>"><?php _e('Document Title:','documentate'); ?></label>
            <input id="<?php echo $this->get_field_id('DocumentHeading'); ?>" name="<?php echo $this->get_field_name('DocumentHeading'); ?>" value="<?php echo $widgetData['DocumentHeading']; ?>" style="width:275px;" />
        </p>    
        <p>
            <label for="<?php echo $this->get_field_id('DocumentCount'); ?>"><?php _e('Documents Quantity:','documentate') ?></label>
            <input id="<?php echo $this->get_field_id('DocumentCount'); ?>" name="<?php echo $this->get_field_name('DocumentCount'); ?>" value="<?php echo $widgetData['DocumentCount']; ?>" style="width:275px;" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('DocumentOrder'); ?>"><?php _e('Documents Order:','documentate') ?></label>
            <select id="<?php echo $this->get_field_id('DocumentOrder'); ?>" name="<?php echo $this->get_field_name('DocumentOrder'); ?>">
                <option <?php selected($widgetData['DocumentOrder'], 'ASC') ?> value="ASC"><?php _e('ASC','documentate'); ?></option>
                <option <?php selected($widgetData['DocumentOrder'], 'DESC') ?> value="DESC"><?php _e('DESC','documentate'); ?></option>
            </select>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('DocumentOrderBy'); ?>"><?php _e('Documents Order by:','documentate') ?></label>
            <select id="<?php echo $this->get_field_id('DocumentOrderBy'); ?>" name="<?php echo $this->get_field_name('DocumentOrderBy'); ?>">
                <option <?php selected($widgetData['DocumentOrderBy'], 'name') ?> value="name"><?php _e('By Name','documentate'); ?></option>
                <option <?php selected($widgetData['DocumentOrderBy'], 'date') ?> value="date"><?php _e('By Date','documentate'); ?></option>
                <option <?php selected($widgetData['DocumentOrderBy'], 'rand') ?> value="rand"><?php _e('By Random','documentate'); ?></option>
                <option <?php selected($widgetData['DocumentOrderBy'], 'popularity') ?> value="popularity"><?php _e('By Popularity','documentate'); ?></option>
                <option <?php selected($widgetData['DocumentOrderBy'], 'comment_count') ?> value="comment_count"><?php _e('By Comments','documentate') ?></option>
            </select>
        </p>
<?php
    }
}
?>