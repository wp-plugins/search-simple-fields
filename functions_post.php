<?php
function search_simple_fields_search_posts()
{
    if(get_option(SSF_POST_TYPES_FOR_SEARCH)) {

        function ssf_search_filter($query) {
            $post_types_for_search = get_option(SSF_POST_TYPES_FOR_SEARCH);
            if(is_array($post_types_for_search))
                if(!is_null(get_option(SSF_MEDIA_FIELDS_FOR_SEARCH)) && is_array(get_option(SSF_MEDIA_FIELDS_FOR_SEARCH)))
                    $post_types_for_search = array_merge($post_types_for_search, array('attachment'));
                else
                    $post_types_for_search = array('attachment');
            if ($query->is_search) 
                $query->set('post_type', $post_types_for_search);
            
            return $query;
        }

        add_filter('pre_get_posts','ssf_search_filter');
    }

    function ssf_search_distinct() {
	return "DISTINCT";
    }
    add_filter('posts_distinct', 'ssf_search_distinct');

    if(get_option(SSF_CUSTOM_FIELDS_FOR_SEARCH) || get_option(SSF_MEDIA_FIELDS_FOR_SEARCH) || get_option(SSF_WP_FIELDS_FOR_SEARCH)) {

        function ssf_custom_search_join($join) {
            // put the custom fields into an array
            global $wpdb;
            if(is_search()) {
                $join .= " LEFT JOIN $wpdb->postmeta ON $wpdb->postmeta.post_id = $wpdb->posts.ID";
            }
            return $join;
        }
        add_filter('posts_join', 'ssf_custom_search_join');

        // THE MOST IMPORTANT PART!
        function ssf_custom_search_where($where) {
            $old_where      = $where;
            $custom_sf      = array();
            $custom_media   = array();
            $custom_wp      = array();
            if (isset($_REQUEST['s'])) {
                global $wpdb;
                if(!is_null(get_option(SSF_CUSTOM_FIELDS_FOR_SEARCH)) && is_array(get_option(SSF_CUSTOM_FIELDS_FOR_SEARCH)))
                    $custom_sf      =  get_option(SSF_CUSTOM_FIELDS_FOR_SEARCH);
                if(!is_null(get_option(SSF_MEDIA_FIELDS_FOR_SEARCH)) && is_array(get_option(SSF_MEDIA_FIELDS_FOR_SEARCH)))
                    $custom_media   =  get_option(SSF_MEDIA_FIELDS_FOR_SEARCH);
                if(!is_null(get_option(SSF_WP_FIELDS_FOR_SEARCH)) && is_array(get_option(SSF_WP_FIELDS_FOR_SEARCH)))
                    $custom_wp      =  get_option(SSF_WP_FIELDS_FOR_SEARCH);
                
                $query = '';
                $var_q = stripslashes($_REQUEST['s']);
                if ($_REQUEST['sentence']) {
                    $search_terms = array($var_q);
                } else {
                    preg_match_all('/".*?("|$)|((?<=[\\s",+])|^)[^\\s",+]+/', $var_q, $matches);
                    $search_terms = array_map(create_function('$a', 'return trim($a, "\\"\'\\n\\r ");'), $matches[0]);
                }
                $n = ($_REQUEST['exact']) ? '' : '%';
                $searchand = '';
                
                foreach((array)$search_terms as $term) {
                    $term = addslashes_gpc($term);
                    $query .= "{$searchand}(";
                        $query .= "($wpdb->posts.post_title LIKE '{$n}{$term}{$n}')";
                        $query .= " OR ($wpdb->posts.post_content LIKE '{$n}{$term}{$n}')";
                        if($custom_sf) {
                            foreach(array_filter($custom_sf) as $custom) {
                                $query .= " OR (";
                                $query .= "($wpdb->postmeta.meta_key LIKE ('{$custom}{$n}'))";
                                $query .= " AND ($wpdb->postmeta.meta_value LIKE '{$n}{$term}{$n}')";
                                $query .= ")";
                            }
                        }
                        if($custom_wp) {
                            foreach(array_filter($custom_wp) as $custom) {
                                $query .= " OR (";
                                $query .= "($wpdb->postmeta.meta_key = '{$custom}')";
                                $query .= " AND ($wpdb->postmeta.meta_value LIKE '{$n}{$term}{$n}')";
                                $query .= ")";
                            }
                        }
                        if($custom_media) { 
                            $media_post_ids = ssf_get_media_post_ids($term);
                            if($media_post_ids)
                                $query .= " OR $wpdb->posts.ID IN (" . implode(',', $media_post_ids) . ") ";
                        }
                        $query .= ")";
                        $searchand = ' AND ';
                    }
                    $term = $wpdb->escape($var_q);
                    if (!$_REQUEST['sentense'] && count($search_terms) > 1 && $search_terms[0] != $var_q) {
                        $search .= " OR ($wpdb->posts.post_title LIKE '{$n}{$term}{$n}')";
                        $search .= " OR ($wpdb->posts.post_content LIKE '{$n}{$term}{$n}')";
                    } if (!empty($query)) {
                        $where = " AND ({$query})  AND ($wpdb->posts.post_status = 'publish') ";
                    }
                }
            return($where);
        }
        add_filter('posts_where', 'ssf_custom_search_where');
    }
}

function ssf_get_media_post_ids($term)
{
    global $wpdb;
    $ids    = array();
    $fields = get_option(SSF_MEDIA_FIELDS_FOR_SEARCH);
    $fields = array_filter($fields);
    $sql = "SELECT ID FROM $wpdb->posts 
                WHERE ID IN (
                    SELECT post_id FROM $wpdb->postmeta
                        WHERE meta_value IN (
                            SELECT post_id FROM $wpdb->postmeta AS b 
                                WHERE ( 1 = 0 "; 
    foreach($fields as $f) {
        $sql .= " OR (b.meta_key='{$f}' AND b.meta_value LIKE ('%{$term}%')) ";
    }
    $sql .= ") 
        )
    )
    AND post_status='publish'";
    $data = $wpdb->get_results($sql);
    if($data) {
        foreach($data as $post) 
            $ids[] = $post->ID;
    }
    return $ids;
}
?>
