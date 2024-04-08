<?php
/*
Plugin Name: Dashboard
Description: A plugin to take input from users on the frontend and display all inputs in a custom menu page in the WordPress dashboard.
Version: 1.0
Author: Your Name
*/

// Enqueue scripts and styles
function user_input_dashboard_enqueue_scripts() {
    // Enqueue CSS
    wp_enqueue_style('user-input-dashboard-styles', plugins_url('css/styles.css', __FILE__));
}
add_action('wp_enqueue_scripts', 'user_input_dashboard_enqueue_scripts');

// Create frontend form shortcode
function user_input_form_shortcode() {
    ob_start();
    ?>
    <style>
        /* Internal CSS for styling form */
        #user-input-form label {
            display: block;
            margin-bottom: 10px;
        }

        #user-input-form input[type="text"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            box-sizing: border-box;
        }

        #user-input-form input[type="submit"] {
            background-color: #0073e6;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
        }

        #user-input-form input[type="submit"]:hover {
            background-color: #005bb5;
        }
    </style>
    <form id="user-input-form" action="" method="post">
        <label for="user-input">Name :</label>
        <input type="text" id="user-input" name="user_input[]" required><br>
        <label for="user-input2">Address :</label>
        <input type="text" id="user-input2" name="user_input[]" required><br>
        <label for="user-input3">Phone Number :</label>
        <input type="text" id="user-input3" name="user_input[]" required><br>
        <label for="user-input4">Message :</label>
        <input type="text" id="user-input4" name="user_input[]" required><br>
        <input type="submit" value="Submit">
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode('user_input_form', 'user_input_form_shortcode');

// Handle form submission
function handle_user_input_submission() {
    if (isset($_POST['user_input'])) {
        $user_inputs = array_map('sanitize_text_field', $_POST['user_input']);
        $previous_data = get_option('user_input_data', array());
        $previous_data[] = $user_inputs;
        update_option('user_input_data', $previous_data);
    }
}
add_action('init', 'handle_user_input_submission');

// Add custom menu page to display user input
function user_input_dashboard_menu_page() {
    add_menu_page(
        'User Input Data',
        'User Input Data',
        'manage_options',
        'user_input_data_page',
        'display_user_input_data_page'
    );
}
add_action('admin_menu', 'user_input_dashboard_menu_page');

// Display user input data on custom menu page in tabular format
function display_user_input_data_page() {
    // Check user capability
    if (!current_user_can('manage_options')) {
        return;
    }

    $user_input_data = get_option('user_input_data', array());

    // Ensure user_input_data is an array
    if (!is_array($user_input_data)) {
        $user_input_data = array(); // Reset to an empty array if not in the correct format
    }

    ?>
    <style>
        /* Internal CSS for styling table */
        .user-input-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .user-input-table th,
        .user-input-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .user-input-table th {
            background-color: #f2f2f2;
        }
    </style>
    <div class="wrap">
        <h1>User Input Data</h1>
        <table class="user-input-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Phone Number</th>
                    <th>Message</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($user_input_data as $inputs) : ?>
                    <?php if (is_array($inputs)) : ?>
                        <tr>
                            <?php foreach ($inputs as $input) : ?>
                                <td><?php echo esc_html($input); ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}
