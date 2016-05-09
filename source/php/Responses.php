<?php

namespace CustomerFeedback;

class Responses
{
    public $postTypeSlug = 'customer-feedback';

    public function __construct()
    {
        add_action('init', array($this, 'registerPostType'));

        // Submit response ajax
        add_action('wp_ajax_submit_response', array($this, 'submitResponse'));
        add_action('wp_ajax_nopriv_submit_response', array($this, 'submitResponse'));

        // Submit comment ajax
        add_action('wp_ajax_submit_comment', array($this, 'submitComment'));
        add_action('wp_ajax_nopriv_submit_comment', array($this, 'submitComment'));
    }

    /**
    * Registers a new post type
    * @uses $wp_post_types Inserts new post type object into the list
    *
    * @param string  Post type key, must not exceed 20 characters
    * @param array|string  See optional args description above.
    * @return object|WP_Error the registered post type object, or an error object
    */
    public function registerPostType()
    {
        $nameSingular = 'Feedback';
        $namePlural = 'Feedback';
        $description = 'Create shortlinks to your posts or pages';

        $labels = array(
            'name'               => _x($nameSingular, 'post type general name', 'customer-feedback'),
            'singular_name'      => _x($nameSingular, 'post type singular name', 'customer-feedback'),
            'menu_name'          => _x($namePlural, 'admin menu', 'customer-feedback'),
            'name_admin_bar'     => _x($nameSingular, 'add new on admin bar', 'customer-feedback'),
            'add_new'            => _x('Add New', 'add new button', 'customer-feedback'),
            'add_new_item'       => sprintf(__('Add new %s', 'customer-feedback'), $nameSingular),
            'new_item'           => sprintf(__('New %s', 'customer-feedback'), $nameSingular),
            'edit_item'          => sprintf(__('Edit %s', 'customer-feedback'), $nameSingular),
            'view_item'          => sprintf(__('View %s', 'customer-feedback'), $nameSingular),
            'all_items'          => sprintf(__('All %s', 'customer-feedback'), $namePlural),
            'search_items'       => sprintf(__('Search %s', 'customer-feedback'), $namePlural),
            'parent_item_colon'  => sprintf(__('Parent %s', 'customer-feedback'), $namePlural),
            'not_found'          => sprintf(__('No %s', 'customer-feedback'), $namePlural),
            'not_found_in_trash' => sprintf(__('No %s in trash', 'customer-feedback'), $namePlural)
        );

        $args = array(
            'labels'               => $labels,
            'description'          => __($description, 'customer-feedback'),
            'public'               => false,
            'publicly_queriable'   => false,
            'show_ui'              => true,
            'show_in_nav_menus'    => false,
            'show_in_menu'         => true,
            'has_archive'          => false,
            'rewrite'              => false,
            'hierarchical'         => false,
            'menu_position'        => 100,
            'exclude_from_search'  => true,
            'menu_icon'            => 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI1MTIiIGhlaWdodD0iNTEyIiB2aWV3Qm94PSIwIDAgNDY5Ljg2MiA0NjkuODYzIj48ZyBmaWxsPSIjRkZGIj48cGF0aCBkPSJNNDQ3LjYzMyAzOS41MzdoLTE5MS4xOWMtMTIuMjYgMC0yMi4yMzIgOS45NzItMjIuMjMyIDIyLjIzdjk4LjY1MmMwIDEyLjI1NyA5Ljk3NCAyMi4yMjggMjIuMjMzIDIyLjIyOGgxNi43ODd2MzkuMTZhNi45MiA2LjkyIDAgMCAwIDExLjQ0MiA1LjIzNWw1MS4zOC00NC4zOTZoMTExLjU4YzEyLjI1NyAwIDIyLjIzLTkuOTczIDIyLjIzLTIyLjIzdi05OC42NWMwLTEyLjI1OC05Ljk3LTIyLjIzLTIyLjIzLTIyLjIzek0yOTYuNjg2IDEyNy40NWMtOS43NDMgMC0xNy42NDItNy45LTE3LjY0Mi0xNy42NDRzNy44OTctMTcuNjQ0IDE3LjY0Mi0xNy42NDRjOS43NDYgMCAxNy42NDYgNy45IDE3LjY0NiAxNy42NDQtLjAwMiA5Ljc0My03LjkgMTcuNjQ0LTE3LjY0NiAxNy42NDR6bTU1LjM1IDBjLTkuNzQgMC0xNy42NDMtNy45LTE3LjY0My0xNy42NDRzNy45LTE3LjY0NCAxNy42NDQtMTcuNjQ0YzkuNzQ0IDAgMTcuNjQ1IDcuOSAxNy42NDUgMTcuNjQ0IDAgOS43NDMtNy45IDE3LjY0NC0xNy42NDUgMTcuNjQ0em01NS4zNTUgMGMtOS43NDIgMC0xNy42NDItNy45LTE3LjY0Mi0xNy42NDRzNy44OTgtMTcuNjQ0IDE3LjY0My0xNy42NDRjOS43NDYgMCAxNy42NDUgNy45IDE3LjY0NSAxNy42NDQtLjAwMiA5Ljc0My03LjkwMiAxNy42NDQtMTcuNjQ0IDE3LjY0NHpNMTQwLjg1NyAxMzQuOTU4Yy00My4wOTMgMC02Ni45NCAyMi4zMy02Ni41MDYgNjUuMzU4LjYzMiA1OC45MzIgMjUuNjc0IDk0LjY4MiA2Ni41MDcgOTQuMDU4IDAgMCA2Ni40NzMgMi42OTUgNjYuNDczLTk0LjA1OCAwLTQzLjAyOC0yMi40NTctNjUuMzU4LTY2LjQ3My02NS4zNTh6TTI1OC40MzQgMzM5LjEwM2wtNjYuNDItMjYuNDgtMTUuNTUzLTEzLjEwN2ExMC4zNSAxMC4zNSAwIDAgMC0xMy45ODUuNTc0bC0yMS42MjQgMjEuNTA0LTIxLjY4Ny0yMS41MWExMC4zNTYgMTAuMzU2IDAgMCAwLTEzLjk3NS0uNTY3bC0xNS41NTYgMTMuMTA2LTY2LjQyMyAyNi40ODRDMS44MiAzNDcuNjI3IDEuODM1IDQyMy4zNyAwIDQzMC4zMjdoMjgxLjY3NGMtMS44MzItNi45NC0xLjg0My04Mi43MTItMjMuMjQtOTEuMjI0eiIvPjwvZz48L3N2Zz4=',
            'supports'             => array('title')
        );

        register_post_type($this->postTypeSlug, $args);
    }

    /**
     * Saves the "Yes" or "No" response as counters in metadata
     * @return integer The last inserted id from db
     */
    public function submitResponse()
    {
        $insertedId = 'false';
        $postId = (isset($_POST['postid']) && is_numeric($_POST['postid'])) ? $_POST['postid'] : null;
        $answer = (isset($_POST['answer']) && strlen($_POST['answer']) > 0) ? $_POST['answer'] : null;

        if ($postId && $answer) {
            $insertedId = wp_insert_post(array(
                'post_type' => $this->postTypeSlug,
                'post_status' => 'publish',
                'post_title' => get_the_title($postId)
            ));

            update_field('customer_feedback_ip', $_SERVER['REMOTE_ADDR'], $insertedId);
            update_field('customer_feedback_answer', $answer, $insertedId);
        }

        echo $insertedId;
        wp_die();
    }

    /**
     * Save a comment response as metadata for the page commented on
     * @return string Always returns "true" as a string
     */
    public function submitComment()
    {
        $answerId = (isset($_POST['answerid']) && is_numeric($_POST['answerid'])) ? $_POST['answerid'] : null;
        $postId = (isset($_POST['postid']) && is_numeric($_POST['postid'])) ? $_POST['postid'] : null;
        $comment = (isset($_POST['comment']) && strlen($_POST['comment']) > 0) ? $_POST['comment'] : null;
        $commentType = (isset($_POST['commenttype']) && strlen($_POST['commenttype']) > 0) ? $_POST['commenttype'] : null;

        if ($answerId && $postId) {
            update_field('customer_feedback_comment', $comment, $answerId);
            update_field('customer_feedback_comment_type', $commentType, $answerId);

            echo 'true';
        } else {
            echo 'false';
        }

        wp_die();
    }
}
