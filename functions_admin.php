<?php
// Add admin menu option
function search_simple_fields_admin_menu() {
	add_submenu_page( 'options-general.php' , "Search Simple Fields", "Search Simple Fields", "administrator", "search-simple-fields-options", "search_simple_fields_options");
}

// Used for debug
function ssf_debug($data, $clr = 'red')
{
    echo "<pre style='color:{$clr};'>"; print_r($data); echo "</pre>";
}

function search_simple_fields_options()
{ 
    // process POST information
     if(isset($_POST['save-changes'])):
        $post_types_for_search      = (isset($_POST['post_types'])) ? $_POST['post_types'] : null;
        $custom_fields_for_search   = (isset($_POST['custom_fields'])) ? $_POST['custom_fields'] : null;
        $media_function             = (isset($_POST[SSF_MEDIA_FUNCTION])) ? $_POST[SSF_MEDIA_FUNCTION] : '';
        $media_fields               = (isset($_POST['media_fields']) && array_filter($_POST['media_fields'])) ? $_POST['media_fields'] : null;
        if(!is_null($post_types_for_search))
            update_option(SSF_POST_TYPES_FOR_SEARCH, $post_types_for_search);
        else
            delete_option(SSF_POST_TYPES_FOR_SEARCH);
        if(!is_null($custom_fields_for_search))
            update_option(SSF_CUSTOM_FIELDS_FOR_SEARCH, $custom_fields_for_search);
        else
            delete_option(SSF_CUSTOM_FIELDS_FOR_SEARCH);
        if($media_function)
            update_option(SSF_MEDIA_FUNCTION, $media_function);
        else
            delete_option(SSF_MEDIA_FUNCTION);
        
        if(!is_null($media_fields))
            update_option(SSF_MEDIA_FIELDS_FOR_SEARCH, $media_fields);
        else
            delete_option(SSF_MEDIA_FIELDS_FOR_SEARCH);
    endif;
    $old_post_types = get_option(SSF_POST_TYPES_FOR_SEARCH);
    $post_types = get_post_types('', 'objects');
    ?>

<div class="wrap">
    <h2><?php _e('Defined Simple Fields');?></h2>
    <form action="" method="POST">
    <div class="simple-fields-list">
        <div class="ssf-list-title"><?php _e('Post types to search in');?></div>
        <?php if($post_types): ?>
        <ul class="post-types-list">
            <?php foreach($post_types as $name => $obj):
                $checked = ($old_post_types && in_array($name, $old_post_types)) ? "checked" : "";
                ?>
            <li><input type="checkbox" name="post_types[]" value="<?php echo $name;?>" <?php echo $checked;?> /> <?php echo $obj->labels->name;?></li>
        <?php endforeach; ?>
        </ul>
        <?php endif;?>
        <div class="ssf-horizontal-line"><!-- --></div>
<?php if(is_plugin_active('simple-fields/simple_fields.php')) :  
    $old_custom_fields  = get_option(SSF_CUSTOM_FIELDS_FOR_SEARCH);
    $field_groups       = get_option("simple_fields_groups");
    ?>
    
        <div class="ssf-list-title"><?php _e('Custom Simple Fields to search in');?></div>
        <?php if($field_groups && is_array($field_groups)): ?>
        <ul class="custom-fields-list">
            <?php 
            foreach($field_groups as $k => $group): ?>
            <li><strong><?php echo $group['name'];?></strong></li>
            <?php if($group['fields']):
                foreach($group['fields'] as $j => $field) :
                    $field_value = "_simple_fields_fieldGroupID_{$k}_fieldID_{$j}_numInSet_";
                    $checked = ($old_custom_fields && in_array($field_value, $old_custom_fields)) ? "checked" : "";
                    ?>
            <li class="custom-field-item"><input type="checkbox" name="custom_fields[]" value="<?php echo $field_value;?>" <?php echo $checked; ?>> <?php echo $field['name'] . ' - ' . $field['type']; ?></li>
        <?php 
        endforeach;
             endif;
            endforeach; ?>
        </ul>
        <?php endif;?>
<?php else: ?>
    <h2>Warning</h2>
    <div class="ssf-warning-class">
        <?php _e("<a href='http://eskapism.se/code-playground/simple-fields/' target='_blank'>Simple Fields</a> plugin is not active in your Wordpress installation.");?>
    </div>
<?php endif; 
    $old_media_fields   = get_option(SSF_MEDIA_FIELDS_FOR_SEARCH);
    $media_fields       = ssf_get_media_fields();
?>
    <div class="ssf-horizontal-line"><!-- --></div>
    <h2><?php _e('Advanced options');?></h2>
    <div class="ssf-list-title"><?php _e('Media custom fields'); ?></div>
    <?php $media_function = get_option(SSF_MEDIA_FUNCTION);
    if($media_function) :
        $media_custom_fields = ssf_get_media_fields();
        if($media_custom_fields): ?>
    <ul class="post-types-list">
        <?php $i = 0;    
            foreach($media_custom_fields as $k => $v) :
                $field_input = ($old_media_fields && isset($old_media_fields[$i])) ? $old_media_fields[$i] : '_' . $k;
            ?>
        <li>
            <?php _e('Custom media field');?>: <input type="text" name="media_fields[]" size="20" value="<?php echo $field_input;?>"> <strong><?php echo $v['label'];?></strong> - <?php _e('type');?>: <em><?php echo $v['input'];?></em>
        </li>
            <?php $i++; 
            endforeach; ?>
    </ul>
        <?php endif;
    endif; ?>
    <?php _e('Custom function you use to define custom media fields');?>: <input type="text" name="<?php echo SSF_MEDIA_FUNCTION;?>" size="40" value="<?php echo $media_function;?>"/>    
    </div>
    <input type="submit" value="Save Changes" name="save-changes"/>
    </form>
</div>
<?php }

function ssf_get_media_fields()
{
    global $wpdb;
    $media_function = get_option(SSF_MEDIA_FUNCTION);
    $value = array();
    if($media_function) {
        $media_posts    = get_posts(array('post_type' => 'attachment', 'numberposts' => 1));
        $attach_ids     = array();
        $value = apply_filters('attachment_fields_to_edit', 'vscan_image_attachment_fields_to_edit', $media_posts[0]->ID);
    }
    return $value;
}
?>
