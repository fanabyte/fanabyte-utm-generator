<?php
 /*
 * Plugin Name:       FanaByte - UTM Generator
 * Plugin URI:        https://fanabyte.com/themes-plugins/plugins/fanabyte-plugins/fanabyte-utm-generator/
 * Description:       This plugin gives you the ability to create UTM for Google Analytics.
 * Version:           1.0.0
 * Author:            FanaByte Academy
 * Author URI:        https://fanabyte.com
 * Text Domain:       fanabyte-utm-generator
 * Requires at least: 6.8
 * Requires PHP:      7.4
 * License: 	      GPLv2 or later
 * Domain Path:       /languages
 */

add_filter('plugin_action_links', 'add_help_link', 10, 2);

function add_help_link($links, $file) {
    // بررسی اینکه آیا افزونه مورد نظرتان فعال است یا نه
    if (plugin_basename(__FILE__) == $file) {
        // اضافه کردن لینک دلخواه به آرایه‌ی دکمه‌ها
$help_link_text=NULL;
if ( get_locale() === 'fa_IR' ) {
  $help_link_text = 'تنظیمات';}
 else {
  $help_link_text = 'Settings';}
  
        $help_link = '<a href="admin.php?page=fanabyte_utm_generator_home" style="color: #00a32a;">' . $help_link_text . '</a>';
        array_push($links, $help_link);
    }

    return $links;
}

 
function add_utm_generator_styles() {
    wp_enqueue_style('utm-generator-styles', plugins_url('assets/css/style.css', __FILE__));
}

add_action('wp_enqueue_scripts', 'add_utm_generator_styles');

function generate_utm_form() {
    $output = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $website = isset($_POST['website']) ? sanitize_text_field($_POST['website']) : '';
        $source = isset($_POST['source']) ? sanitize_text_field($_POST['source']) : '';
        $medium = isset($_POST['medium']) ? sanitize_text_field($_POST['medium']) : '';
        $campaign = isset($_POST['campaign']) ? sanitize_text_field($_POST['campaign']) : '';
        $term = isset($_POST['term']) ? sanitize_text_field($_POST['term']) : '';
        $content = isset($_POST['content']) ? sanitize_text_field($_POST['content']) : '';

        // Create the UTM parameters
        $utm = "https://$website/?utm_source=$source&utm_medium=$medium&utm_campaign=$campaign";
        if (!empty($term)) {
            $utm .= "&utm_term=$term";
        }
        if (!empty($content)) {
            $utm .= "&utm_content=$content";
        }

        // Display the UTM parameters and copy button
        $output .= '
            <div class="alert alert-success">یو تی ام نهایی - Final UTM<br>' . esc_url($utm) . '</div>
            <div class="form-group" style="text-align: center;">
                <button class="btn btn-success" id="copyButton">کپی کردن یو تی ام - Copy UTM</button>
            </div>
            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    const copyButton = document.getElementById("copyButton");
                    copyButton.addEventListener("click", function () {
                        const utmOutput = document.createElement("textarea");
                        utmOutput.value = "' . esc_js($utm) . '";
                        document.body.appendChild(utmOutput);
                        utmOutput.select();
                        document.execCommand("copy");
                        document.body.removeChild(utmOutput);
                        alert("یو تی او با موفقیت کپی شد! - UTM was copied!");
                    });
                });
            </script>
        ';
    }

    // Display the UTM form
    $output .= '
        <form method="post">
            <div class="form-group">
            <div style="text-align: right;">
			<span style="float: right;"><label for="website">آدرس وب سایت</label></span>
            <span style="float: left;"><label for="website">Website URL</label></span>
            </div>
                <input placeholder="برای مثال https://fanabyte.com" type="text" class="form-control" name="website" required>
            </div>
            <div class="form-group">
            <div style="text-align: right;">
			<span style="float: right;"><label for="source">منبع کمپین</label></span>
            <span style="float: left;"><label for="source">Campaign Source</label></span>
                <input placeholder="برای مثال google یا newsletter یا وبسایتی که کمپین را در آن قرار داده اید" type="text" class="form-control" name="source" required>
            </div>
            <div class="form-group">
            <div style="text-align: right;">
			<span style="float: right;"><label for="medium">رسانۀ کمپین</label></span>
            <span style="float: left;"><label for="medium">Campaign Medium</label></span>
                <input placeholder="برای مثال cpc، banner یا email" type="text" class="form-control" name="medium" required>
            </div>
            <div class="form-group">
            <div style="text-align: right;">
			<span style="float: right;"><label for="campaign">نام کمپین</label></span>
            <span style="float: left;"><label for="campaign">Campaign Name</label></span>
                <input placeholder="برای مثال summer_sale یا eid_nowruz (با حروف لاتین)" type="text" class="form-control" name="campaign" required>
            </div>
            <div class="form-group">
            <div style="text-align: right;">
			<span style="float: right;"><label for="term">عبارت کلیدی کمپین</label></span>
            <span style="float: left;"><label for="term">Campaign Term</label></span>
                <input placeholder="عبارت کلیدی برای تبلیغات در موتورهای جستجو (با حروف لاتین)" type="text" class="form-control" name="term">
            </div>
            <div class="form-group">
            <div style="text-align: right;">
			<span style="float: right;"><label for="content">محتوای کمپین</label></span>
            <span style="float: left;"><label for="content">Campaign Content</label></span>
                <input placeholder="محتوای تبلیغی که نمایش داده اید (با حروف لاتین)" type="text" class="form-control" name="content">
            </div>
            <div style="text-align: center">
            <button type="submit" class="btn btn-primary">ساخت یو تی ام - Generate UTM</button>
            </div>
        </form>
    ';

    return $output;
}

// Register the shortcode [fanabyte_utm_generator]
add_shortcode('fanabyte_utm_generator', 'generate_utm_form');

function fanabyte_utm_generator_menu() {
    $plugin_dir = plugin_dir_url(__FILE__);
    $icon_url = $plugin_dir . 'assets/images/fanabyte-plugins-icon.png';
    if ( get_locale() === 'fa_IR') {
    add_menu_page(
        'لینک یو تی ام',
        'لینک یو تی ام',
        'manage_options',
        'fanabyte_utm_generator_home',
        'fanabyte_utm_generator_home_callback',
        $icon_url,
        42
    );

    add_submenu_page(
        'fanabyte_utm_generator_home',
        'آکادمی فنابایت',
        'آکادمی فنابایت',
        'manage_options',
        'fanabyte_utm_generator_fanabyte',
        'fanabyte_utm_generator_fanabyte_callback'
    );
    } else {
    add_menu_page(
        'UTM Link',
        'UTM Link',
        'manage_options',
        'fanabyte_utm_generator_home',
        'fanabyte_utm_generator_home_callback',
        $icon_url,
        42
    );

    add_submenu_page(
        'fanabyte_utm_generator_home',
        'FanaByte Academy',
        'FanaByte Academy',
        'manage_options',
        'fanabyte_utm_generator_fanabyte',
        'fanabyte_utm_generator_fanabyte_callback'
    );
    }
    
}

add_action('admin_menu', 'fanabyte_utm_generator_menu');

function fanabyte_utm_generator_home_callback() {

if (isset($_POST['submit-fa'])) {
    $page_title = 'ساخت لینک یو تی ام';
    $args = array(
        'post_type' => 'page', // نوع پست برای برگه‌ها
        'post_status' => 'publish', // وضعیت منتشر بودن
        'posts_per_page' => 1, // تعداد برگه‌های بازیابی شده (در اینجا فقط یک برگه)
        'title' => $page_title // عنوان برگه
    );

    $query = new WP_Query($args);

    if (!$query->have_posts()) {
        // اگر برگه مورد نظر وجود نداشت، برگه جدید ایجاد می‌شود
        $new_page = array(
            'post_title' => $page_title,
            'post_content' => '
                <p>فرم ساخت لینک یو تی ام</p>
                <p>[fanabyte_utm_generator]</p>
            ',
            'post_status' => 'publish',
            'post_type' => 'page'
        );

        $page_id = wp_insert_post($new_page); // درج برگه جدید

        if ($page_id) {
            // ایجاد لینک یکتا برای برگه
            $page_slug = 'fanabyte-utm-generator';
            wp_update_post(array(
                'ID' => $page_id,
                'post_name' => $page_slug
            ));

            // ایجاد کد کوتاه برای استفاده در متن
            $shortcode = '[fanabyte_utm_generator]';
            update_post_meta($page_id, '_wp_page_template', $shortcode);

            // نمایش پیغام موفقیت‌آمیز
            echo '<div class="updated"><p>برگه با موفقیت ایجاد شد.</p></div>';
        } else {
            // نمایش خطا در صورت مشکل در ایجاد برگه
            echo '<div class="error"><p>خطا در ایجاد برگه رخ داده است.</p></div>';
        }
    } else {
        // نمایش پیغام در صورت وجود برگه
        echo '<div id="message" class="error"><p>برگه از قبل وجود دارد.</p></div>';
    }
}


if (isset($_POST['submit-en'])) {
    $page_title = 'Generate UTM link';
    $args = array(
        'post_type' => 'page', // نوع پست برای برگه‌ها
        'post_status' => 'publish', // وضعیت منتشر بودن
        'posts_per_page' => 1, // تعداد برگه‌های بازیابی شده (در اینجا فقط یک برگه)
        'title' => $page_title // عنوان برگه
    );

    $query = new WP_Query($args);

    if (!$query->have_posts()) {
        // اگر برگه مورد نظر وجود نداشت، برگه جدید ایجاد می‌شود
        $new_page = array(
            'post_title' => $page_title,
            'post_content' => '
                <p>Generate UTM Link Form</p>
                <p>[fanabyte_utm_generator]</p>
            ',
            'post_status' => 'publish',
            'post_type' => 'page'
        );

        $page_id = wp_insert_post($new_page); // درج برگه جدید

        if ($page_id) {
            // ایجاد لینک یکتا برای برگه
            $page_slug = 'fanabyte-utm-generator';
            wp_update_post(array(
                'ID' => $page_id,
                'post_name' => $page_slug
            ));

            // ایجاد کد کوتاه برای استفاده در متن
            $shortcode = '[fanabyte_utm_generator]';
            update_post_meta($page_id, '_wp_page_template', $shortcode);

            // نمایش پیغام موفقیت‌آمیز
            echo '<div class="updated"><p>Page created successfully.</p></div>';
        } else {
            // نمایش خطا در صورت مشکل در ایجاد برگه
            echo '<div class="error"><p>An error occurred while creating the Page.</p></div>';
        }
    } else {
        // نمایش پیغام در صورت وجود برگه
        echo '<div id="message" class="error"><p>The Page already exists.</p></div>';
    }
}


echo '<div class="bee-page-container">
<div>
<div style="padding: 15px;max-width: 1000px;margin: 0 auto;display: flex;background-color: white;border-radius: 5px;margin-top: 20px;border: 1px solid #ccc;">
<div style="padding-bottom: 5px; padding-top: 5px; flex-basis: 100%;}">';

    if ( get_locale() === 'fa_IR') {
echo '<div>
<h1 style="color:#7747FF;direction:ltr;font-size:38px;font-weight:700;letter-spacing:normal;line-height:120%;text-align:right;margin-top:0;margin-bottom:0;"><span class="tinyMce-placeholder">ساخت لینک یو تی ام</span> </h1>
</div>
<div>
<p style=" text-align: right; direction: rtl;padding: 10px;">برای ساخت لینک یو تی ام شما باید کد کوتاه زیر را در یک برگه قرار دهید و سپس با نمایش برگه میتوانید فرم ساخت لینک یو تی ام فنابایت را مشاهده کنید و از آن استفاده کنید.<br/><br/>کد کوتاه برای قرار دادن در برگه یا نوشته:</p>
</div>
<div style="text-align: center;padding: 10px;"><b>[fanabyte_utm_generator]</b></div>
<div>
<div style="border-top:1px solid #dddddd;width:100%;"></div>
</div>
<div>
<p style="padding: 10px; text-align: right; direction: rtl;0">البته شما به راحتی میتوانید با کلیک بر روی دکمه زیر به صورت اتوماتیک برگه برگه مورد نظر را با تمامی تنظیمات بسازید.</p>
</div>
<div style="padding: 10px; text-align: center;">

<form method="post">
    <button style=" background-color: #007bff; color: #fff; border: 0; border-radius: 5px; padding: 5px 15px; line-height: 25px; cursor: pointer;" type="submit" name="submit-fa">ایجاد برگه حاوی فرم ساخت لینک یو تی ام</button>
</form>

</div>';} else {
echo '<div>
<h1 style="color:#7747FF;direction:ltr;font-size:38px;font-weight:700;letter-spacing:normal;line-height:120%;text-align:left;margin-top:0;margin-bottom:0;">Generate UTM link</h1>
</div>
<div>
<p style="padding: 10px; text-align: left; direction: ltr;">To create a UTM link, you must put the following short code in a page, and then by displaying the page, you can see the UTM link creation form and use it.<br/><br/>Short code to insert in post or page:</p>
</div>
<div style="padding: 10px;text-align: center;"><b>[fanabyte_utm_generator]</b></div>
<div>
<div style="padding: 10px;border-top:1px solid #dddddd;width:100%;"></div>
</div>
<div>
<p style="padding: 10px; text-align: left; direction: ltr;">You can easily create the desired page with all the settings automatically by clicking the button below.</p>
</div>
<div style="padding: 10px;text-align: center;">

<form method="post">
    <button style=" background-color: #007bff; color: #fff; border: 0; border-radius: 5px; padding: 5px 15px; line-height: 25px; cursor: pointer;" type="submit" name="submit-en">Creating a Page containing the UTM link creation form</button>
</form>
</div>';}

echo '</div>
</div>
</div>
</div>';
}

function fanabyte_utm_generator_fanabyte_callback() {
    $plugin_dir = plugin_dir_url(__FILE__);
    $logo_url = $plugin_dir . 'assets/images/fanabyte-logo.png';

    echo '<div class="bee-page-container">
    <div>
    <div style="padding: 15px;max-width: 1000px;margin: 0 auto;display: flex;background-color: white;border-radius: 5px;margin-top: 20px;border: 1px solid #ccc;">
    <div style="padding-bottom: 5px; padding-top: 5px; flex-basis: 100%;">
    <div>';

    $icon_youtube = $plugin_dir . 'assets/images/YouTube.png';
    $icon_email = $plugin_dir . 'assets/images/Email.png';
    $icon_facebook = $plugin_dir . 'assets/images/Facebook.png';
    $icon_github = $plugin_dir . 'assets/images/GitHub.png';
    $icon_instagram = $plugin_dir . 'assets/images/Instagram.png';
    $icon_linkedin = $plugin_dir . 'assets/images/LinkedIn.png';
    $icon_telegram = $plugin_dir . 'assets/images/Telegram.png';
    $icon_threads = $plugin_dir . 'assets/images/Threads.png';
    $icon_x = $plugin_dir . 'assets/images/X.png';
    $icon_pintrest = $plugin_dir . 'assets/images/Pinterest.png';

    if ( get_locale() === 'fa_IR') {
        echo '<h1 style="color:#7747FF;direction:ltr;font-size:38px;font-weight:700;letter-spacing:normal;line-height:120%;text-align:right;margin-top:0;margin-bottom:0;"><span class="tinyMce-placeholder">آکادمی فنابایت</span> </h1>
        </div>
        <div style="padding: 30px;position: relative;">
        <div style="position: relative;display: inline-block;width: 55%;margin-left: 2%;margin-right: 2%;white-space-collapse: preserve-breaks;">
            <h3>داستان فنابایت</h3>
            <p style="text-align: justify;">از سال ۱۴۰۰ شروع به‌کار کردیم البته دقیق‌تر از سال ۱۳۹۰ اون اوایل وبسایت رسمی نداشتیم، هدف فنابایت کمک به همه افراد برای ساخت یک کسب‌و‌کار اینترنتی موفق است. اما چطور؟ ما در فنابایت نحوه راه اندازی و استفاده از بهترین سایت‌ساز دنیا را که بیش از ۴۰ درصد وب‌سایت های دنیا از آن استفاده می‌کنند را به شما می‌آموزیم. هم‌چنین کلی مقاله و ویدئو های آموزشی رایگان و یکسری پکیج های آموزشی در حوزه های مختلف منتشر کرده‌ایم، که میتوانید از آنها در جهت افزایش علم و توسعه کسب و کار خود استفاده کنید.</p>
            </div>
        <div style="position: relative;display: inline-block;width: 35%;margin-left: 2%;margin-right: 2%;">
            <p style="text-align: center;"><img src="';echo $logo_url; echo '" alt="آکادمی فنابایت - FanaByte Academy" style="height: 200px;"></p>
        </div>
        </div>

        <div style="background-color: #f0f0f1;padding: 10px;position: relative;border-radius: 3px;text-align: center;">
        <div style="position: relative;display: inline-block;">';
        echo '<p style="text-align: center;"><b>ما را دنبال کنید!</b></p>';
        echo '<p style="text-align: center;"><b>لطفا ما را در شبکه‌های اجتماعی دنبال کنید:</b></p>';
        echo '<p style="text-align: center;">';
        echo '<a href="https://youtube.com/@fanabyte" target="_blank"><img src="';echo $icon_youtube; echo'" alt="FanaByte Youtube" style="height: 30px;"></a> ';
        echo '<a href="https://instagram.com/fanabyte" target="_blank"><img src="';echo $icon_instagram; echo'" alt="FanaByte Instagram" style="height: 30px;"></a> ';
        echo '<a href="https://threads.net/@fanabyte" target="_blank"><img src="';echo $icon_threads; echo'" alt="FanaByte Threads" style="height: 30px;"></a> ';
        echo '<a href="https://facebook.com/fanabyte" target="_blank"><img src="';echo $icon_facebook; echo'" alt="FanaByte Facebook" style="height: 30px;"></a> ';
        echo '<a href="https://github.com/fanabyte" target="_blank"><img src="';echo $icon_github; echo'" alt="FanaByte GitHub" style="height: 30px;"></a> ';
        echo '<a href="https://www.linkedin.com/company/fanabyte" target="_blank"><img src="';echo $icon_linkedin; echo'" alt="FanaByte LinkedIn" style="height: 30px;"></a> ';
        echo '<a href="https://twitter.com/fanabyte" target="_blank"><img src="';echo $icon_x; echo'" alt="FanaByte X (Twitter)" style="height: 30px;"></a> ';
        echo '<a href="https://t.me/fanabyte" target="_blank"><img src="';echo $icon_telegram; echo'" alt="FanaByte Telegram" style="height: 30px;"></a> ';
        echo '<a href="https://www.pinterest.com/fanabyte" target="_blank"><img src="';echo $icon_pintrest; echo'" alt="FanaByte Pintrest" style="height: 30px;"></a> ';
        echo '<a href="mailto:info@fanabyte.com" target="_blank"><img src="';echo $icon_email; echo'" alt="FanaByte Email" style="height: 30px;"></a> ';
        echo '</p>';
        echo '<p style="text-align: center;"><b>☕ اگر از این افزونه لذت می‌برید و برای شما مفید است، لطفاً با خرید یک قهوه از توسعه آن حمایت کنید. حمایت شما برای ما بسیار ارزشمند است!</b></p>';
        echo '<p style="text-align: center;"><b><a style="text-decoration:none;" href="https://www.coffeete.ir/fanabyte" target="_blank" title="حمایت از فنابایت در کافیته">حمایت از فنابایت در کافیته</a></b></p>';
        echo '<p style="text-align: center;"><b><a style="text-decoration:none;" href="https://fanabyte.com/" target="_blank" title="وبسایت آکادمی فنابایت">مشاهده وب‌سایت آکادمی فنابایت</a></b></p>';
        echo '</p></p>
        </div>';
        echo'</div>
        ';} else {
        echo '<h1 style="color:#7747FF;direction:ltr;font-size:38px;font-weight:700;letter-spacing:normal;line-height:120%;text-align:left;margin-top:0;margin-bottom:0;"><span class="tinyMce-placeholder">FanaByte Academy</span> </h1>
        </div>
        <div style="padding: 30px;position: relative;">
        <div style="position: relative;display: inline-block;width: 55%;margin-left: 2%;margin-right: 2%;white-space-collapse: preserve-breaks;">
            <h3>FanaByte story</h3>
            <p style="text-align: justify;">We started working since 2001, although we did not have an official website since 2011, FanaByte goal is to help everyone build a successful internet business. But how? At FanaByte we will teach you how to set up and use the world best website builder, which is used by more than 40% of the world websites. Also, we have published a lot of free educational articles and videos and a series of educational packages in different fields, which you can use to increase your knowledge and develop your business.</p>
            </div>
        <div style="position: relative;display: inline-block;width: 35%;margin-left: 2%;margin-right: 2%;">
            <p style="text-align: center;"><img src="';echo $logo_url; echo '" alt="آکادمی فنابایت - FanaByte Academy" style="height: 200px;"></p>
        </div>
        </div>

        <div style="background-color: #f0f0f1;padding: 10px;position: relative;border-radius: 3px;text-align: center;">
        <div style="position: relative;display: inline-block;">';
        echo '<p style="text-align: center;"><b>Follow us!</b></p>';
        echo '<p style="text-align: center;"><b>Please follow us on social networks:</b></p>';
        echo '<p style="text-align: center;">';
        echo '<a href="https://youtube.com/@fanabyte" target="_blank"><img src="';echo $icon_youtube; echo'" alt="FanaByte Youtube" style="height: 30px;"></a> ';
        echo '<a href="https://instagram.com/fanabyte" target="_blank"><img src="';echo $icon_instagram; echo'" alt="FanaByte Instagram" style="height: 30px;"></a> ';
        echo '<a href="https://threads.net/@fanabyte" target="_blank"><img src="';echo $icon_threads; echo'" alt="FanaByte Threads" style="height: 30px;"></a> ';
        echo '<a href="https://facebook.com/fanabyte" target="_blank"><img src="';echo $icon_facebook; echo'" alt="FanaByte Facebook" style="height: 30px;"></a> ';
        echo '<a href="https://github.com/fanabyte" target="_blank"><img src="';echo $icon_github; echo'" alt="FanaByte GitHub" style="height: 30px;"></a> ';
        echo '<a href="https://www.linkedin.com/company/fanabyte" target="_blank"><img src="';echo $icon_linkedin; echo'" alt="FanaByte LinkedIn" style="height: 30px;"></a> ';
        echo '<a href="https://twitter.com/fanabyte" target="_blank"><img src="';echo $icon_x; echo'" alt="FanaByte X (Twitter)" style="height: 30px;"></a> ';
        echo '<a href="https://t.me/fanabyte" target="_blank"><img src="';echo $icon_telegram; echo'" alt="FanaByte Telegram" style="height: 30px;"></a> ';
        echo '<a href="https://www.pinterest.com/fanabyte" target="_blank"><img src="';echo $icon_pintrest; echo'" alt="FanaByte Pintrest" style="height: 30px;"></a> ';
        echo '<a href="mailto:info@fanabyte.com" target="_blank"><img src="';echo $icon_email; echo'" alt="FanaByte Email" style="height: 30px;"></a> ';
        echo '</p>';
        echo '<p style="text-align: center;"><b>☕ If you enjoy this plugin and find it useful, please support its development by buying us a coffee. Your support is greatly appreciated!</b></p>';
        echo '<p style="text-align: center;"><b><a style="text-decoration:none;" href="https://www.coffeete.ir/fanabyte" target="_blank" title="Support FanaByte on Coffeete">Support FanaByte on Coffeete</a></b></p>';
        echo '<p style="text-align: center;"><b><a style="text-decoration:none;" href="https://fanabyte.com/" target="_blank" title="FanaByte Academy Website">Visit FanaByte Academy Website</a></b></p>';
        echo '</p></p>
        </div>';
    }

    echo '</div>
    </div>
    </div>
    </div>';
}

?>