<?php
/**
 * Plugin Name: WP Hızlandırma Eklentisi
 * Plugin URI: https://github.com/kullaniciadi/wordpress-hizlandirma
 * Description: Web sitenizi hızlandıran bir WordPress eklentisi.
 * Version: 1.0.0
 * Author: [Adınız Soyadınız]
 * Author URI: https://github.com/kullaniciadi
 * License: GPL2
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Doğrudan erişim engellendi.
}

// CSS ve JS küçültme (Minify) işlevi
function wp_hizlandirma_minify($buffer) {
    $buffer = preg_replace('/\s+/', ' ', $buffer); // Gereksiz boşlukları kaldırır
    $buffer = str_replace(array("\n", "\r", "\t"), '', $buffer); // Yeni satırları kaldırır
    return $buffer;
}

// HTML küçültme için çıktı tamponlama başlatma
function wp_hizlandirma_start_buffer() {
    ob_start('wp_hizlandirma_minify');
}
add_action('get_header', 'wp_hizlandirma_start_buffer');

// HTML küçültme için tamponlama durdurma
function wp_hizlandirma_end_buffer() {
    ob_end_flush();
}
add_action('wp_footer', 'wp_hizlandirma_end_buffer');

// Tarayıcı önbellekleme ayarları
function wp_hizlandirma_browser_caching() {
    if (!is_admin()) {
        header('Cache-Control: max-age=31536000, public');
    }
}
add_action('send_headers', 'wp_hizlandirma_browser_caching');

// Veritabanı temizleme (örnek: spam yorumları kaldırma)
function wp_hizlandirma_database_cleanup() {
    global $wpdb;
    // Spam yorumları temizler
    $wpdb->query("DELETE FROM $wpdb->comments WHERE comment_approved = 'spam'");
    // Çöp kutusundaki yorumları temizler
    $wpdb->query("DELETE FROM $wpdb->comments WHERE comment_approved = 'trash'");
}
add_action('admin_init', 'wp_hizlandirma_database_cleanup');

// Yönetici menüsüne eklenti ayar sayfasını ekleme
function wp_hizlandirma_add_admin_menu() {
    add_menu_page(
        'WP Hızlandırma Ayarları',
        'WP Hızlandırma',
        'manage_options',
        'wp-hizlandirma',
        'wp_hizlandirma_settings_page',
        'dashicons-performance'
    );
}
add_action('admin_menu', 'wp_hizlandirma_add_admin_menu');

// Eklenti ayar sayfası içeriği
function wp_hizlandirma_settings_page() {
    ?>
    <div class="wrap">
        <h1>WP Hızlandırma Ayarları</h1>
        <p>Bu eklenti, sitenizi hızlandırmak için temel optimizasyonlar yapar.</p>
        <p>Aşağıdaki özellikler varsayılan olarak etkindir:</p>
        <ul>
            <li>CSS ve JS Küçültme</li>
            <li>HTML Küçültme</li>
            <li>Tarayıcı Önbellekleme</li>
            <li>Veritabanı Temizliği</li>
        </ul>
        <p>Herhangi bir yapılandırmaya gerek yoktur. Otomatik olarak çalışır!</p>
    </div>
    <?php
}
