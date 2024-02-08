<?php
/*
Plugin Name: Car Clean Up Manager
Description: Retrieves and displays data for cars with status draft on a separate admin page.
Version: 1.1.0
Author: John Høj Andreassen
*/

if ( !defined( 'ABSPATH' ) ) exit;


// Act on plugin activation
register_activation_hook( __FILE__, "activate__car_clean_up_plugin" );

// Act on plugin de-activation
register_deactivation_hook( __FILE__, "deactivate__car_clean_up_plugin" );

// Activate Plugin
function activate_myplugin() {

	// Execute tasks on Plugin activation

	// Insert DB Tables
	// init_db__car_clean_up_plugin();
}

// De-activate Plugin
function deactivate__car_clean_up_plugin() {

	// Execute tasks on Plugin de-activation
}

// Initialize DB Tables
function init_db__car_clean_up_plugin() {

	global $wpdb;

    $table_name = $wpdb->prefix . 'akfoer_car_stats';

    $charset_collate = $wpdb->get_charset_collate();



    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id INT AUTO_INCREMENT PRIMARY KEY,
        post_ID VARCHAR(255) NULL,
        created VARCHAR(255) NULL,
        modified VARCHAR(255) NULL,
        title VARCHAR(255) NULL,
        final_price VARCHAR(255) NULL,
        sale_price VARCHAR(255) NULL,
        car_status VARCHAR(255) NULL,
        website_link VARCHAR(255) NULL,
        vehicle_overview VARCHAR(255) NULL,
        year VARCHAR(255) NULL,
        make VARCHAR(255) NULL,
        model VARCHAR(255) NULL,
        plate VARCHAR(255) NULL,
        seats VARCHAR(255) NULL,
        doors VARCHAR(255) NULL,
        status VARCHAR(255) NULL,
        type VARCHAR(255) NULL,
        transmission VARCHAR(255) NULL,
        fuel_type VARCHAR(255) NULL,
        mileage VARCHAR(255) NULL,
        area VARCHAR(255) NULL,
        phone VARCHAR(255) NULL,
        dealer VARCHAR(255) NULL
    ) $charset_collate;";
    // DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}


function display_car_data_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'akfoer_car_stats';
    $table_name = $wpdb->prefix . 'posts';
    $postsQuery = $wpdb->prepare(
        "
        SELECT *
        FROM {$wpdb->prefix}posts
        WHERE post_type = %s AND post_status = %s AND post_date < now() - interval 90 DAY
        ORDER BY post_date DESC
        ",
        'cars',
        'draft'
    );

    $postResults = $wpdb->get_results($postsQuery);

    foreach ($postResults as $post) {
        $ID = $post->ID;
        $created = $post->post_date;
        $title = $post->post_title;
        $modified = $post->post_modified;
        $final_price = get_post_meta($post->ID, 'final_price', true) ?? null;
        $sale_price = get_post_meta($post->ID, 'sale_price', true) ?? null;
        $car_status = get_post_meta($post->ID, 'car_status', true) ?? null;
        $website_link = get_post_meta($post->ID, 'website_link', true) ?? null;
        $vehicle_overview = get_post_meta($post->ID, 'vehicle_overview', true) ?? null;
        
        $year = get_the_terms($post->ID, 'car_year')[0]->name ?? null;
        $make = get_the_terms($post->ID, 'car_make')[0]->name ?? null;
        $model = get_the_terms($post->ID, 'car_model')[0]->name ?? null;
        $plate = get_the_terms($post->ID, 'car_vin_number')[0]->name ?? null;
        $seats = get_the_terms($post->ID, 'car_trim')[0]->name ?? null;
        $doors = get_the_terms($post->ID, 'car_vin_number')[0]->name ?? null;
        $condition = get_the_terms($post->ID, 'car_condition')[0]->name ?? null;
        $type = get_the_terms($post->ID, 'car_body_style')[0]->name ?? null;
        $transmission = get_the_terms($post->ID, 'car_transmission')[0]->name ?? null;
        $engine = get_the_terms($post->ID, 'car_engine')[0]->name ?? null;
        $fuel_type = get_the_terms($post->ID, 'car_fuel_type')[0]->name ?? null;
        $mileage = get_the_terms($post->ID, 'car_mileage')[0]->name ?? null;
        $area = get_the_terms($post->ID, 'car_exterior_color')[0]->name ?? null;
        $phone = get_the_terms($post->ID, 'car_interior_color')[0]->name ?? null;
        $dealer = get_the_terms($post->ID, 'dealer')[0]->name ?? null;

       

        $wpdb->insert($table_name, array(
            'post_ID' => $ID, 
            'created' =>  $created, 
            'modified' => $modified, 
            'title' => $title, 
            'final_price' => $final_price, 
            'sale_price' => $sale_price, 
            'car_status' => $car_status, 
            'website_link' => $website_link, 
            'vehicle_overview' => $vehicle_overview, 
            'year' => $year,
            'make' => $make,
            'model' => $model,
            'plate' => $plate, 
            'seats' => $seats, 
            'doors' => $doors, 
            'status' => $condition, 
            'type' => $type, 
            'transmission' => $transmission, 
            'fuel_type' => $fuel_type, 
            'mileage' => $mileage, 
            'area' => $area, 
            'phone' => $phone, 
            'dealer' => $dealer)
        ); 

        $wpdb->delete(
            $table_name,
            array(
                'ID' => $ID,
            )
        );
    }
    $count = count($postResults);
    $charset_collate = $wpdb->get_charset_collate();
    echo '<div class="wrap">';
        echo '<h1>Car Clean Up Manager</h1>';
        echo '<div class="car-data-display">';
        echo "<h2>Count</h2>";
        echo "<div class='Count'>$count</div>";
        echo '</div>';
    echo '</div>';
}

function add_car_cleanup_manager_menu() {
    add_menu_page(
        'Car Clean Up Manager',
        'Clean Up Cars',
        'manage_options',
        'car_cleanup_manager',
        'display_car_data_page',
        'dashicons-car', // Change this icon as needed
        20
    );
}



// Hook the add_car_cleanup_manager_menu function to the admin_menu action
add_action('admin_menu', 'add_car_cleanup_manager_menu');

function getTermFromMetaValue($value, $postID){
    $meta_value = get_post_meta($postID, $value, true);
    if($meta_value){
        // var_dump(get_term($meta_value)->name);
        return get_term($meta_value)->name;
    } else {
        return $meta_value;
    }
}

?>
