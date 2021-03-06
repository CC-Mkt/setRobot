<?php

$fn_end_while = function () {
    return '<?php endwhile; wp_reset_query(); ?>';
};

$fn_end_if = function () {
    return '<?php endif; ?>';
};

return [
    /*
    |--------------------------------------------------------------------------
    | Directives
    |--------------------------------------------------------------------------
    |
    | List here your custom directives
    |
    */

    /** Create @asset() Blade directive */
    'asset' => function ($asset) {
        return "<?php echo App\asset_path({$asset}); ?>";
    },

    /** Creates @mainquery Blade directive */
    'mainquery' => function () {
        return '<?php while(have_posts()) : the_post(); ?>';
    },

    /** Creates @endmainquery Blade directive */
    'endmainquery' => $fn_end_while,

    /** Creates @customquery(\WP_Query $query_obj) Blade directive */
    'customquery' => function ($query_obj) {
        $output = "<?php while ({$query_obj}->have_posts()) : ?>";
        $output .= "<?php {$query_obj}->the_post(); ?>";
        return $output;
    },

    /** Creates @endcustomquery Blade directive */
    'endcustomquery' => $fn_end_while,

    /** Creates @loggedin Blade directive */
    'loggedin' => function () {
        return "<?php if(is_user_logged_in()) : ?>";
    },

    /** Creates @endloggedin Blade directive */
    'endloggedin' => $fn_end_if,

    /** Creates @visitor Blade directive */
    'visitor' => function () {
        return "<?php if(!is_user_logged_in()) : ?>";
    },

    /** Creates @endvisitor Blade directive */
    'endvisitor' => $fn_end_if,

    /** Create @shortcode($shortCodeString) Blade directive */
    'shortcode' => function ($shortcode) {
        return "<?php echo do_shortcode({$shortcode}); ?>";
    },

    /** Create @inlinesvg() Blade directive */
    'inlinesvg' => function ($path) {
        return "<?php App\get_svg({$path}, true); ?>";
    },

    /** Create @dump($obj) Blade directive */
    'dump' => function ($obj) {
        return "<?php App\dump({$obj}); ?>";
    },

    /** Create @console($obj) Blade directive */
    'console' => function ($obj) {
        return "<?php App\console({$obj}); ?>";
    },

    /** Create @getimage */
    'getimage' => function ($obj) {
        return "<?php echo \App\get_image({$obj}); ?>";
    },
];
