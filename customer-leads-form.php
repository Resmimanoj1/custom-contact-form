<?php
/*
Plugin Name: Customer Contact Form
Plugin URI: #
Description: Simple WordPress Contact Form By Resmi Manoj
Version: 1.0
Author: Resmi Manoj
Author URI: #
Created On: 10-09-2021
*/

/**
 * Enqueue scripts and styles
 */

function add_my_scripts() {
    wp_enqueue_script(
        'jScript',
        'https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js', 
        array('jquery')
    );

    wp_enqueue_script(
        'swal',
        'https://unpkg.com/sweetalert/dist/sweetalert.min.js',
        array('jquery')                     
    );
} 

add_action( 'wp_enqueue_scripts', 'add_my_scripts' );


/**
 * Create a new table for storing Customer-details while plugin activation.
 */
function createCustomerLeadsTable() 
{      
  global $wpdb; 
  $db_table_name = $wpdb->prefix . 'customer_leads';  // Table name
  $charset_collate = $wpdb->get_charset_collate();
  
  if ( $wpdb->get_var( "SHOW TABLES LIKE '{$db_table_name}'" ) != $db_table_name ) {
    $sql = "CREATE TABLE $db_table_name (
        id int(11) NOT NULL auto_increment,
        name varchar(250) NOT NULL,
        email varchar(250) NOT NULL,
        phone varchar(100) NOT NULL,
        service_required varchar(100) NOT NULL,
        created_on TIMESTAMP NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

   require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
   dbDelta( $sql );
   add_option( 'test_db_version', $test_db_version );
  } 
}

register_activation_hook( __FILE__, 'createCustomerLeadsTable' );

function html_form_code() { ?>
    <form method ="POST" action="<?php echo plugin_dir_url( __FILE__ ); ?>customerForm.php" id="customerForm">
    <p>Name (required) <br/>
        <input type="text" name="cf-name" required="" />
    </p>
    <p>
        Email (required) <br/>
        <input type="email" name="cf-email" required="" />
    </p>
    <p>
        Phone Number (required) <br/>
        <input type="text" name="cf-phone" required="" />
    </p>
    <p>
        Service Required (required) <br/>
        <select name="cf-service" required="">
            <option value="" selected disabled hidden>-Select-</option>
            <option value="Electricity">Electricity</option>
            <option value="Internet">Internet</option>
            <option value="Solar">Solar</option>
        </select>
    </p>
    <p style="margin-top: 20px;">
        <input type="submit" name="cf-submitted" value="Submit" id="btnSubmit"></p>
    </form>
    <div id="form_msg" style="color:#00e209;"></div>
    
    <script>
        $("#customerForm").submit(function(e) {
            e.preventDefault(); 
            var form = $(this);
            var url = form.attr('action');
            
            $.ajax({
                type: "POST",
                url: url,
                data: form.serialize(), 
                success: function(data) {
                    swal("Success!", "Data submitted successfully", "success", {
                        button: "OK",
                    }).then(function() {
                        window.location.reload();
                    }).catch(function(reason){
                    });    
                }
            });
            e.preventDefault(); 
        });   
    </script> 
<?php
}


function cf_shortcode() {
    ob_start();
    html_form_code();
    return ob_get_clean();
}

add_shortcode( 'customer_contact_form', 'cf_shortcode' );

function sd_register_top_level_menu(){
    add_menu_page(
        'Customer Leads',
        'Leads',
        'manage_options',
        'mymenupage',
        'sd_display_top_level_menu_page',
        '',
        6
    );
}
add_action( 'admin_menu', 'sd_register_top_level_menu' );

function sd_display_top_level_menu_page() {
    global $wpdb;
    $table_name = 'wp_customer_leads';
    
    $customer_leads = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY created_on DESC"); 

    $result = (array) $customer_leads;
    $i = 0;
    ?>
    <style>
        table, td, th {  
            border: 1px solid #ddd;
            text-align: left;
        }

        table {
            border-collapse: collapse;
            width: 75%;
        }

        th, td {
            padding: 15px;
        }

        .text-center {
            text-align: center;
        }

        .text-bold {
            font-weight: bold
        }
    </style>

<?php
    echo '<h2>Customer Leads</h2>';
    
    echo '<table class="table table-striped">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Name</th>
            <th scope="col">Email</th>
            <th scope="col">Service Requested</th>
            <th scope="col">Date submitted</th>
        </tr>
    </thead>
    <tbody>';
        if (!empty($customer_leads)) {
            foreach($customer_leads as $key => $data) {
                $i++;
                echo '<tr>
                <th scope="row">' . $i  . '</th>
                <td>' . $data->name  . '</td>
                <td>' . $data->email  . '</td>
                <td>' . $data->service_required  . '</td>
                <td>' . $data->created_on  . '</td>
                </tr>';
            }
        } else {
            echo'<td colspan="5" class="text-center text-bold">
                No Data Found
            </td>
    </tbody>';
    }
    echo '</table>';  
}
?>