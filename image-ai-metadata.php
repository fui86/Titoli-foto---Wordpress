<?php
/**
 * Plugin Name: Image AI Metadata
 * Plugin URI: https://github.com/fui86/Titoli-foto---Wordpress
 * Description: Riconosce automaticamente il contenuto delle immagini usando AI e compila i campi Testo alternativo, Titolo, Didascalia e Descrizione.
 * Version: 1.0.0
 * Author: fui86
 * Text Domain: image-ai-metadata
 * Domain Path: /languages
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('IMAGE_AI_METADATA_VERSION', '1.0.0');
define('IMAGE_AI_METADATA_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('IMAGE_AI_METADATA_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Main plugin class
 */
class Image_AI_Metadata {
    
    /**
     * Instance of this class
     */
    private static $instance = null;
    
    /**
     * Get the singleton instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->init_hooks();
    }
    
    /**
     * Initialize WordPress hooks
     */
    private function init_hooks() {
        // Load text domain for translations
        add_action('plugins_loaded', array($this, 'load_textdomain'));
        
        // Add settings page
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        
        // Process images on upload
        add_filter('add_attachment', array($this, 'process_new_image'), 10, 1);
        
        // Add meta box to media edit page
        add_action('add_meta_boxes_attachment', array($this, 'add_meta_box'));
        
        // Handle manual processing
        add_action('admin_post_image_ai_metadata_process', array($this, 'handle_manual_process'));
        
        // Add admin styles
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles'));
    }
    
    /**
     * Load plugin text domain
     */
    public function load_textdomain() {
        load_plugin_textdomain('image-ai-metadata', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_options_page(
            __('Image AI Metadata Settings', 'image-ai-metadata'),
            __('Image AI Metadata', 'image-ai-metadata'),
            'manage_options',
            'image-ai-metadata',
            array($this, 'render_settings_page')
        );
    }
    
    /**
     * Register settings
     */
    public function register_settings() {
        register_setting('image_ai_metadata_options', 'image_ai_metadata_api_token');
        register_setting('image_ai_metadata_options', 'image_ai_metadata_auto_process');
        register_setting('image_ai_metadata_options', 'image_ai_metadata_api_endpoint');
        
        add_settings_section(
            'image_ai_metadata_main',
            __('Configurazione API', 'image-ai-metadata'),
            array($this, 'render_settings_section'),
            'image-ai-metadata'
        );
        
        add_settings_field(
            'image_ai_metadata_api_token',
            __('Hugging Face API Token', 'image-ai-metadata'),
            array($this, 'render_api_token_field'),
            'image-ai-metadata',
            'image_ai_metadata_main'
        );
        
        add_settings_field(
            'image_ai_metadata_api_endpoint',
            __('Endpoint API (opzionale)', 'image-ai-metadata'),
            array($this, 'render_api_endpoint_field'),
            'image-ai-metadata',
            'image_ai_metadata_main'
        );
        
        add_settings_field(
            'image_ai_metadata_auto_process',
            __('Elaborazione automatica', 'image-ai-metadata'),
            array($this, 'render_auto_process_field'),
            'image-ai-metadata',
            'image_ai_metadata_main'
        );
    }
    
    /**
     * Render settings section
     */
    public function render_settings_section() {
        echo '<p>' . __('Configura le impostazioni per il riconoscimento automatico delle immagini tramite AI.', 'image-ai-metadata') . '</p>';
        echo '<p>' . sprintf(
            __('Ottieni un token API gratuito da <a href="%s" target="_blank">Hugging Face</a>.', 'image-ai-metadata'),
            'https://huggingface.co/settings/tokens'
        ) . '</p>';
    }
    
    /**
     * Render API token field
     */
    public function render_api_token_field() {
        $value = get_option('image_ai_metadata_api_token', '');
        echo '<input type="text" name="image_ai_metadata_api_token" value="' . esc_attr($value) . '" size="50" />';
        echo '<p class="description">' . __('Il token API di Hugging Face per accedere al servizio di riconoscimento immagini.', 'image-ai-metadata') . '</p>';
    }
    
    /**
     * Render API endpoint field
     */
    public function render_api_endpoint_field() {
        $value = get_option('image_ai_metadata_api_endpoint', 'https://api-inference.huggingface.co/models/Salesforce/blip-image-captioning-large');
        echo '<input type="text" name="image_ai_metadata_api_endpoint" value="' . esc_attr($value) . '" size="80" />';
        echo '<p class="description">' . __('Endpoint API per il modello di riconoscimento immagini (predefinito: BLIP Image Captioning).', 'image-ai-metadata') . '</p>';
    }
    
    /**
     * Render auto process field
     */
    public function render_auto_process_field() {
        $value = get_option('image_ai_metadata_auto_process', '1');
        echo '<label><input type="checkbox" name="image_ai_metadata_auto_process" value="1" ' . checked($value, '1', false) . ' />';
        echo ' ' . __('Elabora automaticamente le immagini al caricamento', 'image-ai-metadata') . '</label>';
        echo '<p class="description">' . __('Se disabilitato, dovrai elaborare manualmente ogni immagine dalla pagina di modifica.', 'image-ai-metadata') . '</p>';
    }
    
    /**
     * Render settings page
     */
    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        // Show saved message
        if (isset($_GET['settings-updated'])) {
            add_settings_error(
                'image_ai_metadata_messages',
                'image_ai_metadata_message',
                __('Impostazioni salvate', 'image-ai-metadata'),
                'updated'
            );
        }
        
        settings_errors('image_ai_metadata_messages');
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields('image_ai_metadata_options');
                do_settings_sections('image-ai-metadata');
                submit_button(__('Salva impostazioni', 'image-ai-metadata'));
                ?>
            </form>
            
            <hr>
            
            <h2><?php _e('Come funziona', 'image-ai-metadata'); ?></h2>
            <ol>
                <li><?php _e('Registrati gratuitamente su Hugging Face e ottieni un token API', 'image-ai-metadata'); ?></li>
                <li><?php _e('Inserisci il token API nel campo sopra e salva', 'image-ai-metadata'); ?></li>
                <li><?php _e('Carica le tue immagini nella libreria media di WordPress', 'image-ai-metadata'); ?></li>
                <li><?php _e('Il plugin analizzerà automaticamente le immagini e compilerà i metadati', 'image-ai-metadata'); ?></li>
            </ol>
            
            <h2><?php _e('Modelli AI supportati', 'image-ai-metadata'); ?></h2>
            <ul>
                <li><strong>BLIP Image Captioning</strong> (predefinito) - Generazione di descrizioni dettagliate</li>
                <li><strong>ViT GPT2 Image Captioning</strong> - Alternativa per descrizioni più creative</li>
            </ul>
        </div>
        <?php
    }
    
    /**
     * Process new image on upload
     */
    public function process_new_image($attachment_id) {
        // Check if auto-process is enabled
        if (get_option('image_ai_metadata_auto_process', '1') !== '1') {
            return;
        }
        
        // Check if it's an image
        if (!wp_attachment_is_image($attachment_id)) {
            return;
        }
        
        // Process the image
        $this->analyze_and_update_image($attachment_id);
    }
    
    /**
     * Add meta box to media edit page
     */
    public function add_meta_box($post) {
        if (wp_attachment_is_image($post->ID)) {
            add_meta_box(
                'image-ai-metadata-box',
                __('Riconoscimento AI Immagine', 'image-ai-metadata'),
                array($this, 'render_meta_box'),
                'attachment',
                'side',
                'default'
            );
        }
    }
    
    /**
     * Render meta box
     */
    public function render_meta_box($post) {
        wp_nonce_field('image_ai_metadata_process', 'image_ai_metadata_nonce');
        
        $last_processed = get_post_meta($post->ID, '_image_ai_metadata_processed', true);
        
        if ($last_processed) {
            echo '<p>' . sprintf(
                __('Ultima elaborazione: %s', 'image-ai-metadata'),
                date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $last_processed)
            ) . '</p>';
        }
        
        echo '<p>';
        echo '<button type="button" class="button button-primary" onclick="document.getElementById(\'image-ai-metadata-form\').submit();">';
        echo __('Rielabora con AI', 'image-ai-metadata');
        echo '</button>';
        echo '</p>';
        
        echo '<form id="image-ai-metadata-form" method="post" action="' . admin_url('admin-post.php') . '" style="display:none;">';
        echo '<input type="hidden" name="action" value="image_ai_metadata_process" />';
        echo '<input type="hidden" name="attachment_id" value="' . $post->ID . '" />';
        wp_nonce_field('image_ai_metadata_process', 'image_ai_metadata_nonce');
        echo '</form>';
        
        echo '<p class="description">' . __('Usa questo pulsante per rielaborare l\'immagine con l\'AI e aggiornare i metadati.', 'image-ai-metadata') . '</p>';
    }
    
    /**
     * Handle manual processing request
     */
    public function handle_manual_process() {
        // Verify nonce
        if (!isset($_POST['image_ai_metadata_nonce']) || !wp_verify_nonce($_POST['image_ai_metadata_nonce'], 'image_ai_metadata_process')) {
            wp_die(__('Errore di sicurezza', 'image-ai-metadata'));
        }
        
        // Check permissions
        if (!current_user_can('upload_files')) {
            wp_die(__('Non hai i permessi per eseguire questa azione', 'image-ai-metadata'));
        }
        
        $attachment_id = isset($_POST['attachment_id']) ? intval($_POST['attachment_id']) : 0;
        
        if ($attachment_id && wp_attachment_is_image($attachment_id)) {
            $result = $this->analyze_and_update_image($attachment_id);
            
            if (is_wp_error($result)) {
                wp_redirect(add_query_arg(array(
                    'post' => $attachment_id,
                    'action' => 'edit',
                    'image_ai_error' => urlencode($result->get_error_message())
                ), admin_url('post.php')));
            } else {
                wp_redirect(add_query_arg(array(
                    'post' => $attachment_id,
                    'action' => 'edit',
                    'image_ai_success' => '1'
                ), admin_url('post.php')));
            }
        } else {
            wp_die(__('ID allegato non valido', 'image-ai-metadata'));
        }
        
        exit;
    }
    
    /**
     * Analyze image using AI and update metadata
     */
    private function analyze_and_update_image($attachment_id) {
        $api_token = get_option('image_ai_metadata_api_token');
        
        if (empty($api_token)) {
            return new WP_Error('no_api_token', __('Token API non configurato. Configura il plugin nelle impostazioni.', 'image-ai-metadata'));
        }
        
        // Get image file path
        $image_path = get_attached_file($attachment_id);
        
        if (!file_exists($image_path)) {
            return new WP_Error('file_not_found', __('File immagine non trovato.', 'image-ai-metadata'));
        }
        
        // Call AI API
        $description = $this->call_ai_api($image_path, $api_token);
        
        if (is_wp_error($description)) {
            return $description;
        }
        
        // Update image metadata
        $this->update_image_metadata($attachment_id, $description);
        
        // Mark as processed
        update_post_meta($attachment_id, '_image_ai_metadata_processed', time());
        
        return true;
    }
    
    /**
     * Call AI API to analyze image
     */
    private function call_ai_api($image_path, $api_token) {
        $endpoint = get_option('image_ai_metadata_api_endpoint', 'https://api-inference.huggingface.co/models/Salesforce/blip-image-captioning-large');
        
        // Read image file
        $image_data = file_get_contents($image_path);
        
        if ($image_data === false) {
            return new WP_Error('read_error', __('Errore nella lettura del file immagine.', 'image-ai-metadata'));
        }
        
        // Prepare API request
        $response = wp_remote_post($endpoint, array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $api_token,
                'Content-Type' => 'application/octet-stream'
            ),
            'body' => $image_data,
            'timeout' => 30
        ));
        
        if (is_wp_error($response)) {
            return new WP_Error('api_error', sprintf(
                __('Errore nella chiamata API: %s', 'image-ai-metadata'),
                $response->get_error_message()
            ));
        }
        
        $response_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        
        if ($response_code !== 200) {
            return new WP_Error('api_error', sprintf(
                __('Errore API (codice %d): %s', 'image-ai-metadata'),
                $response_code,
                $body
            ));
        }
        
        $data = json_decode($body, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return new WP_Error('json_error', __('Errore nella decodifica della risposta JSON.', 'image-ai-metadata'));
        }
        
        // Extract description from response
        if (isset($data[0]['generated_text'])) {
            return sanitize_text_field($data[0]['generated_text']);
        }
        
        return new WP_Error('invalid_response', __('Risposta API non valida.', 'image-ai-metadata'));
    }
    
    /**
     * Update image metadata with AI description
     */
    private function update_image_metadata($attachment_id, $description) {
        // Capitalize first letter for title
        $title = ucfirst($description);
        
        // Update post data
        wp_update_post(array(
            'ID' => $attachment_id,
            'post_title' => $title,
            'post_excerpt' => $description, // Caption
            'post_content' => $description  // Description
        ));
        
        // Update alt text
        update_post_meta($attachment_id, '_wp_attachment_image_alt', $description);
    }
    
    /**
     * Enqueue admin styles
     */
    public function enqueue_admin_styles($hook) {
        if ($hook === 'post.php' || $hook === 'upload.php') {
            // Add admin notice for success/error messages
            add_action('admin_notices', array($this, 'show_admin_notices'));
        }
    }
    
    /**
     * Show admin notices
     */
    public function show_admin_notices() {
        if (isset($_GET['image_ai_success'])) {
            echo '<div class="notice notice-success is-dismissible">';
            echo '<p>' . __('Immagine elaborata con successo! I metadati sono stati aggiornati.', 'image-ai-metadata') . '</p>';
            echo '</div>';
        }
        
        if (isset($_GET['image_ai_error'])) {
            echo '<div class="notice notice-error is-dismissible">';
            echo '<p>' . sprintf(__('Errore: %s', 'image-ai-metadata'), urldecode($_GET['image_ai_error'])) . '</p>';
            echo '</div>';
        }
    }
}

// Initialize the plugin
function image_ai_metadata_init() {
    return Image_AI_Metadata::get_instance();
}

add_action('plugins_loaded', 'image_ai_metadata_init');
